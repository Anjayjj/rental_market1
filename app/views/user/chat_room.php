<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-header bg-primary text-white rounded-top-4 py-3">
                    <h5 class="m-0 fw-bold"><i class="fas fa-address-book me-2"></i> Kontak Pemilik Barang</h5>
                </div>
                <div class="card-body p-5">
                    
                    <!-- Cek apakah data kontak (owner) berhasil diterima dari Controller -->
                    <?php if (isset($data['owner']) && !empty($data['owner'])): ?>
                        
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user text-secondary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark"><?= htmlspecialchars($data['owner']['name'] ?? 'Pengguna'); ?></h4>
                            <p class="text-muted">Pemilik Barang Terverifikasi</p>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <h3 class="fw-bold text-primary m-0">
                                <?= htmlspecialchars($data['owner']['phone'] ?? '-'); ?>
                            </h3>
                        </div>

                        <?php 
                            // Format nomor telepon untuk WhatsApp
                            $wa_number = preg_replace('/[^0-9]/', '', $data['owner']['phone'] ?? '');
                            if (substr($wa_number, 0, 1) == '0') { 
                                $wa_number = '62' . substr($wa_number, 1); 
                            }
                            $wa_text = urlencode("Halo " . ($data['owner']['name'] ?? '') . ", saya tertarik untuk menyewa barang di RentalMarketplace.");
                        ?>
                        
                        <a href="https://wa.me/<?= $wa_number; ?>?text=<?= $wa_text; ?>" target="_blank" class="btn btn-success btn-lg fw-semibold w-100 rounded-pill shadow-sm">
                            <i class="fab fa-whatsapp me-2"></i> Hubungi via WhatsApp
                        </a>

                    <?php else: ?>
                        <!-- Tampilan darurat jika data tidak ditemukan -->
                        <div class="alert alert-danger rounded-3">
                            <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i><br>
                            Maaf, data kontak tidak dapat dimuat atau tidak ditemukan.
                        </div>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill w-100">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Halaman Sebelumnya
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>