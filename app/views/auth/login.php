<?php require_once VIEWPATH . '/templates/header_public.php'; ?>
<div class="row justify-content-center align-items-center py-4" style="min-height:78vh;">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg overflow-hidden" style="border-radius:22px;">
            <div class="row g-0">
                <div class="col-md-6 d-none d-md-flex auth-art align-items-center justify-content-center p-5">
                    <div class="art-inner text-center">
                        <div class="mb-3"><i class="fas fa-box-open" style="font-size:4rem;"></i></div>
                        <h3 class="fw-bold">Selamat datang!</h3>
                        <p class="opacity-75 mb-4">Masuk untuk mengelola sewa & barang kamu di RentalMarket.</p>
                        <ul class="list-unstyled text-start d-inline-block small opacity-90">
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Kelola barang sewaan</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Lacak pesanan & penyewa</li>
                            <li class="mb-2"><i class="fas fa-check-circle me-2"></i> Chat real-time dengan penyewa</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 bg-white p-4 p-md-5">
                    <?php if(isset($_SESSION['flash_success'])): ?><div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
                    <?php if(isset($_SESSION['flash_error'])): ?><div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
                    <h4 class="fw-bold text-dark mb-1">Masuk</h4>
                    <p class="text-muted small mb-4">Belum punya akun? <a href="<?= BASEURL; ?>/auth/register" class="text-brand fw-semibold">Daftar gratis</a></p>
                    <form action="<?= BASEURL; ?>/auth/login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                        <div class="position-relative mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" placeholder="" required>
    
    <!-- Ikon amplop (sebagai pemanis agar simetris) -->
    <span class="position-absolute end-0 top-50 translate-middle-y me-3" 
          style="cursor: default; margin-top: 14px;">
        <i class="fas fa-envelope text-muted"></i>
    </span>
</div>
                       <!-- Pastikan pembungkusnya memiliki position: relative -->
<div class="position-relative mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" id="passwordField" class="form-control" required>
    
    <!-- Ikon Mata (menggunakan FontAwesome) -->
    <span class="position-absolute end-0 top-50 translate-middle-y me-3" 
          onclick="togglePassword()" style="cursor: pointer; margin-top: 15.5px;">
        <i class="fas fa-eye" id="eyeIcon"></i>
    </span>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById("passwordField");
    const eyeIcon = document.getElementById("eyeIcon");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="rm"><label class="form-check-label small text-muted" for="rm">Ingat saya</label></div>
                            <a href="#" class="small text-brand">Lupa password?</a>
                        </div>
                        <button class="btn btn-brand w-100 fw-bold py-2 mb-3">Masuk</button>
                    </form>
                    <div class="text-center small text-muted mb-2">atau masuk dengan</div>
                    <div class="d-flex gap-2 mb-3">
                        <a href="#" class="btn-social flex-fill"><i class="fab fa-google text-danger"></i> Google</a>
                        <a href="#" class="btn-social flex-fill"><i class="fab fa-facebook text-primary"></i> Facebook</a>
                    </div>
                    <div class="text-center small"><a href="<?= BASEURL; ?>" class="text-muted"><i class="fas fa-arrow-left me-1"></i> Kembali ke beranda</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once VIEWPATH . '/templates/footer_public.php'; ?>
