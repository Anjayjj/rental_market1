<?php require_once VIEWPATH . '/templates/header_public.php'; ?>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- HERO -->
<section class="hero p-4 p-lg-5 mb-5">
    <div class="hero-inner row align-items-center">
        <div class="col-lg-7">
            <span class="badge bg-white text-brand mb-3 px-3 py-2 rounded-pill fw-semibold"><i class="fas fa-sparkles me-1"></i> Platform Sewa P2P #1 di Indonesia</span>
            <h1 class="display-4 fw-bold mb-3">Sewa apa pun,<br>Kapan pun, Di mana pun.</h1>
            <p class="lead mb-4 opacity-75">Dari kamera profesional, peralatan camping, hingga kendaraan — tersedia untuk disewa dengan harga terjangkau dan proses yang mudah.</p>
            <form action="<?= BASEURL; ?>/home/explore" method="GET" class="search-hero mb-3">
                <i class="fas fa-search text-muted ms-2"></i>
                <input type="text" name="search" class="form-control" placeholder="Mau sewa apa hari ini?">
                <button class="btn btn-brand px-4 fw-semibold" type="submit">Cari</button>
            </form>
            <div class="small opacity-75"><i class="fas fa-shield-alt me-1"></i> Transaksi aman &nbsp;·&nbsp; <i class="fas fa-badge-check me-1"></i> Penyedia terverifikasi &nbsp;·&nbsp; <i class="fas fa-headset me-1"></i> Bantuan 24/7</div>
        </div>
        <div class="col-lg-5 d-none d-lg-block position-relative" style="min-height:320px;">
            <div class="float-badge" style="top:20px; right:10px;">
                <i class="fas fa-box fa-lg text-warning"></i>
                <div><div style="font-weight:700"><?= max(0, (int)($data['stats']['items'] ?? 0) - 5); ?>+ barang</div><div class="small opacity-75">siap sewa</div></div>
            </div>
            <div class="float-badge" style="bottom:30px; right:60px; animation: floaty2 6s ease-in-out infinite;">
                <i class="fas fa-star text-warning"></i>
                <div><div style="font-weight:700">4.9/5</div><div class="small opacity-75">rating penyewa</div></div>
            </div>
            <i class="fas fa-box-open" style="font-size:10rem; opacity:.22; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);"></i>
        </div>
    </div>
</section>

<!-- STATS (data riil) -->
<section class="mb-5">
    <div class="row g-3 text-center">
        <div class="col-6 col-lg-3 reveal"><div class="stat-card"><div class="num" data-count="<?= (int)($data['stats']['items'] ?? 0); ?>">0</div><div class="small text-muted">Barang tersedia</div></div></div>
        <div class="col-6 col-lg-3 reveal"><div class="stat-card"><div class="num" data-count="<?= (int)($data['stats']['users'] ?? 0); ?>">0</div><div class="small text-muted">Penyewa terdaftar</div></div></div>
        <div class="col-6 col-lg-3 reveal"><div class="stat-card"><div class="num" data-count="<?= (int)($data['stats']['categories'] ?? 0); ?>">0</div><div class="small text-muted">Kategori</div></div></div>
        <div class="col-6 col-lg-3 reveal"><div class="stat-card"><div class="num" data-count="<?= (int)($data['stats']['bookings_done'] ?? 0); ?>">0</div><div class="small text-muted">Sewa selesai</div></div></div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="mb-5">
    <div class="section-head">
        <div><div class="eyebrow">Kategori</div><h2>Belanja berdasarkan kategori</h2></div>
        <a href="<?= BASEURL; ?>/home/explore" class="text-brand fw-semibold small">Lihat semua <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php if(isset($data['categories'])): foreach($data['categories'] as $c): ?>
            <div class="col-6 col-md-3 reveal">
                <a href="<?= BASEURL; ?>/home/explore?category=<?= $c['id']; ?>" class="cat-tile">
                    <div class="ic"><i class="<?= htmlspecialchars($c['icon'] ?? 'fas fa-tag'); ?>"></i></div>
                    <div class="fw-bold"><?= htmlspecialchars($c['name']); ?></div>
                </a>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<!-- POPULAR ITEMS -->
<section class="mb-5">
    <div class="section-head">
        <div><div class="eyebrow">Paling laris</div><h2>Barang Populer</h2></div>
    </div>
    <div class="row g-4">
        <?php if(empty($data['items'])): ?>
            <div class="col-12 empty-state"><i class="fas fa-box-open"></i><p>Belum ada barang tersedia.</p></div>
        <?php else: foreach($data['items'] as $item): ?>
            <div class="col-6 col-md-4 col-lg-3 reveal">
                <div class="product-card h-100">
                    <div class="thumb">
                        <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>">
                            <img loading="lazy" src="<?= BASEURL; ?>/assets/uploads/items/<?= $item['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                        </a>
                        <button class="fav" data-item="<?= $item['id']; ?>" title="Wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="body d-flex flex-column">
                        <span class="badge bg-light text-secondary mb-2 align-self-start border" style="font-weight:600;"><?= htmlspecialchars($item['category_name']); ?></span>
                        <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>" class="name text-dark mb-2"><?= htmlspecialchars($item['name']); ?></a>
                        <div class="rating-stars mb-2"><?= str_repeat('★', round((float)($item['avg_rating'] ?? 0))); ?><?= str_repeat('☆', 5 - round((float)($item['avg_rating'] ?? 0))); ?> <span class="small text-muted">(<?= (int)($item['total_reviews'] ?? 0); ?>)</span></div>
                        <div class="mt-auto d-flex justify-content-between align-items-end">
                            <div class="price">Rp <?= number_format($item['price_daily'], 0, ',', '.'); ?> <small>/hari</small></div>
                       
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<!-- BARANG LAINNYA -->
<section class="mb-5">
    <div class="section-head">
        <div><div class="eyebrow">Masih banyak pilihan</div><h2>Barang Lainnya</h2></div>
        <a href="<?= BASEURL; ?>/home/explore" class="text-brand fw-semibold small">Lihat semua <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        <?php if(empty($data['more_items'])): ?>
            <div class="col-12 empty-state"><i class="fas fa-box-open"></i><p>Belum ada barang lainnya.</p></div>
        <?php else: foreach($data['more_items'] as $item): ?>
            <div class="col-6 col-md-4 col-lg-3 reveal">
                <div class="product-card h-100">
                    <div class="thumb">
                        <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>">
                            <img loading="lazy" src="<?= BASEURL; ?>/assets/uploads/items/<?= $item['cover_image'] ?? 'default.jpg'; ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                        </a>
                        <button class="fav" data-item="<?= $item['id']; ?>" title="Wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="body d-flex flex-column">
                        <span class="badge bg-light text-secondary mb-2 align-self-start border" style="font-weight:600;"><?= htmlspecialchars($item['category_name']); ?></span>
                        <a href="<?= BASEURL; ?>/item/detail/<?= $item['slug']; ?>" class="name text-dark mb-2"><?= htmlspecialchars($item['name']); ?></a>
                        <div class="rating-stars mb-2"><?= str_repeat('★', round((float)($item['avg_rating'] ?? 0))); ?><?= str_repeat('☆', 5 - round((float)($item['avg_rating'] ?? 0))); ?> <span class="small text-muted">(<?= (int)($item['total_reviews'] ?? 0); ?>)</span></div>
                        <div class="mt-auto d-flex justify-content-between align-items-end">
                            <div class="price">Rp <?= number_format($item['price_daily'], 0, ',', '.'); ?> <small>/hari</small></div>
                        
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="mb-5">
    <div class="section-head"><div><div class="eyebrow">Mudah</div><h2>Cara menyewa di RentalMarket</h2></div></div>
    <div class="row g-4">
        <div class="col-md-4 reveal"><div class="step-box"><div class="num">1</div><h6 class="fw-bold">Cari & Pilih</h6><p class="text-muted small mb-0">Temukan barang yang kamu butuhkan dan pilih tanggal sewa.</p></div></div>
        <div class="col-md-4 reveal"><div class="step-box"><div class="num">2</div><h6 class="fw-bold">Pesan & Bayar</h6><p class="text-muted small mb-0">Ajukan sewa, upload bukti pembayaran, tunggu verifikasi.</p></div></div>
        <div class="col-md-4 reveal"><div class="step-box"><div class="num">3</div><h6 class="fw-bold">Gunakan & Kembalikan</h6><p class="text-muted small mb-0">Ambil barang, gunakan, lalu kembalikan dengan mudah.</p></div></div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="mb-5">
    <div class="section-head"><div><div class="eyebrow">Testimoni</div><h2>Apa kata penyewa kami</h2></div></div>
    <div class="row g-4">
        <div class="col-md-4 reveal"><div class="testi-card"><i class="fas fa-quote-left quote"></i><p class="small mb-3">"Sewa kamera di sini gampang banget, barangnya bersih dan owner ramah. Bakal langganan terus!"</p><div class="d-flex align-items-center gap-2"><div class="avatar">RA</div><div><div class="fw-bold small">Rina A.</div><div class="small text-muted">Jakarta</div></div></div></div></div>
        <div class="col-md-4 reveal"><div class="testi-card"><i class="fas fa-quote-left quote"></i><p class="small mb-3">"Butuh tenda buat hike mendadak, 10 menit langsung dapet. Harga jauh lebih murah dari beli."</p><div class="d-flex align-items-center gap-2"><div class="avatar">BS</div><div><div class="fw-bold small">Bayu S.</div><div class="small text-muted">Bandung</div></div></div></div></div>
        <div class="col-md-4 reveal"><div class="testi-card"><i class="fas fa-quote-left quote"></i><p class="small mb-3">"Mobil buat liburan keluarga lancar jaya. Adminnya responsif pas ada kendala kecil."</p><div class="d-flex align-items-center gap-2"><div class="avatar">DP</div><div><div class="fw-bold small">Dewi P.</div><div class="small text-muted">Surabaya</div></div></div></div></div>
    </div>
</section>

<!-- BRAND STRIP -->
<section class="mb-5">
    <div class="text-center small text-muted mb-3">Dipercaya komunitas & mitra</div>
    <div class="brand-strip">
        <i class="fab fa-nintendo-switch"></i><i class="fab fa-apple"></i><i class="fab fa-android"></i>
        <i class="fab fa-flickr"></i><i class="fab fa-github"></i><i class="fab fa-youtube"></i>
    </div>
</section>

<!-- CTA menjadi penyedia -->
<section class="hero p-5 text-center mb-2 reveal">
    <h2 class="fw-bold mb-2">Punya barang menganggur?</h2>
    <p class="lead mb-4 opacity-75">Jadikan barangmu sumber penghasilan. Pasang iklan sewa dalam 2 menit, gratis!</p>
    <a href="<?= BASEURL; ?>/useritem/create" class="btn btn-light-soft btn-lg fw-bold px-4" style="position: relative; z-index: 10;">
    <i class="fas fa-plus me-1"></i> Mulai Sewakan Sekarang
</a>
</section>

<script>
document.querySelectorAll('.num[data-count]').forEach(function(el){
    var target = parseInt(el.getAttribute('data-count'), 10);
    var dur = 1400, start = null;
    function step(ts){
        if(!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var val = Math.floor((1 - Math.pow(1 - p, 3)) * target);
        el.textContent = val.toLocaleString('id-ID');
        if(p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
});
</script>

<?php require_once VIEWPATH . '/templates/footer_public.php'; ?>