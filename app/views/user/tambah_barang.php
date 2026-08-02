<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-success border-4">
    <h4 class="m-0 fw-bold text-dark">Tambah Barang Sewaan</h4>
</div>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-4">
        <form action="<?= BASEURL; ?>/useritem/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
            <div class="row">
                <div class="col-md-7">
                    <div class="mb-3"><label class="form-label fw-semibold">Nama Barang</label><input type="text" class="form-control" name="name" required placeholder="Contoh: Kamera Sony A7III + Lensa Kit"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select class="form-select" name="category_id" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php if(isset($data['categories'])): ?>
                                <?php foreach($data['categories'] as $cat): ?><option value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option><?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label fw-semibold">Deskripsi & Spesifikasi</label><textarea class="form-control" name="description" rows="5" required placeholder="Jelaskan kondisi barang, kelengkapan..."></textarea></div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3"><label class="form-label fw-semibold">Harga Sewa / Hari (Rp)</label><div class="input-group"><span class="input-group-text bg-light border-end-0">Rp</span><input type="number" class="form-control border-start-0" name="price_daily" required min="1000" step="1000"></div></div>
                    <div class="card bg-light border-0 mb-3"><div class="card-body"><label class="form-label fw-semibold text-success">Foto Utama Barang</label><input type="file" class="form-control mb-2" name="primary_image" accept="image/*" required><small class="text-muted">Format JPG/PNG max 2MB.</small></div></div>
                </div>
            </div>
            <hr class="text-muted">
            <div class="d-flex justify-content-end mt-3"><a href="<?= BASEURL; ?>/useritem/index" class="btn btn-outline-secondary me-2">Batal</a><button type="submit" class="btn btn-success fw-semibold"><i class="fas fa-save me-1"></i> Sewakan</button></div>
        </form>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>