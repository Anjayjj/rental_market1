<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-success border-4">
    <h4 class="m-0 fw-bold text-dark"><i class="fas fa-boxes me-2"></i> Barang Saya</h4>
    <a href="<?= BASEURL; ?>/useritem/create" class="btn btn-brand btn-sm"><i class="fas fa-plus"></i> Tambah Barang</a>
</div>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if(empty($data['items'])): ?>
    <div class="empty-state bg-white rounded border">
        <i class="fas fa-box-open"></i>
        <h6 class="fw-bold text-dark">Belum ada barang</h6>
        <p>Pasang iklan sewa pertama kamu sekarang.</p>
        <a href="<?= BASEURL; ?>/useritem/create" class="btn btn-brand">Tambah Barang</a>
    </div>
<?php else: ?>
<div class="row g-4">
    <?php foreach($data['items'] as $item): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card h-100">
                <div class="thumb">
                    <img src="<?= BASEURL; ?>/assets/uploads/items/<?= $item['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="body d-flex flex-column">
                    <span class="badge <?= $item['status']=='active' ? 'bg-success' : 'bg-warning text-dark'; ?> mb-2 align-self-start">
                        <?= ucfirst($item['status']); ?>
                    </span>
                    <div class="name text-dark mb-2"><?= htmlspecialchars($item['name']); ?></div>
                    
                    <!-- BAGIAN AKSI (HARGA & TOMBOL) YANG DIPERBARUI -->
                    <div class="mt-auto">
                        <div class="price text-primary fw-bold mb-2" style="font-size: 15px;">
                            Rp <?= number_format($item['price_daily'], 0, ',', '.'); ?> <small class="text-muted fw-normal">/hari</small>
                        </div>
                        
                        <div class="d-flex gap-1 w-100 mt-2 border-top pt-2">
                            <!-- Tombol Lihat -->
                            <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug'] ?? $item['id']; ?>" class="btn btn-sm btn-outline-info flex-fill" target="_blank" title="Lihat Detail Barang">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Tombol Edit -->
                            <a href="<?= BASEURL; ?>/useritem/edit/<?= $item['id']; ?>" class="btn btn-sm btn-outline-warning flex-fill" title="Edit Barang">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Tombol Hapus -->
                            <a href="<?= BASEURL; ?>/useritem/delete/<?= $item['id']; ?>" class="btn btn-sm btn-outline-danger flex-fill" onclick="return confirm('Yakin ingin menghapus barang ini?')" title="Hapus Barang">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <!-- ============================================= -->

                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>