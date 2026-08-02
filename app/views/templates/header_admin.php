<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Admin Panel'; ?> - RentalMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif !important; background-color: #f4f6f9 !important; }
        .sidebar { width: 240px; min-height: 100vh; background-color: #4f46e5; position: fixed; top: 0; left: 0; z-index: 1030; }
        .sidebar .brand { padding: 20px; color: #fff; font-weight: 700; font-size: 1.2rem; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.15); display: block; }
        .sidebar .nav-link { color: #aeb9c7; padding: 12px 20px; border-radius: 8px; margin: 4px 12px; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #4338ca; color: #fff; }
        .admin-content { margin-left: 240px; padding: 24px; min-height: 100vh; }
        .card { border: none !important; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important; border-radius: 12px !important; }
    </style>
    <link rel="stylesheet" href="<?= BASEURL; ?>/assets/css/style.css">
</head>
<body>
    <nav class="sidebar">
        <a href="<?= BASEURL; ?>/admin/dashboard" class="brand"><i class="fas fa-tools me-2 text-warning"></i>RentalMarket</a>
        <ul class="nav flex-column mt-2">
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Dashboard Admin' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/dashboard"><i class="fas fa-chart-line fa-fw me-2"></i> Dashboard</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Manajemen Transaksi' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/bookings"><i class="fas fa-receipt fa-fw me-2"></i> Transaksi</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Verifikasi Pembayaran' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/payments"><i class="fas fa-credit-card fa-fw me-2"></i> Pembayaran</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Manajemen Pengguna' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/users"><i class="fas fa-users fa-fw me-2"></i> Pengguna</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Manajemen Barang' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/items"><i class="fas fa-boxes fa-fw me-2"></i> Barang</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Manajemen Kategori' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/categories"><i class="fas fa-tags fa-fw me-2"></i> Kategori</a></li>
            <li><a class="nav-link <?= ($data['title'] ?? '') == 'Log Aktivitas Admin' ? 'active' : ''; ?>" href="<?= BASEURL; ?>/admin/logs"><i class="fas fa-clipboard-list fa-fw me-2"></i> Log</a></li>
        </ul>
        <div class="mt-auto p-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
            <a class="nav-link text-danger" href="<?= BASEURL; ?>/auth/logout"><i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout</a>
        </div>
    </nav>

    <div class="admin-content">
        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['flash_warning'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert"><?= $_SESSION['flash_warning']; unset($_SESSION['flash_warning']); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>