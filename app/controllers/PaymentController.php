<?php
class PaymentController extends Controller {

    // Halaman invoice + upload bukti pembayaran
    public function invoice($id) {
        $this->requireAuth('user');

        $bookingModel = $this->model('BookingModel');
        $booking = $bookingModel->getBookingById($id);

        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASEURL . '/booking/saya');
            exit;
        }

        $data['title'] = 'Pembayaran Invoice';
        $data['booking'] = $booking;
        $this->view('user/invoice_pembayaran', $data);
    }

    public function upload() {
        $this->requireAuth('user');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. CSRF Validation (Pakai '??' agar tidak error jika token kosong)
            if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
                die("Akses ditolak / CSRF Token tidak valid.");
            }

            $booking_id = (int)$_POST['booking_id'];
            $amount = (float)$_POST['amount']; // Di-cast ke float/int agar aman
            $payment_method = htmlspecialchars($_POST['payment_method']);

            // Tentukan URL kembali jika terjadi error (Sesuaikan dengan route asli Anda)
            $redirect_back = BASEURL . '/payment/checkout/' . $booking_id;

            // 2. Validasi File Upload (Pastikan atribut name="..." di HTML adalah 'proof_image')
            if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash_error'] = "File rusak atau belum diunggah.";
                header('Location: ' . $redirect_back);
                exit;
            }

            $file = $_FILES['proof_image'];
            $max_size = 2 * 1024 * 1024; // 2 MB

            // Cek Ukuran
            if ($file['size'] > $max_size) {
                $_SESSION['flash_error'] = "Ukuran file maksimal 2MB.";
                header('Location: ' . $redirect_back);
                exit;
            }

            // Cek MIME-Type sesungguhnya
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($mime_type, $allowed_types)) {
                $_SESSION['flash_error'] = "Format file tidak didukung. Gunakan JPG atau PNG.";
                header('Location: ' . $redirect_back);
                exit;
            }

            // 3. Persiapan Folder & Generate Nama File Aman
            $upload_dir = __DIR__ . '/../assets/uploads/payments/';
            
            // SOLUSI ERROR FOLDER: Buat folder otomatis jika belum ada!
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'proof_' . date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $upload_path = $upload_dir . $new_filename;

            // 4. Pindahkan file dan Simpan Database
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                
                $data = [
                    'booking_id' => $booking_id,
                    'amount' => $amount,
                    'payment_method' => $payment_method,
                    'proof_image' => $new_filename
                ];

                $paymentModel = $this->model('PaymentModel');
                if ($paymentModel->storePayment($data)) {
                    $bookingModel = $this->model('BookingModel');
                    $bookingModel->updateStatusBooking($booking_id, 'verifying'); 
                    // -------------------------------------

                    $_SESSION['flash_success'] = "Bukti pembayaran berhasil diunggah. Menunggu verifikasi Admin.";
                    header('Location: ' . BASEURL . '/booking/saya');
                    exit;
                }
            } else {
                $_SESSION['flash_error'] = "Gagal mengunggah sistem. Periksa perizinan folder.";
                header('Location: ' . $redirect_back);
                exit;
            }
        }
    }
    public function checkout($id) {
        // 1. Pastikan user sudah login
        $this->requireAuth('user');
    
        $bookingModel = $this->model('BookingModel');
        
        // 2. Ambil data transaksi/booking berdasarkan ID
        $data['title'] = 'Pembayaran Sewa';
        $data['booking'] = $bookingModel->getBookingById($id);
    
        // Jika data booking tidak ditemukan, kembalikan ke riwayat sewa
        if (!$data['booking']) {
            $_SESSION['flash_error'] = "Data transaksi tidak ditemukan.";
            header('Location: ' . BASEURL . '/booking/saya');
            exit;
        }
    
        // 3. Tampilkan halaman view pembayaran (sesuaikan dengan nama file view Anda)
        $this->view('user/invoice_pembayaran', $data); 
    }
}
?>