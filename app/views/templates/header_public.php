<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'RentalMarket'; ?> | RentalMarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>/assets/css/style.css">
</head>
<body>

<!-- Top utility bar -->
<div class="topbar py-1">
    <div class="container d-flex justify-content-between">
        <span><i class="fas fa-phone-alt me-1"></i> Bantuan: 0858-4582-6761 | <i class="fas fa-truck-fast me-1"></i> Pengiriman & pengambilan fleksibel</span>
        <span>
            <?php if(isset($_SESSION['user_id'])): ?>
                <i class="fas fa-circle text-success me-1" style="font-size:.5rem;"></i> Hai, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
            <?php else: ?>
                <a href="<?= BASEURL; ?>/auth/login"><i class="fas fa-sign-in-alt me-1"></i> Masuk</a> atau <a href="<?= BASEURL; ?>/auth/register">Daftar</a>
            <?php endif; ?>
        </span>
    </div>
</div>

<!-- Main navbar -->
<nav class="site-nav py-2">
    <div class="container">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="<?= BASEURL; ?>" class="nav-logo text-nowrap"><i class="fas fa-box-open me-1"></i>RentalMarket</a>

            <form action="<?= BASEURL; ?>/home/explore" method="GET" class="search-wrap d-flex flex-grow-1 mx-lg-3">
                <span class="input-group">
                    <!-- Input Nama Barang -->
                    <input type="text" name="search" class="form-control border-end-0" placeholder="Cari kamera, tenda..." value="<?= htmlspecialchars($_GET['search'] ?? ''); ?>">
                    
                    <!-- Input Lokasi Baru -->
                    <input type="text" name="lokasi" class="form-control border-start" style="max-width: 140px; background-color: #f8f9fa;" placeholder="Lokasi..." value="<?= htmlspecialchars($_GET['lokasi'] ?? ''); ?>">
                    
                    <button class="btn btn-brand px-3" type="submit"><i class="fas fa-search"></i></button>
                </span>
            </form>

            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASEURL; ?>/user/wishlist" class="nav-icon" title="Wishlist"><i class="far fa-heart"></i></a>
                <a href="<?= BASEURL; ?>/cart" class="nav-icon" title="Keranjang Sewa">
                    <i class="fas fa-shopping-cart"></i>
                    <?php $cartCount = count($_SESSION['cart'] ?? []); if($cartCount > 0): ?><span class="badge-dot"><?= $cartCount; ?></span><?php endif; ?>
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASEURL; ?>/user/dashboard" class="nav-link d-flex align-items-center" title="Masuk ke Dashboard">
                            <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $_SESSION['user_avatar'] ?? 'default.png'; ?>" class="rounded-circle object-fit-cover" style="width:38px;height:38px;border:2px solid #e9edf3;">
                        </a>
                <?php else: ?>
                    <a href="<?= BASEURL; ?>/auth/login" class="btn btn-outline-brand btn-sm px-3 fw-semibold">Masuk</a>
                <?php endif; ?>
                <a href="<?= BASEURL; ?>/useritem/create" class="btn btn-brand btn-sm px-3 fw-semibold d-none d-md-inline-flex"><i class="fas fa-plus me-1" style="margin-top: 4px;"></i> Sewa</a>
            </div>
            <!-- Tombol Home -->
<a href="<?= BASEURL; ?>" class="text-gray-600 hover:text-purple-600 rounded-full p-2">
    <i class="fas fa-home text-xl"></i>
</a>
        </div>

        <!-- Category bar -->
        <div class="d-flex align-items-center gap-2 mt-2 mb-3 pt-2 pb-1 overflow-auto">
            <a href="<?= BASEURL; ?>/home/explore" class="cat-pill <?= empty($_GET['category']) ? 'active' : ''; ?>">Semua</a>
            <?php if(isset($data['categories'])): foreach($data['categories'] as $c): ?>
                <a href="<?= BASEURL; ?>/home/explore?category=<?= $c['id']; ?>" class="cat-pill <?= (($_GET['category'] ?? '') == $c['id']) ? 'active' : ''; ?>">
                    <i class="<?= htmlspecialchars($c['icon'] ?? 'fas fa-tag'); ?> me-1"></i><?= htmlspecialchars($c['name']); ?>
                </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</nav>

<div class="container py-4">
