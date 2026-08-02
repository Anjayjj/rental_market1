<?php
class AdminModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    } // <--- Konstruktor ditutup dengan benar di sini

    // --- Kategori ---
    public function getCategories($q = '') {
        $sql = "SELECT * FROM categories";
        if ($q !== '') {
            $sql .= " WHERE name LIKE :q";
        }
        $sql .= " ORDER BY name ASC";
        
        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        return $this->db->resultSet();
    }
    public function addCategory($name, $icon, $slug) {
        $this->db->query("INSERT INTO categories (name, icon, slug) VALUES (:name, :icon, :slug)");
        $this->db->bind('name', $name);
        $this->db->bind('icon', $icon);
        $this->db->bind('slug', $slug);
        return $this->db->execute();
    }
    public function updateCategory($id, $name, $icon, $slug) {
        $this->db->query("UPDATE categories SET name=:name, icon=:icon, slug=:slug WHERE id=:id");
        $this->db->bind('name', $name);
        $this->db->bind('icon', $icon);
        $this->db->bind('slug', $slug);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
    public function deleteCategory($id) {
        // Cegah hapus kategori yang masih dipakai
        $this->db->query("SELECT COUNT(*) as c FROM items WHERE category_id = :id");
        $this->db->bind('id', $id);
        $row = $this->db->single();
        if ((int)($row['c'] ?? 0) > 0) {
            return false;
        }
        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    // --- Barang ---
    public function getAllItems($q = '') {
        $sql = "SELECT i.*, c.name as category_name, u.name as owner_name,
                (SELECT image_path FROM item_images WHERE item_id = i.id AND is_primary = 1 LIMIT 1) as cover_image
                FROM items i
                JOIN users u ON i.owner_id = u.id
                JOIN categories c ON i.category_id = c.id";
        if ($q !== '') {
            $sql .= " WHERE i.name LIKE :q OR u.name LIKE :q";
        }
        $sql .= " ORDER BY i.created_at DESC";
        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        return $this->db->resultSet();
    }
    public function updateItemStatus($id, $status) {
        $this->db->query("UPDATE items SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
    public function updateItemCategory($id, $category_id) {
        $this->db->query("UPDATE items SET category_id = :category_id WHERE id = :id");
        $this->db->bind('category_id', $category_id);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
    public function deleteItem($id) {
        $this->db->query("DELETE FROM items WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    // --- Booking ---
    public function updateBookingStatus($id, $status) {
        $this->db->query("UPDATE bookings SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    // --- Review ---
    public function deleteReview($id) {
        $this->db->query("DELETE FROM reviews WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

    // --- Admin Logs ---
    public function logAction($admin_id, $action, $target_type = null, $target_id = null, $note = null) {
        $this->db->query("INSERT INTO admin_logs (admin_id, action, target_type, target_id, note) VALUES (:admin_id, :action, :target_type, :target_id, :note)");
        $this->db->bind('admin_id', $admin_id);
        $this->db->bind('action', $action);
        $this->db->bind('target_type', $target_type);
        $this->db->bind('target_id', $target_id);
        $this->db->bind('note', $note);
        return $this->db->execute();
    }
    public function getLogs($limit = 200, $q = '') {
        $sql = "SELECT l.*, u.name as admin_name 
                FROM admin_logs l 
                JOIN users u ON l.admin_id = u.id";
        if ($q !== '') {
            $sql .= " WHERE u.name LIKE :q OR l.action LIKE :q OR l.target_type LIKE :q";
        }
        $sql .= " ORDER BY l.created_at DESC LIMIT :limit";
        
        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        $this->db->bind('limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // --- Statistik Widget Cards ---
    public function getDashboardStats() {
        $stats = [];
        
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        $stats['total_users'] = $this->db->single()['count'];

        $this->db->query("SELECT COUNT(*) as count FROM items WHERE status != 'inactive'");
        $stats['total_items'] = $this->db->single()['count'];

        $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status IN ('completed', 'active', 'approved')");
        $stats['total_bookings'] = $this->db->single()['count'];

        $this->db->query("SELECT SUM(admin_fee) as total FROM bookings WHERE status IN ('completed', 'active')");
        $stats['total_revenue'] = $this->db->single()['total'] ?? 0;

        return $stats;
    }

    // --- Data untuk Chart.js ---
    public function getRevenueChartData($year = null) {
        if (!$year) $year = date('Y');
        
        $query = "SELECT MONTH(created_at) as month, SUM(admin_fee) as total 
                  FROM bookings 
                  WHERE YEAR(created_at) = :year AND status IN ('completed', 'active')
                  GROUP BY MONTH(created_at)
                  ORDER BY month ASC";
        
        $this->db->query($query);
        $this->db->bind('year', $year);
        $results = $this->db->resultSet();

        $chartData = array_fill(1, 12, 0); 
        
        foreach ($results as $row) {
            $chartData[$row['month']] = (float)$row['total'];
        }

        return array_values($chartData);
    }

    // --- Transaksi ---
    public function getRecentBookings() {
        $query = "SELECT b.invoice_no, b.status, b.grand_total, b.created_at, u.name as user_name 
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.id 
                  ORDER BY b.created_at DESC LIMIT 5";
        $this->db->query($query);
        return $this->db->resultSet();
    }
    public function getAllBookings($q = '') {
        $sql = "SELECT b.*, u.name as user_name, i.name as item_name 
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.id 
                  JOIN items i ON b.item_id = i.id";
        if ($q !== '') {
            $sql .= " WHERE b.invoice_no LIKE :q OR u.name LIKE :q OR i.name LIKE :q";
        }
        $sql .= " ORDER BY b.created_at DESC";
        
        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        return $this->db->resultSet();
    }
    public function getAllUsers($q = '') {
        $sql = "SELECT * FROM users WHERE role = 'user'";
        if ($q !== '') {
            $sql .= " AND (name LIKE :q OR email LIKE :q OR phone LIKE :q)";
        }
        $sql .= " ORDER BY created_at DESC";
        
        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        return $this->db->resultSet();
    }

    // --- Pembayaran ---
    public function getPendingPayments($q = '') {
        $sql = "SELECT p.*, b.invoice_no, b.grand_total, i.name as item_name, u.name as user_name 
                  FROM payments p 
                  JOIN bookings b ON p.booking_id = b.id 
                  JOIN items i ON b.item_id = i.id 
                  JOIN users u ON b.user_id = u.id 
                  WHERE p.status = 'pending'";
        if ($q !== '') {
            $sql .= " AND (b.invoice_no LIKE :q OR u.name LIKE :q OR i.name LIKE :q)";
        }
        $sql .= " ORDER BY p.created_at DESC";

        $this->db->query($sql);
        if ($q !== '') $this->db->bind('q', "%$q%");
        return $this->db->resultSet();
    }
    public function getPaymentById($id) {
        $this->db->query("SELECT * FROM payments WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    // --- Pengguna & Admin ---
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id AND role = 'user'";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }
    public function toggleSuperAdmin($id, $flag) {
        $this->db->query("UPDATE users SET is_super_admin = :flag WHERE id = :id");
        $this->db->bind('flag', $flag ? 1 : 0);
        $this->db->bind('id', $id);
        return $this->db->execute();
    }

} // <--- PENUTUP KELAS UTAMA DITEMPATKAN DI SINI (PALING BAWAH)
?>