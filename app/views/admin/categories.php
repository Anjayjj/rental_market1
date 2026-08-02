<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="fw-bold mb-0">Manajemen Kategori</h4>
    <div class="d-flex gap-2">
        <form method="GET" action="<?= BASEURL; ?>/admin/categories" class="d-flex gap-2">
            <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari nama kategori...">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4">

    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#addCat"><i class="fas fa-plus me-1"></i> Tambah Kategori</button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Nama</th><th>Icon</th><th>Slug</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($data['categories'])): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>
                    <?php else: foreach($data['categories'] as $cat): ?>
                        <tr>
                            <td><?= $cat['id']; ?></td>
                            <td><i class="<?= htmlspecialchars($cat['icon'] ?? 'fas fa-tag'); ?> me-1 text-brand"></i> <?= htmlspecialchars($cat['name']); ?></td>
                            <td><code><?= htmlspecialchars($cat['icon'] ?? 'fas fa-tag'); ?></code></td>
                            <td><?= htmlspecialchars($cat['slug']); ?></td>
                            <td class="text-end">
                                <form method="POST" action="<?= BASEURL; ?>/admin/edit_category/<?= $cat['id']; ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="name" value="<?= htmlspecialchars($cat['name']); ?>">
                                    <input type="hidden" name="icon" value="<?= htmlspecialchars($cat['icon'] ?? 'fas fa-tag'); ?>">
                                    <button class="btn btn-sm btn-outline-primary" onclick="this.form.submit()"><i class="fas fa-edit"></i></button>
                                </form>
                                <form method="POST" action="<?= BASEURL; ?>/admin/delete_category/<?= $cat['id']; ?>" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <button class="btn btn-sm btn-outline-danger" <?= $cat['id']<=3?'disabled':''; ?>><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addCat" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="<?= BASEURL; ?>/admin/add_category" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <div class="mb-3"><label class="form-label">Nama Kategori</label><input class="form-control" name="name" required placeholder="Contoh: Alat Pembersih"></div>
        <div class="mb-3"><label class="form-label">Icon (FontAwesome class)</label><input class="form-control" name="icon" value="fas fa-tag" placeholder="fas fa-tag"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-brand" type="submit">Simpan</button></div>
    </form>
  </div>
</div>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>