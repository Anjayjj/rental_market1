<?php
class BookingController extends Controller {
    
    // 1. Fungsi untuk memproses form booking dari Halaman Detail Barang
    public function store() {
        $this->requireAuth('user');

        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Token Validation Failed.");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $item_id = htmlspecialchars($_POST['item_id']);
            $start_date = htmlspecialchars($_POST['start_date']);
            $end_date = htmlspecialchars($_POST['end_date']);
            $user_id = $_SESSION['user_id'];

            $itemModel = $this->model('ItemModel');
            $item = $itemModel->getItemById($item_id);

            if (!$item) {
                header('Location: ' . BASEURL . '/error/notfound');
                exit;
            }

            $bookingModel = $this->model('BookingModel');
            if (!$bookingModel->isDateAvailable($item_id, $start_date, $end_date)) {
                $_SESSION['flash'] = "Maaf, barang sudah disewa pada tanggal tersebut.";
                header('Location: ' . BASEURL . '/item/detail/' . $item['slug']);
                exit;
            }

            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $duration = $start->diff($end)->days + 1; 
            
            $daily_price = $item['price_daily'];
            $total_price = $duration * $daily_price;
            $admin_fee = 5000; 
            $grand_total = $total_price + $admin_fee;
            $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());

            $data = [
                'invoice_no' => $invoice_no,
                'item_id' => $item_id,
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'duration' => $duration,
                'daily_price' => $daily_price,
                'total_price' => $total_price,
                'admin_fee' => $admin_fee,
                'grand_total' => $grand_total
            ];

            if ($bookingModel->createBooking($data)) {
                $_SESSION['flash_success'] = "Booking berhasil! Silakan lakukan pembayaran.";
                header('Location: ' . BASEURL . '/booking/saya');
                exit;
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem.";
                header('Location: ' . BASEURL . '/item/detail/' . $item['slug']);
                exit;
            }
        }
    }

    // 2. Fungsi pengaman jika URL diakses /booking saja
    public function index() {
        header('Location: ' . BASEURL . '/booking/saya');
        exit;
    }

    // 3. Fungsi untuk menampilkan tabel riwayat sewa (booking) user
    public function saya() {
        $this->requireAuth(); 
        
        $bookingModel = $this->model('BookingModel');
        $data['title'] = 'Riwayat Sewa (Booking) Saya';
        $data['bookings'] = $bookingModel->getBookingsByUser($_SESSION['user_id']);
        
        $this->view('user/booking_saya', $data);
    }
    
    // 4. Fungsi membatalkan sewa
    public function cancel($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
                die("CSRF Token Invalid.");
            }
    
            $bookingModel = $this->model('BookingModel');
            $user_id = $_SESSION['user_id'];
    
            // Eksekusi pembatalan
            if ($bookingModel->cancelBooking($id, $user_id)) {
                $_SESSION['flash_success'] = "Pesanan berhasil dibatalkan.";
            } else {
                $_SESSION['flash_error'] = "Gagal membatalkan pesanan.";
            }
    
            header('Location: ' . BASEURL . '/booking/saya');
            exit;
        }
    }
    
    // 5. Fungsi untuk menampilkan halaman pesanan masuk untuk pemilik barang
    public function masuk() {
        $this->requireAuth('user');

        $bookingModel = $this->model('BookingModel');
        $user_id = $_SESSION['user_id'];

        $data['title'] = 'Pesanan Masuk';
        
        // Mengambil data pesanan di mana user yang sedang login adalah pemilik barangnya
        $data['incoming_bookings'] = $bookingModel->getIncomingBookings($user_id);

        $this->view('user/pesanan_masuk', $data);
    }

    /**
     * 6. Aksi untuk Pemilik: Menandai barang telah diserahkan
     */
    public function serahkanBarang() {
        $this->requireAuth('user'); // Keamanan tambahan
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi CSRF
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) die("CSRF Invalid");
            
            $booking_id = (int)$_POST['booking_id'];
            $signature = $_POST['signature'];
            $this->model('BookingModel')->updateStatusBooking($booking_id, 'handover');
            if ($this->model('BookingModel')->updateStatusToHandover($booking_id, $signature)) {
            
            $_SESSION['flash_success'] = "Status diperbarui! Menunggu penyewa mengonfirmasi penerimaan barang.";
            header('Location: ' . BASEURL . '/booking/masuk'); 
            exit;
        }
    }
    }

   /**
     * 7. Aksi untuk Penyewa: Mengonfirmasi bahwa barang telah diterima (Full Otomatis + TTD)
     */
    public function konfirmasiTerima() {
        $this->requireAuth('user');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) die("CSRF Invalid");
            
            $booking_id = (int)$_POST['booking_id'];
            $signature = $_POST['signature']; // Tangkap gambar Base64 TTD Penyewa
            
            // Panggil trigger utama agar status ketersediaan barang (tabel items) ikut terupdate jadi 'Rented'
            $this->model('BookingModel')->updateStatusBooking($booking_id, 'active');
            
            // Simpan gambar TTD dan update status
            if ($this->model('BookingModel')->updateStatusToActiveWithSignature($booking_id, $signature)) {
                $_SESSION['flash_success'] = "Barang telah diterima. Selamat menggunakan barang sewaan Anda!";
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem.";
            }
            header('Location: ' . BASEURL . '/booking/saya');
            exit;
        }
    }
    public function selesaiSewa() {
        $this->requireAuth('user');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) die("CSRF Invalid");
            
            $booking_id = (int)$_POST['booking_id'];
            
            // Ubah status menjadi 'completed' (Berlaku untuk pengembalian awal maupun terlambat)
            if ($this->model('BookingModel')->updateStatusBooking($booking_id, 'completed')) {
                $_SESSION['flash_success'] = "Sewa selesai! Barang telah dikembalikan dengan aman.";
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem saat menyelesaikan sewa.";
            }
            header('Location: ' . BASEURL . '/booking/masuk');
            exit;
        }
    }
    // Fungsi untuk Mencetak Surat Perjanjian
    public function cetakPerjanjian($booking_id) {
        // Wajib login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        
        $bookingModel = $this->model('BookingModel');
        $data['booking'] = $bookingModel->getAgreementData($booking_id);
        
        if (!$data['booking']) {
            die("Data pesanan tidak ditemukan.");
        }
        
        // PROTEKSI PRIVASI: Hanya penyewa, pemilik, atau admin yang bisa melihat surat ini
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_role'] ?? 'user';
        
        if ($user_id != $data['booking']['user_id'] && $user_id != $data['booking']['owner_id'] && $user_role != 'admin') {
            die("Akses Ditolak: Anda tidak memiliki izin untuk melihat dokumen ini.");
        }
        
        $data['title'] = 'Surat Perjanjian Sewa - ' . $data['booking']['invoice_no'];
        
        // Panggil view cetak (TANPA HEADER & FOOTER TEMPLATE WEBSITE)
        $this->view('user/perjanjian', $data);
    }
    public function submitReview() {
        $this->requireAuth('user');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) die("CSRF Invalid");
            
            // Susun data sesuai struktur tabel 'reviews' Anda
            $data = [
                'booking_id' => (int)$_POST['booking_id'],
                'item_id'    => (int)$_POST['item_id'],
                'user_id'    => $_SESSION['user_id'], // Ambil langsung dari session penyewa
                'rating'     => (int)$_POST['rating'],
                'comment'    => htmlspecialchars($_POST['comment'])
            ];
            
            if ($this->model('BookingModel')->saveReview($data)) {
                $_SESSION['flash_success'] = "Terima kasih! Ulasan Anda berhasil disimpan.";
            } else {
                $_SESSION['flash_error'] = "Gagal menyimpan ulasan.";
            }
            header('Location: ' . BASEURL . '/booking/saya');
            exit;
        }
    }
    
}
?>