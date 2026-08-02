<?php
class ChatController extends Controller {

    public function __construct() {
        $this->requireAuth(); // Wajib Login
    }

    public function start($item_id, $owner_id) {
        $renter_id = $_SESSION['user_id'];
        
        if ($renter_id == $owner_id) {
            $_SESSION['flash_error'] = "Anda tidak bisa mengirim pesan ke barang milik sendiri.";
            header('Location: ' . BASEURL . '/item/detail/' . $item_id);
            exit;
        }

        $chatModel = $this->model('ChatModel');
        $room_id = $chatModel->getOrCreateRoom($item_id, $renter_id, $owner_id);
        
        header('Location: ' . BASEURL . '/chat/room/' . $room_id);
        exit;
    }

    public function room($room_id) {
        $chatModel = $this->model('ChatModel');
        $userModel = $this->model('UserModel'); 
        
        if (!$chatModel->checkRoomAccess($room_id, $_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/error/forbidden');
            exit;
        }

        // Siapkan array data
        $data = [];

        // Tarik data ruangan obrolan dari tabel chat_rooms
        $room = $chatModel->getRoomById($room_id);

        if ($room) {
            // Tentukan siapa lawan bicaranya berdasarkan database Anda (renter_id vs owner_id)
            $lawan_bicara_id = ($room['renter_id'] == $_SESSION['user_id']) ? $room['owner_id'] : $room['renter_id'];
            
            // Masukkan data lawan bicara ke dalam array 'owner'
            $data['owner'] = $userModel->getUserById($lawan_bicara_id);
        } else {
            $data['owner'] = null;
        }
        
        // Kirim $data ke tampilan chat_room
        $this->view('user/chat_room', $data);
    }
}
?>