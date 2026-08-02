<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Log Aktivitas Admin</h4>
        <span class="text-muted small">Menampilkan data log terakhir</span>
    </div>
    <form method="GET" action="<?= BASEURL; ?>/admin/logs" class="d-flex gap-2">
        <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari admin/aksi/target...">
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Log Aktivitas Admin</h4>
    <span class="text-muted small">Menampilkan 200 aksi terakhir</span>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Waktu</th><th>Admin</th><th>Aksi</th><th>Target</th><th>ID</th><th>Catatan</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($data['logs'])): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada log.</td></tr>
                    <?php else: foreach($data['logs'] as $log): ?>
                        <tr>
                            <td class="small text-muted"><?= date('d M Y H:i', strtotime($log['created_at'])); ?></td>
                            <td><?= htmlspecialchars($log['admin_name']); ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($log['action']); ?></span></td>
                            <td><?= htmlspecialchars($log['target_type'] ?? '-'); ?></td>
                            <td><?= $log['target_id'] ?? '-'; ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($log['note'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>