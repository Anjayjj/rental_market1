<?php
class PaymentModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function storePayment($data) {
        try {
            $this->db->dbh->beginTransaction(); // Mulai Transaksi

            // 1. Insert ke tabel payments
            $query1 = "INSERT INTO payments (booking_id, amount, payment_method, proof_image, status) 
                       VALUES (:booking_id, :amount, :payment_method, :proof_image, 'pending')";
            $this->db->query($query1);
            $this->db->bind('booking_id', $data['booking_id']);
            $this->db->bind('amount', $data['amount']);
            $this->db->bind('payment_method', $data['payment_method']);
            $this->db->bind('proof_image', $data['proof_image']);
            $this->db->execute();

            // 2. Update status bookings menjadi 'pending' (menunggu verifikasi)
            // (Asumsinya sebelumnya status adalah 'pending' tapi belum ada payment)
            // Anda bisa menambahkan status 'waiting_verification' jika mengubah skema ENUM.
            
            $this->db->dbh->commit(); // Eksekusi Transaksi
            return true;
        } catch (Exception $e) {
            $this->db->dbh->rollBack(); // Batalkan jika salah satu gagal
            return false;
        }
    }

    // Method untuk Admin mengubah status
    public function updatePaymentStatus($payment_id, $booking_id, $status_payment, $status_booking) {
        try {
            $this->db->dbh->beginTransaction();

            // Update tabel payment
            $this->db->query("UPDATE payments SET status = :status WHERE id = :id");
            $this->db->bind('status', $status_payment);
            $this->db->bind('id', $payment_id);
            $this->db->execute();

            // Update tabel booking (disetujui / ditolak)
            $this->db->query("UPDATE bookings SET status = :status WHERE id = :id");
            $this->db->bind('status', $status_booking);
            $this->db->bind('id', $booking_id);
            $this->db->execute();

            $this->db->dbh->commit();
            return true;
        } catch (Exception $e) {
            $this->db->dbh->rollBack();
            return false;
        }
    }
    public function updateStatus($id, $status) 
    {
        $this->db->query("UPDATE payments SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        
        return $this->db->execute();
    }
}
?>