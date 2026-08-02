<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-primary border-4">
    <h4 class="m-0 fw-bold text-dark"><i class="fas fa-shopping-cart me-2"></i> Keranjang Sewa</h4>
</div>

<?php if(empty($data['items'])): ?>
    <div class="empty-state bg-white rounded border"><i class="fas fa-shopping-cart"></i><h6 class="fw-bold text-dark">Keranjang kosong</h6><p>Belum ada barang yang kamu pilih untuk disewa.</p><a href="<?= BASEURL; ?>/home/explore" class="btn btn-brand">Jelajahi Katalog</a></div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-8">
        <?php foreach($data['items'] as $it): ?>
            <div class="cart-item mb-3">
                <img src="<?= BASEURL; ?>/assets/uploads/items/<?= $it['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($it['name']); ?>">
                <div class="flex-grow-1">
                    <a href="<?= BASEURL; ?>/item/detail/<?= $it['slug']; ?>" class="fw-bold text-dark text-decoration-none"><?= htmlspecialchars($it['name']); ?></a>
                    <div class="text-muted small">Rp <?= number_format($it['daily_price'],0,',','.'); ?> / hari &middot; <?= $it['duration']; ?> hari</div>
                    <div class="text-muted small"><?= $it['start']; ?> s/d <?= $it['end']; ?></div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-brand mb-2">Rp <?= number_format($it['total_price']+5000,0,',','.'); ?></div>
                    <a href="<?= BASEURL; ?>/cart/remove/<?= $it['item_id']; ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top:84px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Ringkasan</h6>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal Sewa</span><span>Rp <?= number_format($data['total']-count($data['items'])*5000,0,',','.'); ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Biaya Admin</span><span>Rp <?= number_format(count($data['items'])*5000,0,',','.'); ?></span></div>
                <hr>
                <div class="d-flex justify-content-between mb-3"><span class="fw-bold">Total</span><span class="fw-bold text-brand fs-5">Rp <?= number_format($data['total'],0,',','.'); ?></span></div>
                <a href="<?= BASEURL; ?>/cart/checkout" class="btn btn-brand w-100 fw-bold py-2">Checkout & Pesan</a>
                <a href="<?= BASEURL; ?>/home/explore" class="btn btn-outline-secondary w-100 mt-2 btn-sm">Lanjut Belanja</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>
