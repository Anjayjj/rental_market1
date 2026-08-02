<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="m-0 fw-bold">Manajemen Pengguna</h4>
    <div class="d-flex gap-2">
        <form method="GET" action="<?= BASEURL; ?>/admin/users" class="d-flex gap-2">
            <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari nama/email/no hp...">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
  
</div>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Profil</th><th>Nama</th><th>Email</th><th>No. HP</th><th>Role</th><th>Super Admin</th><th>Daftar</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($data['users'])): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada pengguna terdaftar.</td></tr>
                    <?php else: foreach($data['users'] as $user): ?>
                        <tr>
                            <td><img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $user['avatar'] ?? 'default.png'; ?>" class="rounded-circle border" style="width:40px;height:40px;object-fit:cover;"></td>
                            <td class="fw-bold"><?= htmlspecialchars($user['name']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['phone']); ?></td>
                            <td><span class="badge rounded-pill <?= $user['role']=='admin'?'bg-danger':'bg-secondary'; ?>"><?= ucfirst($user['role']); ?></span></td>
                            <td><?= !empty($user['is_super_admin']) ? '<i class="fas fa-crown text-warning"></i> Ya' : 'Tidak'; ?></td>
                            <td><small class="text-muted"><?= date('d M Y', strtotime($user['created_at'])); ?></small></td>
                            <td class="text-center">
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" action="<?= BASEURL; ?>/admin/toggle_super_admin/<?= $user['id']; ?>" class="d-inline" onsubmit="return confirm('Ubah hak akses super admin untuk user ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="flag" value="<?= empty($user['is_super_admin']) ? '1' : '0'; ?>">
                                    <button class="btn btn-sm <?= !empty($user['is_super_admin']) ? 'btn-outline-warning' : 'btn-outline-secondary'; ?>" title="<?= !empty($user['is_super_admin']) ? 'Cabut Super Admin' : 'Jadikan Super Admin'; ?>">
                                        <i class="fas fa-crown"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?= BASEURL; ?>/admin/delete_user/<?= $user['id']; ?>" class="d-inline ms-1" onsubmit="return confirm('PERINGATAN: Menghapus pengguna ini akan menghapus seluruh data barang dan riwayat transaksinya! Lanjutkan?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>