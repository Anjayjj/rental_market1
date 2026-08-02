<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-warning border-4">
    <h4 class="m-0 fw-bold text-dark"><i class="fas fa-edit me-2"></i> Edit Barang</h4>
    <a href="<?= BASEURL; ?>/useritem/index" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-4">
        
        <!-- Form mengarah ke fungsi update dengan ID barang tersebut -->
        <form action="<?= BASEURL; ?>/useritem/update/<?= $data['item']['id']; ?>" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($data['item']['name']); ?>" required>
                </div>
                
                <div class="col-md-6 mt-3 mt-md-0">
                    <label for="category_id" class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="" disabled>Pilih Kategori</option>
                        <?php foreach($data['categories'] as $cat): ?>
                            <!-- Logika ini akan otomatis memilih (select) kategori lama barang tersebut -->
                            <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $data['item']['category_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="price_daily" class="form-label fw-bold">Harga Sewa per Hari (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price_daily" name="price_daily" value="<?= $data['item']['price_daily']; ?>" required min="0">
                </div>
                
                <div class="col-md-6 mt-3 mt-md-0">
                    <label for="primary_image" class="form-label fw-bold">Ubah Gambar Utama (Opsional)</label>
                    <input type="file" class="form-control" id="primary_image" name="primary_image" accept="image/jpeg, image/png, image/webp">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    
                    <!-- Menampilkan gambar yang saat ini tersimpan di database -->
                    <?php if(!empty($data['item']['cover_image'])): ?>
                        <div class="mt-2 p-2 border rounded d-inline-block bg-light">
                            <small class="d-block text-muted mb-1">Gambar saat ini:</small>
                            <img src="<?= BASEURL; ?>/assets/uploads/items/<?= $data['item']['cover_image']; ?>" alt="Gambar Lama" class="img-thumbnail" style="max-height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-bold">Deskripsi Barang <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($data['item']['description']); ?></textarea>
            </div>

            <hr class="text-muted">
            
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="<?= BASEURL; ?>/useritem/index" class="btn btn-light border fw-bold text-dark">Batal</a>
                <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>