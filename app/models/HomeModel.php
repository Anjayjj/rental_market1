<?php
class HomeModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Mengambil 8 barang terbaru untuk di halaman depan
    public function getLatestItems($limit = 8) {
        $query = "SELECT i.*, c.name as category_name,
                  (SELECT image_path FROM item_images WHERE item_id = i.id AND is_primary = 1 LIMIT 1) as cover_image,
                  (SELECT ROUND(AVG(rating),1) FROM reviews WHERE item_id = i.id) as avg_rating,
                  (SELECT COUNT(id) FROM reviews WHERE item_id = i.id) as total_reviews
                  FROM items i
                  JOIN categories c ON i.category_id = c.id
                  WHERE i.status = 'active'
                  ORDER BY avg_rating DESC, total_reviews DESC
                  LIMIT :limit";

        $this->db->query($query);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Mengambil barang selain yang sudah tampil di bagian "Populer" (Barang Lainnya)
    public function getOtherItems($exclude_ids = [], $limit = 8) {
        $query = "SELECT i.*, c.name as category_name,
                  (SELECT image_path FROM item_images WHERE item_id = i.id AND is_primary = 1 LIMIT 1) as cover_image,
                  (SELECT ROUND(AVG(rating),1) FROM reviews WHERE item_id = i.id) as avg_rating,
                  (SELECT COUNT(id) FROM reviews WHERE item_id = i.id) as total_reviews
                  FROM items i
                  JOIN categories c ON i.category_id = c.id
                  WHERE i.status = 'active'";
        if (!empty($exclude_ids)) {
            $ids = implode(',', array_map(function($x){ return (int)$x; }, $exclude_ids));
            $query .= " AND i.id NOT IN (" . $ids . ")";
        }
        $query .= " ORDER BY RAND() LIMIT :limit";
        $this->db->query($query);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Mengambil kategori untuk dropdown dan badge
    public function getCategories() {
        $this->db->query("SELECT * FROM categories ORDER BY id ASC");
        return $this->db->resultSet();
    }

    // Statistik riil dari database (tidak dibesar-besarkan)
    public function getStats() {
        $stats = ['items' => 0, 'users' => 0, 'categories' => 0, 'bookings_done' => 0];

        $this->db->query("SELECT COUNT(*) AS total FROM items WHERE status = 'active'");
        $row = $this->db->single();
        $stats['items'] = (int)($row['total'] ?? 0);

        $this->db->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
        $row = $this->db->single();
        $stats['users'] = (int)($row['total'] ?? 0);

        $this->db->query("SELECT COUNT(*) AS total FROM categories");
        $row = $this->db->single();
        $stats['categories'] = (int)($row['total'] ?? 0);

        $this->db->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'completed'");
        $row = $this->db->single();
        $stats['bookings_done'] = (int)($row['total'] ?? 0);

        return $stats;
    }

    // Logika Pencarian Kompleks (Search & Filter & Sort)
    public function searchItems($keyword = '', $category_id = 0, $sort = 'terbaru') {
        $query = "SELECT i.*, c.name as category_name,
                  (SELECT image_path FROM item_images WHERE item_id = i.id AND is_primary = 1 LIMIT 1) as cover_image,
                  (SELECT ROUND(AVG(rating),1) FROM reviews WHERE item_id = i.id) as avg_rating,
                  (SELECT COUNT(id) FROM reviews WHERE item_id = i.id) as total_reviews
                  FROM items i
                  JOIN categories c ON i.category_id = c.id
                  WHERE i.status = 'active'";

        if (!empty($keyword)) {
            $query .= " AND (i.name LIKE :keyword OR i.description LIKE :keyword)";
        }
        if (!empty($category_id)) {
            $query .= " AND i.category_id = :category_id";
        }

        $orders = [
            'terbaru'  => "i.created_at DESC",
            'murah'    => "i.price_daily ASC",
            'mahal'    => "i.price_daily DESC",
            'nama'     => "i.name ASC"
        ];
        $orderBy = $orders[$sort] ?? $orders['terbaru'];
        $query .= " ORDER BY " . $orderBy;

        $this->db->query($query);

        if (!empty($keyword)) {
            $this->db->bind(':keyword', "%$keyword%");
        }
        if (!empty($category_id)) {
            $this->db->bind(':category_id', $category_id, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }
  // --- Fungsi Prediksi Pencarian (Mencakup Kategori & Barang) ---
  public function getSearchSuggestions($keyword, $limit = 6) {
    $suggestions = [];

    // 1. Cari Kategori yang cocok (Maksimal 2 teratas)
    $queryCat = "SELECT id, name FROM categories WHERE name LIKE :keyword LIMIT 2";
    $this->db->query($queryCat);
    $this->db->bind(':keyword', "%$keyword%");
    $categories = $this->db->resultSet();

    foreach ($categories as $cat) {
        $suggestions[] = [
            'type' => 'category', // Penanda bahwa ini adalah kategori
            'name' => 'Kategori: ' . $cat['name'],
            'url'  => BASEURL . '/home/explore?category=' . $cat['id']
        ];
    }

    // 2. Cari Barang yang cocok (Berdasarkan Nama, Deskripsi, ATAU Nama Kategori)
    $itemLimit = $limit - count($suggestions); // Sisa slot untuk barang
    
    $queryItem = "SELECT i.name, i.slug 
                  FROM items i 
                  JOIN categories c ON i.category_id = c.id 
                  WHERE i.status = 'active' 
                  AND (i.name LIKE :keyword OR i.description LIKE :keyword OR c.name LIKE :keyword)
                  ORDER BY i.name ASC LIMIT :limit";
    
    $this->db->query($queryItem);
    $this->db->bind(':keyword', "%$keyword%");
    $this->db->bind(':limit', (int)$itemLimit, PDO::PARAM_INT);
    $items = $this->db->resultSet();

    foreach ($items as $item) {
        $suggestions[] = [
            'type' => 'item', // Penanda bahwa ini adalah spesifik barang
            'name' => $item['name'],
            'url'  => BASEURL . '/item/detail/' . $item['slug']
        ];
    }

    return $suggestions; // Mengembalikan gabungan kategori dan barang
}
}
?>