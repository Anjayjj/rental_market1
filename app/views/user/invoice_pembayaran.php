<?php require_once VIEWPATH . '/templates/header_user.php'; ?>
<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['flash_success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); // Hapus pesan setelah ditampilkan ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['flash_error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-primary border-4">
    <h4 class="m-0 fw-bold text-dark">Pembayaran Sewa</h4>
    <a href="<?= BASEURL; ?>/booking/saya" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
</div>

<div class="container-fluid px-0">
    <div class="row">
        <!-- Rincian Invoice -->
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Invoice: <span class="text-primary"><?= $data['booking']['invoice_no']; ?></span></h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-warning text-dark">Menunggu Pembayaran</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Barang</td>
                            <td class="fw-bold"><?= htmlspecialchars($data['booking']['item_name']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi Sewa</td>
                            <td><?= $data['booking']['start_date']; ?> s/d <?= $data['booking']['end_date']; ?> (<?= $data['booking']['duration']; ?> Hari)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Sewa</td>
                            <td>Rp <?= number_format($data['booking']['total_price'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Biaya Admin</td>
                            <td>Rp <?= number_format($data['booking']['admin_fee'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted fw-bold">Total Pembayaran</td>
                            <td class="fw-bold fs-5 text-danger">Rp <?= number_format($data['booking']['grand_total'], 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Upload Bukti -->
        <div class="col-md-5">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title">Upload Bukti Transfer</h5>
                    <p class="text-muted small">Silakan transfer ke <strong>BNI 1981149345 a/n Andika</strong></p>
                    
                    <form action="<?= BASEURL; ?>/payment/upload" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                        <input type="hidden" name="booking_id" value="<?= $data['booking']['id']; ?>">
                        <input type="hidden" name="amount" value="<?= $data['booking']['grand_total']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="BNI">Transfer Bank BNI</option>
                                <option value="BCA">Transfer Bank BCA</option>
                                <option value="Mandiri">Transfer Bank Mandiri</option>
                                <option value="Gopay">GoPay</option>
                                <option value="Ovo">OVO</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti Transfer (JPG/PNG, Max 2MB)</label>
                            <input type="file" name="proof_image" class="form-control" accept="image/jpeg, image/png" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Kirim Bukti Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>
