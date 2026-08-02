<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-danger border-4">
    <h4 class="m-0 fw-bold text-dark"><i class="far fa-heart me-2 text-danger"></i> Wishlist Saya</h4>
</div>

<?php if(empty($data['items'])): ?>
    <div class="empty-state bg-white rounded border"><i class="far fa-heart"></i><h6 class="fw-bold text-dark">Belum ada tersimpan</h6><p>Suka barang? Tap hati untuk menyimpannya di sini.</p><a href="<?= BASEURL; ?>/home/explore" class="btn btn-brand">Jelajahi Katalog</a></div>
<?php else: ?>
<div class="row g-4">
    <?php foreach($data['items'] as $item): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card h-100">
                <div class="thumb">
                    <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>"><img loading="lazy" src="<?= BASEURL; ?>/assets/uploads/items/<?= $item['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($item['name']); ?>"></a>
                    <a href="<?= BASEURL; ?>/user/wishlist/remove/<?= $item['id']; ?>" class="fav active" title="Hapus"><i class="fas fa-heart"></i></a>
                </div>
                <div class="body d-flex flex-column">
                    <span class="badge bg-light text-secondary mb-2 align-self-start border" style="font-weight:600;"><?= htmlspecialchars($item['category_name'] ?? ''); ?></span>
                    <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>" class="name text-dark mb-2"><?= htmlspecialchars($item['name']); ?></a>
                    <div class="rating-stars mb-2"><?= str_repeat('★', round((float)($item['avg_rating'] ?? 0))); ?><?= str_repeat('☆', 5 - round((float)($item['avg_rating'] ?? 0))); ?> <span class="small text-muted">(<?= (int)($item['total_reviews'] ?? 0); ?>)</span></div>
                    <div class="mt-auto d-flex justify-content-between align-items-end">
                        <div class="price">Rp <?= number_format($item['price_daily'], 0, ',', '.'); ?> <small>/hari</small></div>
                 
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>