<?php require_once VIEWPATH . '/templates/header_admin.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
    <h4 class="mb-1 fw-bold text-dark">Verifikasi Pembayaran</h4>
    <p class="text-muted small mb-0">Tinjau dan validasi bukti transfer penyewa.</p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="<?= BASEURL; ?>/admin/payments" class="d-flex gap-2">
            <input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Cari invoice/nama...">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">Daftar Menunggu Verifikasi</h6>
    </div>
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover align-middle bg-white">
        <thead class="table-light">
            <tr>
                <th>No Invoice</th>
                <th>Penyewa</th>
                <th>Barang</th>
                <th>Metode</th>
                <th>Total (Rp)</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- 1. Cek apakah array data pembayaran kosong -->
            <?php if (empty($data['payments'])): // Sesuaikan 'payments' dengan nama array dari controller Anda jika berbeda ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Tidak ada pembayaran yang menunggu verifikasi.
                    </td>
                </tr>
            <?php else: ?>
                <!-- 2. Jika ada data, lakukan perulangan -->
                <?php foreach ($data['payments'] as $booking): ?>
                    <tr>
                        <td class="text-primary fw-semibold"><?= htmlspecialchars($booking['invoice_no']); ?></td>
                        <td><?= htmlspecialchars($booking['user_name'] ?? 'User'); ?></td>
                        <td><?= htmlspecialchars($booking['item_name'] ?? 'Barang'); ?></td>
                        <td><?= htmlspecialchars($booking['payment_method']); ?></td>
                        <td class="fw-bold text-danger"><?= number_format($booking['amount'] ?? 0, 0, ',', '.'); ?></td>
                        
                        <td>
                            <!-- Tombol Pemicu Modal Bukti -->
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="modal" data-bs-target="#modalBukti<?= $booking['id']; ?>">
                                Lihat Bukti
                            </button>
                        </td>
                        
                        <td>
                            <!-- Form Terima -->
                            <form action="<?= BASEURL; ?>/adminpayment/verifikasi" method="POST" class="d-inline">
                                <!-- Hapus atau sesuaikan baris CSRF token ini jika Anda belum menggunakan sistem token di sesi Admin -->
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                
                                <input type="hidden" name="payment_id" value="<?= $booking['id']; ?>">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                <input type="hidden" name="action" value="verify">
                                <button type="submit" class="btn btn-sm btn-success rounded-pill" onclick="return confirm('Apakah Anda yakin ingin MENERIMA pembayaran ini?');">Terima</button>
                            </form>
                            
                            <!-- Form Tolak -->
                            <form action="<?= BASEURL; ?>/adminpayment/verifikasi" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                
                                <input type="hidden" name="payment_id" value="<?= $booking['id']; ?>">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Apakah Anda yakin ingin MENOLAK pembayaran ini?');">Tolak</button>
                            </form>
                        </td>
                    </tr>

                    <!-- 3. Modal Bukti Pembayaran HARUS berada DI DALAM perulangan foreach -->
                    <div class="modal fade" id="modalBukti<?= $booking['id']; ?>" tabindex="-1" aria-labelledby="modalBuktiLabel<?= $booking['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalBuktiLabel<?= $booking['id']; ?>">Bukti Transfer - <?= htmlspecialchars($booking['invoice_no']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <?php if (!empty($booking['proof_image'])): ?>
                                        <img src="<?= BASEURL; ?>/assets/uploads/payments/<?= htmlspecialchars($booking['proof_image']); ?>" alt="Bukti Transfer" class="img-fluid rounded shadow-sm">
                                    <?php else: ?>
                                        <p class="text-muted">Bukti gambar tidak tersedia.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer_admin.php'; ?>
