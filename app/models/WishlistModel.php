<?php
class WishlistModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function checkWishlist($user_id, $item_id) {
        $this->db->query("SELECT id FROM wishlists WHERE user_id = :user_id AND item_id = :item_id");
        $this->db->bind('user_id', $user_id);
        $this->db->bind('item_id', $item_id);
        return $this->db->single();
    }

    // Ambil semua item_id yang di-wishlist user
    public function getItemIdsByUser($user_id) {
        $this->db->query("SELECT item_id FROM wishlists WHERE user_id = :user_id");
        $this->db->bind('user_id', $user_id);
        $rows = $this->db->resultSet();
        return array_column($rows, 'item_id');
    }

    public function toggleWishlist($user_id, $item_id) {
        $check = $this->checkWishlist($user_id, $item_id);
        
        if ($check) {
            // Jika sudah ada, hapus
            $this->db->query("DELETE FROM wishlists WHERE id = :id");
            $this->db->bind('id', $check['id']);
            $this->db->execute();
            return 'removed';
        } else {
            // Jika belum, tambah
            $this->db->query("INSERT INTO wishlists (user_id, item_id) VALUES (:user_id, :item_id)");
            $this->db->bind('user_id', $user_id);
            $this->db->bind('item_id', $item_id);
            $this->db->execute();
            return 'added';
        }
    }
}
?>