<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Verifikasi Pengembalian</h4>
        <p class="text-muted small mb-0">Konfirmasi barang telah dikembalikan dan hitung denda jika ada.</p>
    </div>
    <a href="<?= BASEURL; ?>/admin/bookings" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
</div>

<div class="card shadow-sm border-primary" style="max-width: 640px;">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-undo me-2"></i>Verifikasi Pengembalian Barang</h5>
    </div>
    <div class="card-body">
        <!-- Rincian Penyewaan -->
        <div class="alert alert-info border-0">
            <strong>Barang:</strong> <?= htmlspecialchars($data['booking']['item_name']); ?><br>
            <strong>Tanggal Harus Kembali:</strong> <?= date('d M Y', strtotime($data['booking']['end_date'])); ?><br>
            <strong>Penyewa:</strong> <?= htmlspecialchars($data['booking']['user_name']); ?>
        </div>

        <form action="<?= BASEURL; ?>/return/process" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
            <input type="hidden" name="booking_id" value="<?= $data['booking']['id']; ?>">

            <div class="mb-3">
                <label class="form-label text-danger fw-bold">Denda Kerusakan (Opsional)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" name="damage_fee" value="0" min="0" step="1000">
                </div>
                <small class="text-muted">Isi hanya jika barang dikembalikan dalam keadaan rusak/hilang komponen.</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi Kerusakan (Opsional)</label>
                <textarea class="form-control" name="damage_desc" rows="2" placeholder="Contoh: Lensa kamera tergores, tripod patah..."></textarea>
            </div>

            <div class="alert alert-warning small border-0">
                <i class="fas fa-exclamation-triangle"></i> Denda keterlambatan (jika ada) akan dihitung otomatis oleh sistem berdasarkan tanggal hari ini.
            </div>

            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin barang ini sudah dikembalikan? Status penyewaan akan diselesaikan.')">
                <i class="fas fa-check-circle"></i> Konfirmasi Pengembalian
            </button>
        </form>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>
