<?php require_once VIEWPATH . '/templates/header_user.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border-start border-secondary border-4">
    <h4 class="m-0 fw-bold text-dark">Pengaturan Akun</h4>
</div>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-4">
        <form action="<?= BASEURL; ?>/user/update_profile" method="POST" enctype="multipart/form-data">
            <!-- Area Foto Profil -->
            <div class="d-flex flex-column align-items-center mb-4 pb-3 border-bottom">
                <img src="<?= BASEURL; ?>/assets/uploads/avatars/<?= $_SESSION['user_avatar'] ?? 'default.png'; ?>" class="rounded-circle mb-3 border shadow-sm object-fit-cover" style="width: 120px; height: 120px;" id="avatarPreview" alt="Foto Profil">
                <div class="w-100 text-center">
                    <label for="avatar" class="btn btn-sm btn-outline-primary mb-2"><i class="fas fa-camera me-1"></i> Pilih Foto Baru</label>
                    <input class="form-control d-none" type="file" id="avatar" name="avatar" accept="image/jpeg, image/png, image/jpg" onchange="previewImage(event)">
                    
                    <?php if(isset($_SESSION['user_avatar']) && $_SESSION['user_avatar'] != 'default.png'): ?>
                        <div class="form-check d-flex justify-content-center align-items-center mt-2 mb-0">
                            <input class="form-check-input me-2" type="checkbox" name="remove_avatar" id="removeAvatar" value="1">
                            <label class="form-check-label text-danger small fw-semibold" for="removeAvatar" style="cursor: pointer;"><i class="fas fa-trash-alt me-1"></i>Hapus Foto Saat Ini</label>
                        </div>
                    <?php endif; ?>
                    <div class="form-text small mt-1">Format JPG/PNG, Maksimal 2MB.</div>
                </div>
            </div>

            <!-- Data Diri -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" class="form-control bg-light" name="name" value="<?= $_SESSION['user_name']; ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Alamat Lengkap</label>
              <!-- INI BENAR -->
<textarea class="form-control" name="address"><?= $data['user']['address'] ?? ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<?php require_once VIEWPATH . '/templates/footer.php'; ?>