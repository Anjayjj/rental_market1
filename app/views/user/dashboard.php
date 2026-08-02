<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex align-items-center mb-4 bg-white p-4 rounded shadow-sm border-start border-primary border-4">
    <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $_SESSION['user_avatar'] ?? 'default.png'; ?>" class="rounded-circle shadow-sm me-4 border object-fit-cover" style="width: 72px; height: 72px;">
    <div>
        <h3 class="mb-1 fw-bold text-dark">Halo, <?= explode(' ', $_SESSION['user_name'])[0]; ?>! 👋</h3>
        <span class="text-muted"><i class="fas fa-calendar-alt me-2 text-brand"></i> <?= date('d F Y'); ?> &middot; Kelola sewa & barangmu di satu tempat.</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3"><div class="nav-icon" style="background:var(--brand-soft);color:var(--brand);"><i class="fas fa-boxes"></i></div><div><div class="text-muted small">Barang Disewakan</div><div class="h4 fw-bold mb-0"><?= (int)$data['stats']['items']; ?></div></div></div></div></div>
    <div class="col-md-4"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3"><div class="nav-icon" style="background:#ecfdf5;color:#16a34a;"><i class="fas fa-receipt"></i></div><div><div class="text-muted small">Sedang Disewa</div><div class="h4 fw-bold mb-0"><?= (int)$data['stats']['rented']; ?></div></div></div></div></div>
    <div class="col-md-4"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3"><div class="nav-icon" style="background:#fef3c7;color:#f59e0b;"><i class="fas fa-check-circle"></i></div><div><div class="text-muted small">Sewa Selesai</div><div class="h4 fw-bold mb-0"><?= (int)$data['stats']['done']; ?></div></div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h6 class="m-0 fw-bold text-dark">Pesanan Terbaru</h6><a href="<?= BASEURL; ?>/booking/saya" class="btn btn-sm btn-outline-brand">Lihat Semua</a></div>
            <div class="card-body p-0">
                <?php if(empty($data['my_bookings'])): ?>
                    <div class="empty-state"><i class="fas fa-receipt"></i><p>Belum ada pesanan.</p></div>
                <?php else: $n=0; foreach($data['my_bookings'] as $b): if($n++>=5) break; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                        <div><div class="fw-semibold"><?= htmlspecialchars($b['item_name']); ?></div><small class="text-muted"><?= $b['invoice_no']; ?> &middot; <?= $b['start_date']; ?> s/d <?= $b['end_date']; ?></small></div>
                        <span class="badge rounded-pill <?= $b['status']=='completed'?'bg-success':($b['status']=='active'?'bg-primary':($b['status']=='pending'?'bg-warning text-dark':'bg-secondary')); ?>"><?= ucfirst($b['status']); ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h6 class="m-0 fw-bold text-dark">Barang Saya</h6><a href="<?= BASEURL; ?>/useritem/create" class="btn btn-sm btn-brand"><i class="fas fa-plus"></i> Tambah</a></div>
            <div class="card-body p-0">
                <?php if(empty($data['my_items'])): ?>
                    <div class="empty-state"><i class="fas fa-box-open"></i><p>Belum punya barang.</p></div>
                <?php else: $n=0; foreach($data['my_items'] as $it): if($n++>=5) break; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                        <div class="fw-semibold"><?= htmlspecialchars($it['name']); ?></div>
                        <span class="badge rounded-pill <?= $it['status']=='active'?'bg-success':'bg-warning text-dark'; ?>"><?= ucfirst($it['status']); ?></span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>
