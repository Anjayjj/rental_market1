<?php require_once VIEWPATH . '/templates/header_public.php'; ?>
<div class="row justify-content-center align-items-center py-4" style="min-height:78vh;">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius:22px;">
            <div class="row g-0">
                <div class="col-md-6 d-none d-md-flex auth-art align-items-center justify-content-center p-5">
                    <div class="art-inner text-center">
                        <div class="mb-3"><i class="fas fa-handshake" style="font-size:4rem;"></i></div>
                        <h3 class="fw-bold">Bergabung sekarang</h3>
                        <p class="opacity-75 mb-4">Sewa kebutuhanmu atau dapatkan penghasilan dari barang menganggur.</p>
                        <ul class="list-unstyled text-start d-inline-block small opacity-90">
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Gratis & tanpa biaya bulanan</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Pasang iklan sewa < 2 menit</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Komunitas penyewa se-Indonesia</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 bg-white p-4 p-md-5">
                    <?php if(isset($_SESSION['flash_error'])): ?><div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
                    <h4 class="fw-bold text-dark mb-1">Daftar Akun</h4>
                    <p class="text-muted small mb-4">Sudah punya akun? <a href="<?= BASEURL; ?>/auth/login" class="text-brand fw-semibold">Masuk</a></p>
                    <form action="<?= BASEURL; ?>/auth/register" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                        <div class="position-relative mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <div class="input-group"><span class="input-group-text bg-light border-0"><i class="far fa-user text-muted"></i></span><input type="text" name="name" class="form-control bg-light border-0" required placeholder="John Doe"></div>
                        </div>
                        <div class="position-relative mb-3">
                            <label class="form-label fw-semibold small">Email</label>
                            <div class="input-group"><span class="input-group-text bg-light border-0"><i class="far fa-envelope text-muted"></i></span><input type="email" name="email" class="form-control bg-light border-0" required placeholder="nama@email.com"></div>
                        </div>
                        <div class="position-relative mb-3">
                            <label class="form-label fw-semibold small">No. HP</label>
                            <div class="input-group"><span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span><input type="text" name="phone" class="form-control bg-light border-0" required placeholder="08xxxxxxxx"></div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold small">Password</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span><input type="password" name="password" class="form-control bg-light border-0" required placeholder="••••••"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold small">Konfirmasi</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span><input type="password" name="password_confirm" class="form-control bg-light border-0" required placeholder="••••••"></div>
                            </div>
                        </div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="agree" required><label class="form-check-label small text-muted" for="agree">Saya menyetujui <a href="#" class="text-brand">Syarat & Ketentuan</a></label></div>
                        <button class="btn btn-brand w-100 fw-bold py-2 mb-3">Daftar Sekarang</button>
                    </form>
                    <div class="text-center small text-muted mb-2">atau daftar dengan</div>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn-social flex-fill"><i class="fab fa-google text-danger"></i> Google</a>
                        <a href="#" class="btn-social flex-fill"><i class="fab fa-facebook text-primary"></i> Facebook</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once VIEWPATH . '/templates/footer_public.php'; ?>
