<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0 fw-bold">Dashboard Admin</h4>
        <small class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>. Tanggal: <?= date('d M Y'); ?></small>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100 py-2 border-0">
            <div class="card-body">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($data['stats']['total_users']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow-sm h-100 py-2 border-0">
            <div class="card-body">
                <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Barang</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($data['stats']['total_items']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4 shadow-sm h-100 py-2 border-0">
            <div class="card-body">
                <div class="text-xs fw-bold text-info text-uppercase mb-1">Booking Berhasil</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($data['stats']['total_bookings']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100 py-2 border-0">
            <div class="card-body">
                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pendapatan Platform</div>
                <div class="h5 mb-0 fw-bold text-gray-800">Rp <?= number_format($data['stats']['total_revenue'], 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Pending -->
<div class="row mb-4">
    <div class="col-12 mb-4"> <!-- Mengubah col-lg-8 menjadi col-12 dan menghapus col-lg-4 -->
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Perlu Perhatian</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Pemilik</th><th class="text-end">Aksi</th></tr></thead>
                        <tbody>
                            <?php if(empty($data['uncategorized'])): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada barang yang perlu dikategorikan.</td></tr>
                            <?php else: foreach($data['uncategorized'] as $it): ?>
                                <tr>
                                    <td><?= $it['id']; ?></td>
                                    <td><?= htmlspecialchars($it['name']); ?></td>
                                    <td><span class="badge bg-warning text-dark">Lainnya</span></td>
                                    <td><?= htmlspecialchars($it['owner_name'] ?? '-'); ?></td>
                                    <td class="text-end">
                                        <form method="POST" action="<?= BASEURL; ?>/admin/update_item_category/<?= $it['id']; ?>" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                            <select name="category_id" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                <option value="">Pilih...</option>
                                                <?php foreach (($data['all_categories'] ?? []) as $c): if($c['id']==7) continue; ?>
                                                    <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Transaksi Terbaru -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header py-3 bg-white border-bottom"><h6 class="m-0 fw-bold text-primary">Grafik Pendapatan (Tahun <?= date('Y'); ?>)</h6></div>
            <div class="card-body"><div class="chart-area"><canvas id="revenueChart" style="height: 320px;"></canvas></div></div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Transaksi Terbaru</h6>
                <a href="<?= BASEURL; ?>/admin/bookings" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if(empty($data['recent_bookings'])): ?>
                        <li class="list-group-item text-center text-muted py-4">Belum ada transaksi.</li>
                    <?php else: foreach($data['recent_bookings'] as $trx): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div><h6 class="my-0 small fw-bold text-dark"><?= $trx['invoice_no']; ?></h6><small class="text-muted"><?= $trx['user_name']; ?> - <?= date('d M Y', strtotime($trx['created_at'])); ?></small></div>
                            <?php $badge = 'bg-secondary'; if($trx['status'] == 'completed' || $trx['status'] == 'active') $badge = 'bg-success'; if($trx['status'] == 'approved') $badge = 'bg-info text-dark'; if($trx['status'] == 'pending') $badge = 'bg-warning text-dark'; if($trx['status'] == 'rejected' || $trx['status'] == 'cancelled') $badge = 'bg-danger'; ?>
                            <span class="badge <?= $badge; ?> rounded-pill" style="font-size: 0.75rem;"><?= ucfirst($trx['status']); ?></span>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData = <?= $data['chart_data']; ?>;
    new Chart(document.getElementById("revenueChart"), {
        type: 'line',
        data: {
            labels: ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agt","Sep","Okt","Nov","Des"],
            datasets: [{ label: "Pendapatan (Rp)", lineTension: 0.3, backgroundColor: "rgba(26, 37, 47, 0.1)", borderColor: "rgba(26, 37, 47, 1)", pointRadius: 4, pointBackgroundColor: "#fff", pointBorderColor: "rgba(26, 37, 47, 1)", pointHoverRadius: 5, pointBorderWidth: 2, data: chartData, fill: true }]
        },
        options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); } } } }, plugins: { legend: { display: false }, tooltip: { backgroundColor: "#fff", bodyColor: "#858796", titleColor: '#6e707e', borderColor: '#dddfeb', borderWidth: 1, padding: 15, displayColors: false, callbacks: { label: function(ctx){ return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw); } } } } }
    });
});
</script>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>