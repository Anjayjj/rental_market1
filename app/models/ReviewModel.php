<?php
class ReviewModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Mengambil semua review dari sebuah barang beserta data penyewanya
    public function getReviewsByItem($item_id) {
        $query = "SELECT r.*, u.name as user_name, u.avatar as user_avatar 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.item_id = :item_id 
                  ORDER BY r.created_at DESC";
        $this->db->query($query);
        $this->db->bind('item_id', $item_id);
        return $this->db->resultSet();
    }

    // Mengambil rata-rata rating
    public function getAverageRating($item_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews 
                  FROM reviews WHERE item_id = :item_id";
        $this->db->query($query);
        $this->db->bind('item_id', $item_id);
        return $this->db->single();
    }

    // Cek apakah user berhak mereview (Punya booking 'completed' dan belum direview)
    public function checkEligibility($user_id, $item_id, $booking_id) {
        $query = "SELECT id FROM bookings 
                  WHERE id = :booking_id AND user_id = :user_id 
                  AND item_id = :item_id AND status = 'completed' 
                  AND id NOT IN (SELECT booking_id FROM reviews)";
        $this->db->query($query);
        $this->db->bind('booking_id', $booking_id);
        $this->db->bind('user_id', $user_id);
        $this->db->bind('item_id', $item_id);
        return $this->db->single(); // Return true jika ada
    }

    // Simpan Review
    public function storeReview($data) {
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