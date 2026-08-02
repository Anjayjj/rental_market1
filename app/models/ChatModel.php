<?php
class ChatModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Cari ruang obrolan, jika belum ada, buat baru
    public function getOrCreateRoom($item_id, $renter_id, $owner_id) {
        $this->db->query("SELECT id FROM chat_rooms WHERE item_id = :item_id AND renter_id = :renter_id");
        $this->db->bind('item_id', $item_id);
        $this->db->bind('renter_id', $renter_id);
        $room = $this->db->single();

        if ($room) {
            return $room['id'];
        } else {
            $this->db->query("INSERT INTO chat_rooms (item_id, renter_id, owner_id) VALUES (:item_id, :renter_id, :owner_id)");
            $this->db->bind('item_id', $item_id);
            $this->db->bind('renter_id', $renter_id);
            $this->db->bind('owner_id', $owner_id);
            $this->db->execute();
            return $this->db->dbh->lastInsertId();
        }
    }

    // Validasi apakah user yang sedang login berhak mengakses Room ini
    public function checkRoomAccess($room_id, $user_id) {
        $this->db->query("SELECT id FROM chat_rooms WHERE id = :room_id AND (renter_id = :user_id OR owner_id = :user_id)");
        $this->db->bind('room_id', $room_id);
        $this->db->bind('user_id', $user_id);
        return $this->db->single();
    }

    // Mengambil pesan baru berdasarkan last_msg_id (Optimasi AJAX Polling)
    public function getMessages($room_id, $last_msg_id = 0) {
        $query = "SELECT m.*, u.name as sender_name, u.avatar as sender_avatar 
                  FROM chat_messages m 
                  JOIN users u ON m.sender_id = u.id 
                  WHERE m.room_id = :room_id AND m.id > :last_msg_id 
                  ORDER BY m.created_at ASC";
        
        $this->db->query($query);
        $this->db->bind('room_id', $room_id);
        $this->db->bind('last_msg_id', $last_msg_id);
        return $this->db->resultSet();
    }

    // Simpan pesan baru
    public function sendMessage($room_id, $sender_id, $message) {
        $query = "INSERT INTO chat_messages (room_id, sender_id, message) VALUES (:room_id, :sender_id, :message)";
        $this->db->query($query);
        $this->db->bind('room_id', $room_id);
        $this->db->bind('sender_id', $sender_id);
        $this->db->bind('message', $message);
        return $this->db->execute();
    }
    public function getRoomById($room_id) {
        $this->db->query("SELECT * FROM chat_rooms WHERE id = :id");
        $this->db->bind('id', $room_id);
        return $this->db->single();
    }
}
?>