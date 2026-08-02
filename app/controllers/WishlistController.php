<?php
class WishlistController extends Controller {

    // Fungsi ini akan dipanggil saat ikon Hati di header diklik
    public function index() {
        // 1. Cek apakah user belum login
        if (!isset($_SESSION['user_id'])) {
            // Buat pesan error yang sama persis seperti di keranjang
            $_SESSION['flash_error'] = "Silakan login terlebih dahulu untuk melihat wishlist Anda.";
            
            // Arahkan ke halaman login
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // 2. Jika sudah login, ambil data wishlist dan tampilkan halamannya
        $data['title'] = 'Wishlist Saya';
        $wishlistModel = $this->model('WishlistModel');
        
        // Ambil data barang apa saja yang ada di wishlist user ini
        $data['items'] = $wishlistModel->getWishlistByUser($_SESSION['user_id']); 
        
        // Tampilkan ke halaman view (pastikan Anda sudah membuat file view-nya)
        $this->view('user/wishlist', $data);
    }
}