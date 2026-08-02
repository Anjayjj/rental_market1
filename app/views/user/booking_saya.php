<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<?php
// Array penerjemah status ke Bahasa Indonesia
$status_indo = [
    'pending'          => 'Menunggu Pembayaran',
    'verifying'        => 'Menunggu Verifikasi',
    'paid'             => 'Menunggu Verifikasi Admin',
    'approved'         => 'Approved (Admin)',
    'handover' => 'Harap Konfirmasi',
    'active'           => 'Sedang Anda Sewa',
    'in_use'           => 'Sedang Anda Sewa',
    'overdue'          => 'Terlambat Mengembalikan',
    'completed'        => 'Selesai',
    'rejected'         => 'Ditolak',
    'cancelled'        => 'Dibatalkan'
];
?>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-primary border-4">
    <h4 class="m-0 fw-bold text-dark">Riwayat Sewa Anda</h4>
</div>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Barang</th>
                        <th>Tgl Sewa</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['bookings'])): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat penyewaan.</td></tr>
                    <?php else: ?>
                        <?php foreach($data['bookings'] as $trx): ?>
                            <?php 
        // Format nomor WA (Ubah 08... menjadi 628...)
        $wa_number = $trx['owner_phone'];
        if (substr($wa_number, 0, 1) == '0') {
            $wa_number = '62' . substr($wa_number, 1);
        }
        
        // Buat template pesan otomatis
        $wa_text = urlencode("Halo Kak " . $trx['owner_name'] . ", saya penyewa barang " . $trx['item_name'] . " (Invoice: " . $trx['invoice_no'] . "). Saya ingin diskusi terkait waktu dan lokasi serah terima barangnya.");
        $wa_link = "https://wa.me/{$wa_number}?text={$wa_text}";
    ?>
                            <?php
                                // Ambil status dari database dan jadikan huruf kecil semua agar aman
                                $status_db = strtolower($trx['status']);
                                
                              // 1. Logika Badge Warna
                              $badge = 'bg-secondary';
                              if($status_db == 'completed') $badge = 'bg-success';
                              if($status_db == 'active' || $status_db == 'in_use') $badge = 'bg-primary';
                              if($status_db == 'approved' || $status_db == 'paid') $badge = 'bg-info text-dark';
                              if($status_db == 'verifying') $badge = 'bg-secondary text-white';
                              if($status_db == 'handover' || $status_db == 'pending') $badge = 'bg-warning text-dark';
                              if($status_db == 'rejected' || $status_db == 'cancelled' || $status_db == 'overdue') $badge = 'bg-danger';
                              
                              // 2. Logika Teks & Hitung Hari Terlambat
                              $status_text = $status_indo[$status_db] ?? ucfirst($status_db);
                              if ($status_db === 'overdue') {
                                  $end_date_obj = new DateTime($trx['end_date']);
                                  $today_obj = new DateTime(date('Y-m-d'));
                                  $late_days = $end_date_obj->diff($today_obj)->days;
                                  $status_text = "Terlambat " . $late_days . " Hari";
                              }
                            ?>
                            <tr>
                                <td class="fw-semibold text-primary"><?= htmlspecialchars($trx['invoice_no']); ?></td>
                                <td><?= htmlspecialchars($trx['item_name']); ?></td>
                                <td><small class="text-muted"><?= $trx['start_date']; ?> <br>s/d<br> <?= $trx['end_date']; ?></small></td>
                                <td class="fw-semibold">Rp <?= number_format($trx['grand_total'], 0, ',', '.'); ?></td>
                                <td><span class="badge <?= $badge; ?> rounded-pill"><?= $status_text; ?></span></td>

                                <td class="text-center">
                                    <?php if ($status_db === 'pending') : ?>
                                        <!-- Belum Bayar -->
                                        <a href="<?= BASEURL; ?>/payment/checkout/<?= $trx['id']; ?>" class="btn btn-sm btn-primary fw-semibold mb-1 me-2">Bayar</a>
                                        <form action="<?= BASEURL; ?>/booking/cancel/<?= $trx['id']; ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan sewa ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold mb-1">Batalkan</button>
                                        </form>

                                    <?php elseif ($status_db === 'verifying'): ?>
                                        <!-- Sedang diverifikasi Admin -->
                                        <span class="text-muted small">Menunggu Verifikasi Pembayaran</span>
                                        
                                        <?php elseif ($status_db === 'approved' || $status_db === 'paid'): ?>
                                        <!-- Sudah di ACC Admin (atau COD), Penyewa menghubungi Pemilik -->
                                        <span class="text-info small fw-bold d-block mb-2">
                                            <i class="fas fa-clock"></i> Menunggu Diambil
                                        </span>
                                        
                                        <!-- Tombol WhatsApp untuk Penyewa -->
                                        <a href="<?= $wa_link; ?>" target="_blank" class="btn btn-success btn-sm w-100 fw-semibold mb-1">
                                            <i class="fab fa-whatsapp me-1"></i> Hubungi Pemilik
                                        </a>
                                        
                                        <!-- Menampilkan Nama Pemilik -->
                                        <span class="text-muted d-block mt-1" style="font-size: 11px;">
                                            Pemilik: <?= htmlspecialchars($trx['owner_name']); ?>
                                        </span>
                                        
                                   <?php elseif ($status_db === 'handover'): ?>
    <!-- Tombol Pemicu Modal TTD Penyewa -->
    <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTtdRenter<?= $trx['id']; ?>">
        <i class="fas fa-box-open me-1"></i> Terima Barang
    </button>

    <!-- Modal Canvas Tanda Tangan Penyewa -->
    <div class="modal fade" id="modalTtdRenter<?= $trx['id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tanda Tangan Penyewa</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= BASEURL; ?>/booking/konfirmasiTerima" method="POST" onsubmit="return saveSignatureRenter(<?= $trx['id']; ?>)">
                    <div class="modal-body text-center">
                        <p class="small text-muted mb-2">Tanda tangani di bawah ini sebagai bukti resmi bahwa fisik barang telah Anda terima dengan baik.</p>
                        
                        <!-- Kotak Canvas -->
                        <canvas id="canvasTtdRenter<?= $trx['id']; ?>" width="300" height="150" style="border: 2px dashed #ccc; border-radius: 8px; background-color: #f9f9f9; touch-action: none;"></canvas>
                        
                        <!-- Input Hidden -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="booking_id" value="<?= $trx['id']; ?>">
                        <input type="hidden" name="signature" id="signatureInputRenter<?= $trx['id']; ?>">
                        
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCanvasRenter(<?= $trx['id']; ?>)"><i class="fas fa-eraser"></i> Hapus Ulang</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-check"></i> Konfirmasi Terima</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                                    <?php elseif ($status_db === 'active' || $status_db === 'in_use') : ?>
                                        <!-- Sedang asyik disewa -->
                                        <span class="badge bg-success p-2"><i class="fas fa-check-circle me-1"></i> Sedang Digunakan</span>

                                        <?php elseif ($status_db === 'completed'): ?>
    <?php if ($trx['is_reviewed'] == 0): ?> <!-- Cek apakah jumlah ulasan = 0 -->
        <!-- Tombol Pemicu Modal Ulasan -->
        <button type="button" class="btn btn-warning btn-sm fw-bold w-100 shadow-sm text-dark mt-1" data-bs-toggle="modal" data-bs-target="#modalReview<?= $trx['id']; ?>">
            <i class="fas fa-star me-1"></i> Beri Ulasan
        </button>

        <!-- Modal Form Ulasan -->
        <div class="modal fade text-start" id="modalReview<?= $trx['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Ulas Pengalaman Sewa</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= BASEURL; ?>/booking/submitReview" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="booking_id" value="<?= $trx['id']; ?>">
                            <!-- Tambahkan input item_id tersembunyi -->
                            <input type="hidden" name="item_id" value="<?= $trx['item_id']; ?>"> 
                            
                            <div class="mb-3 text-center">
                                <label class="form-label fw-bold d-block">Beri Rating (1-5)</label>
                                <select name="rating" class="form-select w-50 mx-auto text-center font-monospace" required>
                                    <option value="" disabled selected>Pilih Rating</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5/5) Sangat Bagus</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5) Bagus</option>
                                    <option value="3">⭐⭐⭐ (3/5) Cukup</option>
                                    <option value="2">⭐⭐ (2/5) Kurang</option>
                                    <option value="1">⭐ (1/5) Sangat Kurang</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tulis Ulasan Anda</label>
                                <!-- Ubah name menjadi "comment" -->
                                <textarea name="comment" class="form-control" rows="3" placeholder="Bagaimana kondisi barang dan pelayanan pemiliknya?" required></textarea> 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-paper-plane me-1"></i> Kirim Ulasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <span class="badge border border-success text-success p-2 w-100 mt-1"><i class="fas fa-check-circle me-1"></i> Sudah Diulas</span>
    <?php endif; ?>

<?php else: ?>
    <!-- Dibatalkan / Ditolak -->
    <span class="text-muted small fw-bold">Dibatalkan / Ditolak</span>
<?php endif; ?>
                                    <?php if (!in_array($status_db, ['pending', 'cancelled', 'rejected'])): ?>
    <a href="<?= BASEURL; ?>/booking/cetakPerjanjian/<?= $trx['id']; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-semibold mt-1">
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

    document.addEventListener('shown.bs.modal', function (event) {
        let modal = event.target;
        let canvas = modal.querySelector('canvas');
        if (canvas && !signaturePads[canvas.id]) {
            signaturePads[canvas.id] = new SignaturePad(canvas, {
                penColor: 'rgb(0, 0, 0)',
                backgroundColor: 'rgba(255, 255, 255, 0)'
            });
        }
    });

    function clearCanvasRenter(id) {
        if (signaturePads['canvasTtdRenter' + id]) {
            signaturePads['canvasTtdRenter' + id].clear();
        }
    }

    function saveSignatureRenter(id) {
        let pad = signaturePads['canvasTtdRenter' + id];
        if (pad.isEmpty()) {
            alert("Silakan tanda tangan terlebih dahulu sebelum mengonfirmasi!");
            return false; 
        }
        document.getElementById('signatureInputRenter' + id).value = pad.toDataURL('image/png');
        return true; 
    }
</script>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>