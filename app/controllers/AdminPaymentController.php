<?php
class AdminPaymentController extends Controller {
    
    public function verifikasi() {
        // Middleware: Pastikan hanya Role Admin yang bisa mengakses fungsi ini
        $this->requireAuth('admin');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi CSRF
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Akses Ilegal / CSRF Token Mismatch.");
            }

            $payment_id = (int)$_POST['payment_id'];
            $booking_id = (int)$_POST['booking_id'];
            $action = $_POST['action']; // 'verify' atau 'reject'

            $paymentModel = $this->model('PaymentModel');
            $bookingModel = $this->model('BookingModel');

            if ($action === 'verify') {
                // 1. Ubah status tabel payments menjadi 'verified'
                $paymentModel->updateStatus($payment_id, 'verified');
                
                // 2. Ubah status tabel bookings menjadi 'approved'
                $bookingModel->updateStatusBooking($booking_id, 'approved');

                $_SESSION['flash_success'] = "Pembayaran Valid. Status Booking telah disetujui.";
            } elseif ($action === 'reject') {
                // 1. Ubah status pembayaran menjadi 'failed'
                $paymentModel->updateStatus($payment_id, 'failed');
                
                // 2. Ubah status booking menjadi 'rejected' agar User mengunggah ulang / booking ulang
                $bookingModel->updateStatusBooking($booking_id, 'rejected');

                $_SESSION['flash_error'] = "Pembayaran Ditolak. Transaksi dibatalkan.";
            }

            // Arahkan kembali ke halaman tabel daftar pembayaran Admin
            header('Location: ' . BASEURL . '/admin/payments');
            exit;
        }
    }
}
?>