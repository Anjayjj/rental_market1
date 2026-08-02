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
    <style>
        body { background-color: #f5f6fa !important; }
        #sidebar { width: 250px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(180deg,#4f46e5,#4338ca); z-index: 1030; display:flex; flex-direction:column; }
        .sidebar-brand { padding:18px 20px; font-weight:800; font-size:1.2rem; color:#fff; text-decoration:none; border-bottom:1px solid rgba(255,255,255,.15); }
        .nav-sidebar .nav-link { color:rgba(255,255,255,.8); padding:11px 18px; margin:4px 12px; border-radius:10px; font-weight:600; }
        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.15); }
        #main-content { margin-left:250px; width: calc(100% - 250px); min-height:100vh; display:flex; flex-direction:column; }
        .top-navbar { background:#fff; padding:14px 28px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e9edf3; }
        .btn-primary { background-color:#4f46e5 !important; border-color:#4f46e5 !important; }
        .btn-primary:hover { background-color:#4338ca !important; border-color:#4338ca !important; }
        .text-primary { color:#4f46e5 !important; }
        .bg-primary { background-color:#4f46e5 !important; }
        .border-primary { border-color:#4f46e5 !important; }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar">
        <a href="<?= BASEURL; ?>" class="sidebar-brand d-flex align-items-center"><i class="fas fa-box-open me-2"></i> RentalMarket</a>
        <div class="px-3 pt-3"><small class="text-uppercase fw-bold" style="font-size:.7rem; color:rgba(255,255,255,.6);">Menu Utama</small></div>
        <ul class="nav nav-sidebar flex-column mb-auto">
            <li><a href="<?= BASEURL; ?>/user/dashboard" class="nav-link menu-link"><i class="fas fa-border-all fa-fw me-2"></i> Dashboard</a></li>
            <li><a href="<?= BASEURL; ?>/useritem/index" class="nav-link menu-link"><i class="fas fa-boxes fa-fw me-2"></i> Barang Saya</a></li>
            <li><a href="<?= BASEURL; ?>/booking/masuk" class="nav-link <?= (isset($data['title']) && $data['title'] == 'Pesanan Masuk') ? 'active' : ''; ?>">
    <i class="fas fa-inbox"></i>
    <span>Pesanan Masuk</span>
</a></li>
            <li><a href="<?= BASEURL; ?>/useritem/create" class="nav-link menu-link"><i class="fas fa-plus-circle fa-fw me-2"></i> Mulai Sewakan</a></li>
            <li><a href="<?= BASEURL; ?>/booking/saya" class="nav-link menu-link"><i class="fas fa-receipt fa-fw me-2"></i> Riwayat Sewa</a></li>
            <li><a href="<?= BASEURL; ?>/user/wishlist" class="nav-link menu-link"><i class="far fa-heart fa-fw me-2"></i> Wishlist</a></li>
            <li><a href="<?= BASEURL; ?>/cart" class="nav-link menu-link"><i class="fas fa-shopping-cart me-2"></i> Keranjang</a></li>
        </ul>
        <div class="px-3 py-3" style="border-top:1px solid rgba(255,255,255,.15);">
            <ul class="nav nav-sidebar flex-column">
                <li><a href="<?= BASEURL; ?>/user/settings" class="nav-link menu-link"><i class="fas fa-cog fa-fw me-2"></i> Pengaturan</a></li>
                <li><a href="<?= BASEURL; ?>/auth/logout" class="nav-link text-white-50 mt-2"><i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div id="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASEURL; ?>" class="btn btn-sm btn-outline-brand" title="Kembali ke Beranda"><i class="fas fa-home me-1"></i> Beranda</a>
                <h5 class="mb-0 fw-bold text-dark">Portal Member</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                    <div class="text-end me-3 d-none d-md-block">
                        <span class="d-block fw-bold text-dark" style="font-size:.9rem;"><?= $_SESSION['user_name']; ?></span>
                        <span class="d-block text-muted" style="font-size:.75rem;"><?= $_SESSION['user_role'] == 'admin' ? 'Administrator' : 'Member'; ?></span>
                    </div>
                    <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $_SESSION['user_avatar'] ?? 'default.png'; ?>" class="rounded-circle object-fit-cover shadow-sm" style="width:42px;height:42px;border:2px solid #e9edf3;">
                </a>
            </div>
        </div>
        </div>
        <div class="p-4 p-md-5 flex-grow-1">
