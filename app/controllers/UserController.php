<?php
class UserController extends Controller {

    public function __construct() {
        // Hanya user yang sudah login yang bisa mengakses menu ini
        $this->requireAuth();
    }

    // Halaman Utama Dashboard User
    public function dashboard() {
        $data['title'] = 'Dashboard Saya';
        $itemModel = $this->model('ItemModel');
        $bookingModel = $this->model('BookingModel');
        $bookingModel->autoUpdateStatus();
        $my_items = $itemModel->getItemsByOwner($_SESSION['user_id']);
        $my_bookings = $bookingModel->getBookingsByUser($_SESSION['user_id']);
        $rented = 0; $done = 0;
        foreach ($my_bookings as $b) {
            if (in_array($b['status'], array('pending','approved','active'))) $rented++;
            if ($b['status'] === 'completed') $done++;
        }
        $data['my_items'] = $my_items;
        $data['my_bookings'] = $my_bookings;
        $data['stats'] = array('items' => count($my_items), 'rented' => $rented, 'done' => $done);
        $this->view('user/dashboard', $data);
    }

    // Placeholder untuk halaman pengaturan (Bisa dikembangkan nanti)
    public function settings() {
        $data['title'] = 'Pengaturan Akun';
        $data['user'] = $this->model('UserModel')->getUserById($_SESSION['user_id']);
    
    $this->view('templates/header_user', $data);
    $this->view('user/settings', $data);
    $this->view('templates/footer');
    }

    // Halaman Wishlist / Barang Disimpan
    public function wishlist() {
        $this->requireAuth();
        $wishlistModel = $this->model('WishlistModel');
        $itemModel = $this->model('ItemModel');

        $ids = $wishlistModel->getItemIdsByUser($_SESSION['user_id']);
        $items = [];
        foreach ($ids as $id) {
            $it = $itemModel->getItemById($id);
            if ($it) {
                $img = $itemModel->getItemImages($id);
                $it['cover_image'] = $img[0]['image_path'] ?? 'default.jpg';
                $items[] = $it;
            }
        }
        $data['title'] = 'Wishlist Saya';
        $data['items'] = $items;
        $this->view('user/wishlist', $data);
    }
   // Method untuk memproses pembaruan profil dan upload foto
   public function update_profile() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars(trim($_POST['name']));
        $address = htmlspecialchars(trim($_POST['address']));
        $user_id = $_SESSION['user_id'];
        
        // Default avatar adalah avatar yang sedang dipakai saat ini
       // Default avatar adalah avatar yang sedang dipakai saat ini
       $avatar = $_SESSION['user_avatar']; 
       $target_dir = __DIR__ . '/../../assets/uploads/avatars/';

       // CEK 1: APAKAH USER MEMINTA UNTUK MENGHAPUS FOTO? (KEMBALI KE DEFAULT)
       if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] == '1') {
           if ($avatar != 'default.png') {
               $old_file = $target_dir . $avatar;
               if (file_exists($old_file)) {
                   unlink($old_file); // Hapus foto dari folder
               }
           }
           $avatar = 'default.png'; // Set kembali nama database menjadi default.png
       } 
       // CEK 2: JIKA TIDAK DIHAPUS, APAKAH ADA UPLOAD FOTO BARU?
       else if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
           
           $file_name = $_FILES['avatar']['name'];
           $file_size = $_FILES['avatar']['size'];
           $file_tmp = $_FILES['avatar']['tmp_name'];
           
           $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
           $allowed_ext = ['jpg', 'jpeg', 'png'];

           if (!in_array($file_ext, $allowed_ext)) {
               $_SESSION['flash_error'] = "Gagal: Format foto harus JPG atau PNG.";
               header('Location: ' . BASEURL . '/user/settings');
               exit;
           }

           if ($file_size > 2000000) {
               $_SESSION['flash_error'] = "Gagal: Ukuran foto maksimal 2MB.";
               header('Location: ' . BASEURL . '/user/settings');
               exit;
           }

           $new_avatar_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
           $target_file = $target_dir . $new_avatar_name;

           if (move_uploaded_file($file_tmp, $target_file)) {
               $avatar = $new_avatar_name;

               if ($_SESSION['user_avatar'] != 'default.png') {
                   $old_file = $target_dir . $_SESSION['user_avatar'];
                   if (file_exists($old_file)) {
                       unlink($old_file);
                   }
               }
           } else {
               $_SESSION['flash_error'] = "Terjadi kesalahan saat mengunggah foto.";
               header('Location: ' . BASEURL . '/user/settings');
               exit;
           }
       }

        // LOGIKA PENYIMPANAN DATABASE
        if (!empty($name) && !empty($address)) {
            $userModel = $this->model('UserModel');
            
            // Memasukkan $avatar ke dalam parameter model
            if ($userModel->updateProfile($user_id, $name, $address, $avatar)) {
                
                // UPDATE SESSION agar foto baru langsung muncul di pojok kanan atas
                $_SESSION['user_name'] = $name;
                $_SESSION['user_avatar'] = $avatar; 
                
                $_SESSION['flash_success'] = "Perubahan berhasil disimpan!";
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan pada sistem database.";
            }
        } else {
            $_SESSION['flash_error'] = "Nama dan alamat tidak boleh kosong.";
        }

        header('Location: ' . BASEURL . '/user/settings');
        exit;
    }
}
}
?>