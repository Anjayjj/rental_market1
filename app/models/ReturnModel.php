<?php
class ReturnModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Mendapatkan data booking lengkap beserta harga harian barang
    public function getBookingForReturn($booking_id) {
        $query = "SELECT b.*, i.price_daily, i.name as item_name 
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  WHERE b.id = :id AND b.status = 'active'";
        $this->db->query($query);
        $this->db->bind('id', $booking_id);
        return $this->db->single();
    }

    // Mendapatkan data booking untuk ditampilkan di form pengembalian (tanpa batasan status)
    public function getBookingDetail($booking_id) {
        $query = "SELECT b.*, i.name as item_name, u.name as user_name 
                  FROM bookings b
                  JOIN items i ON b.item_id = i.id
                  JOIN users u ON b.user_id = u.id
                  WHERE b.id = :id";
        $this->db->query($query);
        $this->db->bind('id', $booking_id);
        return $this->db->single();
    }

    // Memproses pengembalian
    public function processReturn($data) {
        try {
            $this->db->dbh->beginTransaction();

            // 1. Update status booking menjadi 'completed'
            $queryBooking = "UPDATE bookings SET status = 'completed' WHERE id = :id";
            $this->db->query($queryBooking);
            $this->db->bind('id', $data['booking_id']);
            $this->db->execute();

            // 2. Jika ada denda (total_penalty > 0), masukkan ke tabel penalties
            if ($data['total_penalty'] > 0) {
                $queryPenalty = "INSERT INTO penalties 
                                 (booking_id, late_days, damage_fee, late_fee, total_penalty, description, status) 
                                 VALUES 
                                 (:booking_id, :late_days, :damage_fee, :late_fee, :total_penalty, :description, 'unpaid')";
                $this->db->query($queryPenalty);
                $this->db->bind('booking_id', $data['booking_id']);
                $this->db->bind('late_days', $data['late_days']);
                $this->db->bind('damage_fee', $data['damage_fee']);
                $this->db->bind('late_fee', $data['late_fee']);
                $this->db->bind('total_penalty', $data['total_penalty']);
                $this->db->bind('description', $data['description']);
                $this->db->execute();
            }

            // 3. (Opsional) Update status barang jika ingin dikelola otomatis
            // Jika dikembalikan, ubah status item dari 'rented' kembali ke 'active'
            // Awalnya ketika status booking 'active', status item diubah menjadi 'rented'
            
            $this->db->dbh->commit();
            return true;
        } catch (Exception $e) {
            $this->db->dbh->rollBack();
            return false;
        }
    }
}
?>