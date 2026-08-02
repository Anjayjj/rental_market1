<?php require_once VIEWPATH . '/templates/header_public.php'; ?>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="<?= BASEURL; ?>" class="text-muted">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/home/explore" class="text-muted">Katalog</a></li>
        <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($data['item']['name']); ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- Gallery -->
    <div class="col-lg-6">
        <div class="gallery-main" id="galleryMain">
            <?php if(empty($data['images'])): ?>
                <img src="<?= BASEURL; ?>/assets/uploads/items/default.jpg" id="mainImg" alt="default">
            <?php else: ?>
                <img src="<?= BASEURL; ?>/assets/uploads/items/<?= $data['images'][0]['image_path']; ?>" id="mainImg" alt="<?= htmlspecialchars($data['item']['name']); ?>">
            <?php endif; ?>
        </div>
        <div class="gallery-thumbs">
            <?php if(!empty($data['images'])): foreach($data['images'] as $img): ?>
                <img src="<?= BASEURL; ?>/assets/uploads/items/<?= $img['image_path']; ?>" class="thumb-img <?= $img===reset($data['images'])?'active':''; ?>" onclick="swapImg(this)" onmouseenter="swapImg(this)">
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Info + booking -->
    <div class="col-lg-6">
        <div class="buy-box">
            <span class="badge badge-soft mb-2"><i class="<?= htmlspecialchars($data['item']['category_icon'] ?? 'fas fa-tag'); ?> me-1"></i><?= htmlspecialchars($data['item']['category_name']); ?></span>
            <h1 class="fw-bold mb-2" style="font-size:1.9rem;"><?= htmlspecialchars($data['item']['name']); ?></h1>

            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="rating-stars"><?= str_repeat('★', round((float)($data['rating']['avg_rating'] ?? 0))); ?><?= str_repeat('☆', 5 - round((float)($data['rating']['avg_rating'] ?? 0))); ?></span>
                <small class="text-muted"><?= number_format((float)($data['rating']['avg_rating'] ?? 0), 1, ',', '.'); ?> (<?= (int)($data['rating']['total_reviews'] ?? 0); ?> ulasan)</small>
            </div>

            <div class="d-flex align-items-baseline gap-2 mb-3">
                <span class="fs-3 fw-bold text-brand">Rp <?= number_format($data['item']['price_daily'], 0, ',', '.'); ?></span>
                <span class="text-muted">/ hari</span>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <?php require_once VIEWPATH . '/components/form_booking.php'; ?>
                </div>
            </div>

            <div class="seller-card d-flex align-items-center gap-3 border rounded p-3">
                <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $data['item']['owner_avatar'] ?? 'default.png'; ?>" alt="">
                <div>
                    <small class="text-muted d-block" style="font-size:.72rem;letter-spacing:.5px;text-transform:uppercase;">Pemilik Terverifikasi</small>
                    <strong><?= htmlspecialchars($data['item']['owner_name']); ?></strong>
                </div>
                <!-- INI KODE UNTUK MENAMPILKAN ALAMAT -->
        <span class="d-block text-muted small">
            <i class="fas fa-map-marker-alt text-danger me-1"></i> 
            <?= !empty($data['item']['owner_address']) ? htmlspecialchars($data['item']['owner_address']) : 'Alamat belum diatur'; ?>
        </span>
        <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $data['item']['owner_id']): ?>
    <a href="<?= BASEURL; ?>/chat/start/<?= $data['item']['id']; ?>/<?= $data['item']['owner_id']; ?>" class="btn btn-outline-brand btn-sm ms-auto">
        <i class="far fa-comment-dots me-1"></i> Chat Penjual
    </a>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: deskripsi / ulasan -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <ul class="nav nav-pills border-0 mb-1" id="dtab" role="tablist">
            <li class="nav-item"><button class="nav-link active fw-semibold border-0" data-bs-toggle="tab" data-bs-target="#deskripsi">Deskripsi</button></li>
            <li class="nav-item"><button class="nav-link fw-semibold text-muted border-0" data-bs-toggle="tab" data-bs-target="#ulasan">Ulasan (<?= (int)($data['rating']['total_reviews'] ?? 0); ?>)</button></li>
        </ul>
        <div class="tab-content pt-3">
            <div class="tab-pane fade show active text-muted" id="deskripsi" style="line-height:1.8;"><?= nl2br(htmlspecialchars($data['item']['description'])); ?></div>
            <div class="tab-pane fade" id="ulasan">
                <?php if(empty($data['reviews'])): ?>
                    <div class="empty-state"><div class="empty-art"><i class="fas fa-comment-slash"></i></div><p>Belum ada ulasan.</p></div>
                <?php else: foreach($data['reviews'] as $r): ?>
                    <div class="d-flex mb-3 pb-3 border-bottom">
                        <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $r['user_avatar'] ?? 'default.png'; ?>" class="rounded-circle me-3 border object-fit-cover" style="width:44px;height:44px;">
                        <div>
                            <div class="d-flex align-items-center gap-2"><strong class="text-dark"><?= htmlspecialchars($r['user_name']); ?></strong><span class="rating-stars small"><?= str_repeat('★',(int)$r['rating']).str_repeat('☆',5-(int)$r['rating']); ?></span></div>
                            <p class="text-muted small mb-0 mt-1"><?= nl2br(htmlspecialchars($r['comment'] ?? '')); ?></p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Related -->
<?php if(!empty($data['related'])): ?>
<section class="mt-5">
    <div class="section-head"><div><div class="eyebrow">Rekomendasi</div><h2>Barang serupa</h2></div></div>
    <div class="row g-4">
        <?php foreach($data['related'] as $rel): ?>
            <div class="col-6 col-md-3">
                <div class="product-card h-100">
                    <div class="thumb"><a href="<?= BASEURL; ?>/item/detail/<?= $rel['slug']; ?>"><img loading="lazy" src="<?= BASEURL; ?>/assets/uploads/items/<?= $rel['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($rel['name']); ?>"></a></div>
                    <div class="body d-flex flex-column">
                        <span class="badge bg-light text-secondary mb-2 align-self-start border" style="font-weight:600;"><?= htmlspecialchars($rel['category_name']); ?></span>
                        <a href="<?= BASEURL; ?>/item/detail/<?= $rel['slug']; ?>" class="name text-dark mb-2"><?= htmlspecialchars($rel['name']); ?></a>
                        <div class="mt-auto price">Rp <?= number_format($rel['price_daily'], 0, ',', '.'); ?> <small>/hari</small></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once VIEWPATH . '/templates/footer_public.php'; ?>

<script>
function swapImg(el){ document.getElementById('mainImg').src = el.src; document.querySelectorAll('.thumb-img').forEach(i=>i.classList.remove('active')); el.classList.add('active'); }
document.getElementById('galleryMain')?.addEventListener('click', function(){ this.classList.toggle('zoomed'); });
</script>
