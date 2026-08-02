<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<?php
// Array penerjemah status ke Bahasa Indonesia
$status_indo = [
    'pending'          => 'Menunggu Pembayaran',
    'paid'             => 'Menunggu Verifikasi Admin',
    'approved'         => 'Siap Diserahkan',
    'handover' => 'Proses Serah Terima',
    'active'           => 'Sedang Disewa',
    'in_use'           => 'Sedang Disewa',
    'completed'        => 'Selesai',
    'cancelled'        => 'Dibatalkan'
];
?>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Bagian Judul (Disamakan persis dengan Riwayat Sewa) -->
<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-primary border-4">
    <h4 class="m-0 fw-bold text-dark">Pesanan Masuk (Barang Saya)</h4>
</div>

<!-- Bagian Tabel -->
<div class="card shadow-sm border-0 bg-white">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Barang</th>
                        <th>Penyewa</th>
                        <th>Tgl Sewa</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['incoming_bookings'])): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td></tr>
                    <?php else: ?>
                        <?php foreach($data['incoming_bookings'] as $row): ?>
                            <?php
                                // Logika Warna Badge 
                                $badge = 'bg-secondary';
                                if($row['status'] == 'completed' || $row['status'] == 'active' || $row['status'] == 'in_use') $badge = 'bg-success';
                                if($row['status'] == 'approved' || $row['status'] == 'paid' || $row['status'] == 'handover') $badge = 'bg-info text-dark';
                                if($row['status'] == 'pending') $badge = 'bg-warning text-dark';
                                if($row['status'] == 'rejected' || $row['status'] == 'cancelled') $badge = 'bg-danger';

                                // Merapikan teks status
                                $status_text = ucfirst($row['status']);
                                if($row['status'] == 'approved' || $row['status'] == 'paid') $status_text = 'Approved (Admin)';
                                if($row['status'] == 'handover') $status_text = 'Menunggu Penerimaan';
                                if($row['status'] == 'active' || $row['status'] == 'in_use') $status_text = 'Sedang Disewa';
                            ?>
                                
                            <tr>
                                <td class="fw-semibold text-primary"><?= htmlspecialchars($row['invoice_no'] ?? 'INV-XXX'); ?></td>
                                <td><?= htmlspecialchars($row['item_name']); ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['renter_name']); ?></div>
                                    <small class="text-muted"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($row['renter_phone'] ?? '-'); ?></small>
                                </td>
                                <td><small class="text-muted"><?= $row['start_date']; ?> <br>s/d<br> <?= $row['end_date']; ?></small></td>
                                <td><span class="badge <?= $badge; ?> rounded-pill"><?= $status_text; ?></span></td>

                                <td class="text-center">
    <?php if ($row['status'] == 'paid' || $row['status'] == 'approved'): ?>
        
        <!-- Tombol Serahkan Barang -->
        <!-- Tombol Pemicu Modal -->
<button type="button" class="btn btn-warning btn-sm fw-semibold w-100 mb-1" data-bs-toggle="modal" data-bs-target="#modalTtdOwner<?= $row['id']; ?>">
    <i class="fas fa-handshake me-1"></i> Serahkan Barang
</button>

<!-- Modal Canvas Tanda Tangan -->
<div class="modal fade" id="modalTtdOwner<?= $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tanda Tangan Pemilik</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASEURL; ?>/booking/serahkanBarang" method="POST" onsubmit="return saveSignature(<?= $row['id']; ?>)">
                <div class="modal-body text-center">
                    <p class="small text-muted mb-2">Goreskan tanda tangan Anda di dalam kotak bawah ini sebagai bukti penyerahan barang.</p>
                    
                    <!-- Kotak Canvas -->
                    <canvas id="canvasTtd<?= $row['id']; ?>" width="300" height="150" style="border: 2px dashed #ccc; border-radius: 8px; touch-action: none; background-color: #f9f9f9;"></canvas>
                    
                    <!-- Input Hidden untuk Data Form -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                    <input type="hidden" name="signature" id="signatureInput<?= $row['id']; ?>">
                    
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCanvas(<?= $row['id']; ?>)"><i class="fas fa-eraser"></i> Hapus Ulang</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-check"></i> Konfirmasi & Serahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- Tombol WhatsApp -->
        <?php 
            $wa_number = preg_replace('/[^0-9]/', '', $row['renter_phone']);
            if (substr($wa_number, 0, 1) == '0') { $wa_number = '62' . substr($wa_number, 1);}
            $wa_text = urlencode("Halo, saya pemilik barang " . $row['item_name'] . " yang Anda sewa. Kapan dan di mana kita bisa bertemu untuk serah terima barang?");
        ?>
        <a href="https://wa.me/<?= $wa_number; ?>?text=<?= $wa_text; ?>" target="_blank" class="btn btn-sm btn-success fw-semibold w-100 mb-1">
            <i class="fab fa-whatsapp me-1"></i> WhatsApp
        </a>

    <?php elseif ($row['status'] == 'handover'): ?>
        <span class="badge bg-warning text-dark d-block mb-2"><i class="fas fa-clock"></i> Menunggu Konfirmasi Penyewa</span>
        
        <?php elseif ($row['status'] == 'active' || $row['status'] == 'in_use' || $row['status'] == 'overdue'): ?>
        
        <?php if ($row['status'] == 'overdue'): ?>
            <?php 
                $end_date_obj = new DateTime($row['end_date']);
                $today_obj = new DateTime(date('Y-m-d'));
                $late_days = $end_date_obj->diff($today_obj)->days;
            ?>
            <span class="badge bg-danger p-2 w-100 mb-2">
                <i class="fas fa-exclamation-triangle me-1"></i> Penyewa Terlambat <?= $late_days; ?> Hari
            </span>
        <?php else: ?>
            <span class="badge bg-primary p-2 w-100 mb-2"><i class="fas fa-check-circle me-1"></i> Sedang Disewa</span>
        <?php endif; ?>
        
        <!-- Tombol Pemilik Mengonfirmasi Pengembalian (Tampil di kondisi 'active' dan 'overdue') -->
        <form action="<?= BASEURL; ?>/booking/selesaiSewa" method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
            <button type="submit" class="btn btn-success btn-sm fw-semibold w-100" onclick="return confirm('Apakah fisik barang telah dikembalikan kepada Anda?');">
                <i class="fas fa-box me-1"></i> Barang Dikembalikan
            </button>
        </form>
        
    <?php else: ?>
        <span class="text-muted small fw-bold">
    <?= $status_indo[$row['status']] ?? ucfirst($row['status']); ?>
</span>
    <?php endif; ?>
    <?php if (!in_array($row['status'], ['pending', 'cancelled', 'rejected'])): ?>
        <a href="<?= BASEURL; ?>/booking/cetakPerjanjian/<?= $row['id']; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-semibold mt-2">
            <i class="fas fa-file-contract me-1"></i> Cetak Perjanjian
        </a>
    <?php endif; ?>
</td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Library JS Tanda Tangan -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePads = {};

    // Aktifkan Canvas hanya ketika Modal terbuka agar ukurannya akurat
    document.addEventListener('shown.bs.modal', function (event) {
        let modal = event.target;
        let canvas = modal.querySelector('canvas');
        if (canvas && !signaturePads[canvas.id]) {
            signaturePads[canvas.id] = new SignaturePad(canvas, {
                penColor: 'rgb(0, 0, 0)',
                backgroundColor: 'rgba(255, 255, 255, 0)' // Transparan
            });
        }
    });

    function clearCanvas(id) {
        if (signaturePads['canvasTtd' + id]) {
            signaturePads['canvasTtd' + id].clear();
        }
    }

    function saveSignature(id) {
        let pad = signaturePads['canvasTtd' + id];
        if (pad.isEmpty()) {
            alert("Silakan tanda tangan terlebih dahulu sebelum mengonfirmasi!");
            return false; // Hentikan form agar tidak terkirim
        }
        
        // Ubah goresan menjadi gambar Base64 dan simpan ke input hidden
        document.getElementById('signatureInput' + id).value = pad.toDataURL('image/png');
        return true; 
    }
</script>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>