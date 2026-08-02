<?php
class HomeController extends Controller {

    public function index() {
        $bookingModel = $this->model('BookingModel');
        $bookingModel->autoUpdateStatus();
        $homeModel = $this->model('HomeModel');

        $data['title'] = 'RentalMarket - Sewa Peralatan Terpercaya';
        $data['categories'] = $homeModel->getCategories();
        $latest = $homeModel->getLatestItems(8);
        $data['items'] = $latest;
        $exclude = array_column($latest, 'id');
        $data['more_items'] = $homeModel->getOtherItems($exclude, 8);
        $data['stats'] = $homeModel->getStats();

        $this->view('public/home', $data);
    }

    public function explore() {
        $homeModel = $this->model('HomeModel');

        // Menangkap parameter GET dari form pencarian (name="search"), filter kategori & sorting
        $keyword = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
        $lokasi = $_GET['lokasi'] ?? '';
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

        $data['title'] = 'Eksplorasi Barang';
        $data['categories'] = $homeModel->getCategories();
        $data['items'] = $homeModel->searchItems($keyword, $lokasi, $category_id, $sort);

        // Kirim kembali parameter agar form search & filter tetap terisi (Sticky Form)
        $data['search_q'] = $keyword;
        $data['search_cat'] = $category_id;
        $data['sort'] = $sort;

        $this->view('public/explore', $data);
    }
    // --- Endpoint untuk AJAX Autocomplete ---
    public function suggestions() {
        if (isset($_GET['q'])) {
            $keyword = htmlspecialchars($_GET['q']);
            $homeModel = $this->model('HomeModel');
            
            // Ambil maksimal 5 prediksi
            $results = $homeModel->getSearchSuggestions($keyword, 5);
            
            // Jadikan output sebagai JSON
            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        }
    }
}
