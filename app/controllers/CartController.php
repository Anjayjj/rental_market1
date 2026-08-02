<?php
class CartController extends Controller {

    // Tampilkan Keranjang Sewa
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silakan login terlebih dahulu.";
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        $data['title'] = 'Keranjang Sewa';
        $data['items'] = $_SESSION['cart'] ?? [];
        $data['total'] = 0;
        foreach ($data['items'] as $it) {
            $data['total'] += $it['total_price'] + 5000;
        }
        $this->view('user/keranjang', $data);
    }

    // Tambah ke keranjang dari halaman detail
    public function add() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = "Silahkan login terlebih dahulu.";
            
            // 1. Deteksi apakah request ini berasal dari JavaScript/AJAX
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                      || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

            if ($isAjax) {
                // Jika pakai JS, kirim instruksi ke JS untuk pindah halaman
                echo json_encode(['status' => 'redirect', 'url' => BASEURL . '/auth/login']);
            } else {
                // Jika pakai form biasa, PAKSA simpan pesan ke memori sebelum pindah!
                session_write_close(); 
                header('Location: ' . BASEURL . '/auth/login');
            }
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
                die("CSRF Token Invalid.");
            }
            $item_id = (int)$_POST['item_id'];
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];
            
            // TANGKAP METODE PEMBAYARAN (Default ke 'online' jika kosong/tidak dipilih)
            $payment_method = $_POST['payment_method'] ?? 'online';

            $itemModel = $this->model('ItemModel');
            $item = $itemModel->getItemById($item_id);
            if (!$item) { header('Location: ' . BASEURL . '/home/explore'); exit; }

            $d = (new DateTime($end))->diff(new DateTime($start))->days + 1;
            if ($d < 1) { header('Location: ' . BASEURL . '/item/detail/' . $item['slug']); exit; }

            // SIMPAN METODE PEMBAYARAN KE DALAM SESSION CART
            $_SESSION['cart'][$item_id] = [
                'item_id'        => $item_id,
                'slug'           => $item['slug'],
                'name'           => $item['name'],
                'cover_image'    => $itemModel->getItemImages($item_id)[0]['image_path'] ?? 'default.jpg',
                'start'          => $start,
                'end'            => $end,
                'duration'       => $d,
                'daily_price'    => $item['price_daily'],
                'total_price'    => $d * $item['price_daily'],
                'payment_method' => $payment_method // <-- Disimpan di sini
            ];

            $_SESSION['flash_success'] = "Ditambahkan ke Keranjang Sewa.";
        }
        header('Location: ' . BASEURL . '/cart');
        exit;
    }

    // Hapus item dari keranjang
    public function remove($item_id) {
        unset($_SESSION['cart'][$item_id]);
        header('Location: ' . BASEURL . '/cart');
        exit;
    }

    // Checkout: buat booking untuk tiap item, lalu kosongkan cart
    public function checkout() {
        $this->requireAuth('user');
        if (empty($_SESSION['cart'])) {
            header('Location: ' . BASEURL . '/cart'); exit;
        }
        $bookingModel = $this->model('BookingModel');
        $count = 0;
        
        foreach ($_SESSION['cart'] as $it) {
            $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // SIAPKAN DATA UNTUK DIKIRIM KE DATABASE
            $data = [
                'invoice_no'     => $invoice_no,
                'item_id'        => $it['item_id'],
                'user_id'        => $_SESSION['user_id'],
                'start_date'     => $it['start'],
                'end_date'       => $it['end'],
                'duration'       => $it['duration'],
                'daily_price'    => $it['daily_price'],
                'total_price'    => $it['total_price'],
                'admin_fee'      => 5000,
                'grand_total'    => $it['total_price'] + 5000,
                'payment_method' => $it['payment_method'] ?? 'online' // <-- Dikirim ke database
            ];
            
            if ($bookingModel->createBooking($data)) $count++;
        }
        
        $_SESSION['cart'] = [];
        if ($count > 0) {
            $_SESSION['flash_success'] = "Pesanan berhasil dibuat. Cek status pada riwayat pesanan Anda.";
        }
        header('Location: ' . BASEURL . '/booking/saya');
        exit;
    }
}