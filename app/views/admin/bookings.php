<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Manajemen Seluruh Transaksi</h4>
    <div class="d-flex gap-2">
        <form method="GET" action="<?= BASEURL; ?>/admin/bookings" class="d-flex gap-2">
            <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari invoice/nama...">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No Invoice</th><th>Penyewa</th><th>Barang</th><th>Tanggal Sewa</th><th>Total Tagihan</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($data['bookings'])): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                    <?php else: foreach($data['bookings'] as $trx): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?= $trx['invoice_no']; ?></td>
                            <td><?= htmlspecialchars($trx['user_name']); ?></td>
                            <td><?= htmlspecialchars($trx['item_name']); ?></td>
                            <td><small class="d-block text-muted">Mulai: <?= date('d/m/Y', strtotime($trx['start_date'])); ?></small><small class="d-block text-muted">Selesai: <?= date('d/m/Y', strtotime($trx['end_date'])); ?></small></td>
                            <td>Rp <?= number_format($trx['grand_total'], 0, ',', '.'); ?></td>
                            
                            <!-- BAGIAN STATUS YANG DIPERBARUI -->
                            <td>
                                <?php
                                    $status_db = strtolower($trx['status']);
                                    $badge = 'bg-secondary';
                                    
                                    if($status_db == 'completed' || $status_db == 'active') $badge = 'bg-success';
                                    if($status_db == 'approved') $badge = 'bg-info text-dark';
                                    if($status_db == 'pending') $badge = 'bg-warning text-dark';
                                    if($status_db == 'rejected' || $status_db == 'cancelled') $badge = 'bg-danger';
                                    
                                    // Default teks adalah huruf besar dari database
                                    $status_text = strtoupper($status_db);
                                    
                                    // Manipulasi teks khusus untuk handover_pending
                                    if($status_db == 'handover_pending') {
                                        $status_text = 'HANDOVER';
                                        $badge = 'bg-secondary';
                                    }
                                ?>
                                <span class="badge <?= $badge; ?> rounded-pill" style="font-size: 0.75rem;"><?= $status_text; ?></span>
                            </td>
                            
                            <!-- BAGIAN AKSI & DROPDOWN YANG DIPERBARUI -->
                            <td class="text-end">
                                <form method="POST" action="<?= BASEURL; ?>/admin/update_booking_status/<?= $trx['id']; ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                    <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                        <?php 
                                        // Tambahkan 'handover_pending' ke dalam array pilihan
                                        $options = ['pending', 'approved', 'handover_pending', 'active', 'completed', 'cancelled', 'rejected'];
                                        
                                        foreach ($options as $st): 
                                            // Ubah tampilan teks dropdown agar rapi
                                            $label = ($st === 'handover_pending') ? 'Handover' : ucfirst($st);
                                        ?>
                                            <option value="<?= $st; ?>" <?= $trx['status']==$st?'selected':''; ?>><?= $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                <a href="<?= BASEURL; ?>/return/form/<?= $trx['id']; ?>" class="btn btn-sm btn-outline-primary ms-1"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>