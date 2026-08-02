<?php
class ItemController extends Controller {

    public function detail($slug) {
        // Bebas diakses tanpa login
        $itemModel = $this->model('ItemModel'); // Asumsi ada method getItemBySlug() dan getImages()
        $reviewModel = $this->model('ReviewModel');
        $wishlistModel = $this->model('WishlistModel');

        // Ambil Data Barang & Pemilik
        $item = $itemModel->getItemBySlug($slug);
        
        if (!$item) {
            header('Location: ' . BASEURL . '/error/notfound');
            exit;
        }

        $item_id = $item['id'];

        $data['item'] = $item;
        $data['images'] = $itemModel->getItemImages($item_id); // Ambil galeri foto
        
        // Ambil Data Review
        $data['reviews'] = $reviewModel->getReviewsByItem($item_id);
        $data['rating'] = $reviewModel->getAverageRating($item_id);

        // Barang terkait (sesama kategori)
        $data['related'] = $itemModel->getRelatedItems($item['category_id'], $item_id, 4);

        // Cek Wishlist (Hanya jika login)
        $data['is_wishlist'] = false;
        if (isset($_SESSION['user_id'])) {
            $data['is_wishlist'] = $wishlistModel->checkWishlist($_SESSION['user_id'], $item_id) ? true : false;
        }
        $data['categories'] = $itemModel->getCategories();

        $this->view('public/detail_barang', $data);
    }

    // Endpoint AJAX untuk Wishlist
    public function toggle_wishlist() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silahkan login terlebih dahulu.";
            echo json_encode([
                'status' => 'redirect', 
                'url' => BASEURL . '/auth/login'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $item_id = (int)$_POST['item_id'];
            $user_id = $_SESSION['user_id'];

            $wishlistModel = $this->model('WishlistModel');
            $action = $wishlistModel->toggleWishlist($user_id, $item_id);

            echo json_encode(['status' => 'success', 'action' => $action]);
        }
    }
    public function search() {
        $keyword = $_GET['q'] ?? '';
        $lokasi = $_GET['lokasi'] ?? ''; 
        
        $data['title'] = 'Hasil Pencarian';
        // Kirimkan keyword dan lokasi ke model
        $data['items'] = $this->model('ItemModel')->searchItems($keyword, $lokasi);
        
        $this->view('templates/header', $data);
        $this->view('item/katalog', $data); // Sesuaikan dengan nama file view katalog Anda
        $this->view('templates/footer');
    }
}
?>