<?php
class BookingModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Cek apakah tanggal bentrok dengan booking yang sudah ada (status approved/active)
    public function isDateAvailable($item_id, $start_date, $end_date) {
        // Tambahkan 'handover' agar barang yang sedang proses serah terima tidak bisa disewa orang lain
        $query = "SELECT COUNT(*) as count FROM bookings 
                  WHERE item_id = :item_id 
                  AND status IN ('approved', 'handover', 'active')
                  AND (start_date <= :end_date AND end_date >= :start_date)";
        
        $this->db->query($query);
        $this->db->bind('item_id', $item_id);
        $this->db->bind('start_date', $start_date);
        $this->db->bind('end_date', $end_date);
        
        $result = $this->db->single();
        return $result['count'] == 0; 
    }

    // Simpan data booking
    public function createBooking($data) {
        // 1. Tentukan status otomatis berdasarkan metode pembayaran
        // Jika memilih COD, langsung 'approved'. Jika online, masuk ke 'pending'.
        $status = (isset($data['payment_method']) && $data['payment_method'] === 'cod') ? 'approved' : 'pending';
        
        // 2. Ambil nilai payment method (default 'online')
        $payment_method = $data['payment_method'] ?? 'online';

        // 3. Query dengan tambahan kolom payment_method dan status dinamis
        $query = "INSERT INTO bookings 
                  (invoice_no, item_id, user_id, start_date, end_date, duration, daily_price, total_price, admin_fee, grand_total, payment_method, status) 
                  VALUES 
                  (:invoice_no, :item_id, :user_id, :start_date, :end_date, :duration, :daily_price, :total_price, :admin_fee, :grand_total, :payment_method, :status)";

        $this->db->query($query);
        
        // Binding data standar
        $this->db->bind('invoice_no', $data['invoice_no']);
        $this->db->bind('item_id', $data['item_id']);
        $this->db->bind('user_id', $data['user_id']);
        $this->db->bind('start_date', $data['start_date']);
        $this->db->bind('end_date', $data['end_date']);
        $this->db->bind('duration', $data['duration']);
        $this->db->bind('daily_price', $data['daily_price']);
        $this->db->bind('total_price', $data['total_price']);
        $this->db->bind('admin_fee', $data['admin_fee']);
        $this->db->bind('grand_total', $data['grand_total']);
        
        // Binding data baru (Metode & Status)
        $this->db->bind('payment_method', $payment_method);
        $this->db->bind('status', $status);

        return $this->db->execute();
    }
    public function getBookingsByUser($user_id) {
        $query = "SELECT b.*, 
                         i.name as item_name, 
                         u_owner.name as owner_name, 
                         u_owner.phone as owner_phone,
                         (SELECT COUNT(*) FROM reviews r WHERE r.booking_id = b.id) as is_reviewed
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  JOIN users u_owner ON i.owner_id = u_owner.id
                  WHERE b.user_id = :user_id
                  ORDER BY b.created_at DESC";
                  
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        return $this->db->resultSet();
    }

    // Mengambil satu booking lengkap beserta nama barang (untuk invoice)
    public function getBookingById($id) {
        $query = "SELECT b.*, i.name as item_name 
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  WHERE b.id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        return $this->db->single();
    }
    // --- Fungsi Otomatisasi Status Transaksi ---
    public function autoUpdateStatus() {
        // 1. BATAL OTOMATIS: Pesanan belum dibayar lewat dari 1 hari
        $queryCancel = "UPDATE bookings SET status = 'cancelled' 
                        WHERE status = 'pending' AND created_at < (NOW() - INTERVAL 1 DAY)";
        $this->db->query($queryCancel);
        $this->db->execute();

        // 2. KETERLAMBATAN OTOMATIS (SKENARIO 2): 
        // Jika status masih 'active' tapi hari ini sudah melewati end_date
        $queryOverdue = "UPDATE bookings SET status = 'overdue' 
                         WHERE status = 'active' AND end_date < CURDATE()";
        $this->db->query($queryOverdue);
        $this->db->execute();
    }
    public function cancelBooking($booking_id, $user_id) {
        // Pastikan booking berstatus pending dan milik user yang bersangkutan
        $this->db->query("UPDATE bookings SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status = 'pending'");
        $this->db->bind('id', $booking_id);
        $this->db->bind('user_id', $user_id);
        return $this->db->execute();
    }
    public function getIncomingBookings($owner_id) {
        $query = "SELECT b.*, i.name as item_name, u.name as renter_name, u.phone as renter_phone 
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  JOIN users u ON b.user_id = u.id
                  WHERE i.owner_id = :owner_id
                  ORDER BY b.created_at DESC";
        $this->db->query($query);
        $this->db->bind('owner_id', $owner_id);
        return $this->db->resultSet();
    }
    public function updateStatusBooking($booking_id, $status) {
        // 1. Update status di tabel bookings
        $this->db->query("UPDATE bookings SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $booking_id);
        $bookingUpdated = $this->db->execute(); // Simpan hasil eksekusi ke variabel, JANGAN di-return dulu

        // A. Ambil ID barang (item_id) dari transaksi ini
        $this->db->query("SELECT item_id FROM bookings WHERE id = :id");
        $this->db->bind('id', $booking_id);
        $booking = $this->db->single();
        
        if ($booking) {
            $item_id = $booking['item_id'];
            $item_status = null;

            // B. Tentukan status master barang berdasarkan status transaksi
            if ($status === 'handover' || $status === 'active' || $status === 'in_use' || $status === 'overdue') {
                // Jika sedang proses serah terima, disewa, atau terlambat, barang jadi 'Rented'
                $item_status = 'Rented'; 
            } 
            elseif ($status === 'completed' || $status === 'cancelled' || $status === 'rejected') {
                // Jika transaksi selesai, batal, atau ditolak, barang kembali 'Active' (tersedia)
                $item_status = 'Active'; 
            }

            // C. Eksekusi update ke tabel items jika ada perubahan status
            if ($item_status !== null) {
                $queryItem = "UPDATE items SET status = :item_status WHERE id = :item_id";
                $this->db->query($queryItem);
                $this->db->bind('item_status', $item_status);
                $this->db->bind('item_id', $item_id);
                $this->db->execute();
            }
        }
        
        // Return hasil eksekusi booking di bagian paling akhir
        return $bookingUpdated; 
    }
    public function getAgreementData($booking_id) {
        $query = "SELECT b.*, 
                         i.name as item_name, i.owner_id,
                         u_renter.name as renter_name, u_renter.phone as renter_phone, u_renter.address as renter_address,
                         u_owner.name as owner_name, u_owner.phone as owner_phone, u_owner.address as owner_address
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  JOIN users u_renter ON b.user_id = u_renter.id
                  JOIN users u_owner ON i.owner_id = u_owner.id
                  WHERE b.id = :id";
                  
        $this->db->query($query);
        $this->db->bind('id', $booking_id);
        return $this->db->single();
    }
    // Contoh pembaruan Model Anda
    public function updateStatusToHandover($booking_id, $signature) {
        $query = "UPDATE bookings SET status = 'handover', owner_signature = :signature WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('signature', $signature);
        $this->db->bind('id', $booking_id);
        return $this->db->execute();
    }
    // Fungsi untuk menyimpan TTD Penyewa saat konfirmasi terima barang
    public function updateStatusToActiveWithSignature($booking_id, $signature) {
        $query = "UPDATE bookings SET status = 'active', renter_signature = :signature WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('signature', $signature);
        $this->db->bind('id', $booking_id);
        return $this->db->execute();
    }
    // Menyimpan ulasan ke tabel 'reviews'
    public function saveReview($data) {
        $query = "INSERT INTO reviews (booking_id, item_id, user_id, rating, comment) 
                  VALUES (:booking_id, :item_id, :user_id, :rating, :comment)";
        
        $this->db->query($query);
        $this->db->bind('booking_id', $data['booking_id']);
        $this->db->bind('item_id', $data['item_id']);
        $this->db->bind('user_id', $data['user_id']);
        $this->db->bind('rating', $data['rating']);
        $this->db->bind('comment', $data['comment']);
        
        return $this->db->execute();
    }
}
?>