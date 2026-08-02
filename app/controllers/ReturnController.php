<?php
class ReturnController extends Controller {

    // Menampilkan form verifikasi pengembalian barang (untuk Admin/Pemilik)
    public function form($id) {
        $this->requireAuth();

        $returnModel = $this->model('ReturnModel');
        $booking = $returnModel->getBookingDetail($id);

        if (!$booking) {
            header('Location: ' . BASEURL . '/admin/bookings');
            exit;
        }

        $data['title'] = 'Verifikasi Pengembalian';
        $data['booking'] = $booking;
        $this->view('dashboard/form_pengembalian', $data);
    }

    public function process() {
        // Asumsi: Admin atau Pemilik Barang yang bisa memproses pengembalian
        $this->requireAuth(); // Pastikan user login (Role check spesifik bisa ditambahkan)

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi CSRF
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Validation Failed.");
            }

            $booking_id = (int)$_POST['booking_id'];
            $damage_fee = (float)$_POST['damage_fee']; // Denda kerusakan manual (jika ada)
            $damage_desc = htmlspecialchars($_POST['damage_desc']);

            $returnModel = $this->model('ReturnModel');
            $booking = $returnModel->getBookingForReturn($booking_id);

            if (!$booking) {
                $_SESSION['flash_error'] = "Data penyewaan tidak ditemukan atau sudah selesai.";
                header('Location: ' . BASEURL . '/dashboard/rentals');
                exit;
            }

            // 1. Kalkulasi Keterlambatan
            // Menggunakan DateTime PHP (Akurat untuk menangani tahun kabisat dll)
            $end_date_expected = new DateTime($booking['end_date']);
            
            // Tanggal aktual pengembalian (hari ini)
            // Di lingkungan produksi sesungguhnya gunakan waktu server (Asia/Jakarta)
            $date_returned_actual = new DateTime('now'); 
            
            // Set waktu ke 00:00:00 untuk perbandingan murni tanggal
            $end_date_expected->setTime(0,0,0);
            $date_returned_actual->setTime(0,0,0);

            $late_days = 0;
            $late_fee = 0;

            // Jika tanggal kembali > batas tanggal selesai
            if ($date_returned_actual > $end_date_expected) {
                $interval = $end_date_expected->diff($date_returned_actual);
                $late_days = $interval->days;

                // Rumus Denda Keterlambatan: (Harga Sewa Harian x Jumlah Hari Telat) + Penalti (Opsional)
                // Misal penalti keterlambatan adalah 50% lebih mahal dari harga harian
                $daily_penalty_rate = $booking['price_daily'] * 1.5; 
                $late_fee = $late_days * $daily_penalty_rate;
            }

            // 2. Total Penalti (Keterlambatan + Kerusakan)
            $total_penalty = $late_fee + $damage_fee;
            
            $description = [];
            if ($late_days > 0) {
                $description[] = "Terlambat $late_days hari.";
            }
            if ($damage_fee > 0) {
                $description[] = "Kerusakan: " . $damage_desc;
            }
            $final_description = implode(" | ", $description);

            // 3. Siapkan Data untuk disimpan
            $data = [
                'booking_id' => $booking_id,
                'late_days' => $late_days,
                'late_fee' => $late_fee,
                'damage_fee' => $damage_fee,
                'total_penalty' => $total_penalty,
                'description' => $final_description
            ];

            // 4. Eksekusi Pengembalian
            if ($returnModel->processReturn($data)) {
                if ($total_penalty > 0) {
                    $_SESSION['flash_warning'] = "Barang dikembalikan. Denda tercatat sebesar Rp " . number_format($total_penalty, 0, ',', '.');
                } else {
                    $_SESSION['flash_success'] = "Barang berhasil dikembalikan tanpa denda.";
                }
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem saat memproses pengembalian.";
            }

            header('Location: ' . BASEURL . '/admin/bookings');
            exit;
        }
    }
}
?>