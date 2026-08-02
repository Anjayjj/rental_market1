<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Manajemen Barang</h4>
    <form method="GET" action="<?= BASEURL; ?>/admin/items" class="d-flex gap-2">
        <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari nama barang/pemilik...">
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Gambar</th><th>Nama Barang</th><th>Kategori</th><th>Pemilik</th><th>Harga</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($data['items'])): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada barang.</td></tr>
                    <?php else: foreach($data['items'] as $it): ?>
                        <tr>
                            <td><?= $it['id']; ?></td>
                            <td><img src="<?= BASEURL; ?>/assets/uploads/items/<?= htmlspecialchars($it['cover_image'] ?? 'default.jpg'); ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;"></td>
                            <td><?= htmlspecialchars($it['name']); ?></td>
                            <td><?= htmlspecialchars($it['category_name'] ?? 'Uncategorized'); ?></td>
                            <td><?= htmlspecialchars($it['owner_name'] ?? '-'); ?></td>
                            <td>Rp <?= number_format($it['price_daily'],0,',','.'); ?></td>
                            <td><span class="badge rounded-pill <?= $it['status']=='active'?'bg-success':($it['status']=='rented'?'bg-warning text-dark':'bg-secondary'); ?>"><?= ucfirst($it['status']); ?></span></td>
                            <td class="text-end">
                                <form method="POST" action="<?= BASEURL; ?>/admin/update_item_status/<?= $it['id']; ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                        <?php foreach (['active','inactive','rented'] as $st): ?>
                                            <option value="<?= $st; ?>" <?= $it['status']==$st?'selected':''; ?>><?= ucfirst($st); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                <form method="POST" action="<?= BASEURL; ?>/admin/delete_item/<?= $it['id']; ?>" class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>