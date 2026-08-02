<form action="<?= BASEURL; ?>/cart/add" method="POST" id="form_booking">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
    <input type="hidden" name="item_id" value="<?= $data['item']['id']; ?>">
    <input type="hidden" id="daily_price" value="<?= $data['item']['price_daily']; ?>">

    <?php 
        // Cek apakah yang melihat adalah pemilik barang
        $is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $data['item']['owner_id']); 
        
        // Cek apakah status barang sedang 'Active' (tersedia)
        $is_active = (strtolower($data['item']['status']) === 'active');
    ?>

   <!-- JIKA BARANG SEDANG DISEWA / TIDAK AKTIF -->
   <?php if(!$is_active): ?>
        
        <?php if($is_owner): ?>
            <!-- TAMPILAN KHUSUS UNTUK PEMILIK BARANG -->
            <div class="alert alert-info text-center mb-3 border-0 shadow-sm" role="alert">
                <i class="fas fa-info-circle mb-2 text-info" style="font-size: 24px;"></i><br>
                <strong class="text-dark">Status: Sedang Disewa</strong><br>
                <small class="text-muted">Barang Anda saat ini sedang dalam masa penyewaan.</small>
            </div>
            
            <div class="d-grid gap-2 mt-2">
                <a href="<?= BASEURL; ?>/booking/masuk" class="btn btn-primary fw-bold py-2">
                    <i class="fas fa-list me-1"></i> Cek Pesanan Masuk
                </a>
            </div>
            
        <?php else: ?>
            <!-- TAMPILAN UNTUK PUBLIK (Misal ada yang buka via Link/Bookmark) -->
            <div class="alert alert-warning text-center mb-3 border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle mb-2" style="font-size: 24px;"></i><br>
                <strong>Barang Sedang Disewa</strong><br>
                <small>Barang ini sedang tidak tersedia untuk disewa saat ini.</small>
            </div>
            
            <div class="d-grid gap-2 mt-2">
                <button type="button" class="btn btn-secondary fw-bold py-2" disabled>
                    <i class="fas fa-ban me-1"></i> Tidak Tersedia
                </button>
                <a href="<?= BASEURL; ?>/home/explore" class="btn btn-outline-brand btn-sm">
                    <i class="fas fa-th-large me-1"></i> Lihat Katalog Lainnya
                </a>
            </div>
        <?php endif; ?>

    <!-- JIKA BARANG TERSEDIA (ACTIVE) -->
    <?php else: ?>

        <?php if($is_owner): ?>
            <div class="alert alert-warning p-2 small mb-3 border-0">
                <i class="fas fa-exclamation-triangle text-dark"></i> <strong>Mode Pemilik:</strong> Blokir jadwal barang Anda sendiri.
            </div>
        <?php endif; ?>

        <!-- Bagian Input Tanggal Mulai -->
        <div class="mb-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" class="form-control" 
                <?= !isset($_SESSION['user_id']) ? 'disabled' : 'required'; ?>>
        </div>

        <!-- Bagian Input Tanggal Selesai -->
        <div class="mb-3">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" id="end_date" name="end_date" class="form-control" 
                <?= !isset($_SESSION['user_id']) ? 'disabled' : 'required'; ?>>
        </div>

        <!-- Rincian Harga -->
        <div id="price_summary" class="d-none bg-light border p-3 rounded mb-3">
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Durasi Sewa:</span><span id="display_duration" class="fw-bold small">0 Hari</span></div>
            <div class="d-flex justify-content-between align-items-center"><span class="text-muted small">Total Tagihan:</span><span id="display_total" class="fw-bold text-brand fs-5">Rp 0</span></div>
            <div class="text-muted small mt-1">+ Biaya admin Rp 5.000</div>
        </div>

        <!-- PILIHAN METODE TRANSAKSI (Hanya tampil untuk Penyewa) -->
        <?php if(!$is_owner && isset($_SESSION['user_id'])): ?>
            <div class="mb-3">
                <label class="form-label fw-bold small text-dark">Metode Transaksi</label>
                
                <div class="form-check border rounded p-2 mb-2 bg-light shadow-sm">
                    <input class="form-check-input ms-1 mt-2" type="radio" name="payment_method" id="pay_online" value="online" checked required>
                    <label class="form-check-label small ms-2 d-block" for="pay_online" style="cursor: pointer;">
                        <strong class="text-dark">Bayar Online (Sistem Web)</strong><br>
                        <span class="text-muted" style="font-size: 11.5px;">Aman, uang ditahan sistem sampai barang diterima.</span>
                    </label>
                </div>
                
                <div class="form-check border rounded p-2 shadow-sm">
                    <input class="form-check-input ms-1 mt-2" type="radio" name="payment_method" id="pay_cod" value="cod" required>
                    <label class="form-check-label small ms-2 d-block" for="pay_cod" style="cursor: pointer;">
                        <strong class="text-dark">Bayar di Tempat (COD)</strong><br>
                        <span class="text-muted" style="font-size: 11.5px;">Bayar langsung saat serah terima barang.</span>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if($is_owner): ?>
            <div class="form-check mb-3 bg-light p-2 rounded border">
                <input class="form-check-input ms-1" type="checkbox" id="tnc_owner" required>
                <label class="form-check-label small text-muted ms-2" for="tnc_owner">Saya setuju dengan S&K Marketplace untuk penggunaan pribadi.</label>
            </div>
        <?php endif; ?>

        <div class="d-grid gap-2">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <button type="button" class="btn btn-brand fw-bold py-2" onclick="window.location.href='<?= BASEURL; ?>/auth/login'">
                    <i class="fas fa-sign-in-alt me-1"></i> Login untuk Menyewa
                </button>
            <?php else: ?>
                <button type="submit" class="btn <?= $is_owner ? 'btn-warning text-dark' : 'btn-brand'; ?> fw-bold py-2" id="btn_submit">
                    <i class="fas fa-cart-plus me-1"></i> <?= $is_owner ? 'Blokir Jadwal' : 'Masukkan Keranjang Sewa'; ?>
                </button>
            <?php endif; ?>
            
            <?php if(!$is_owner): ?>
            <a href="<?= BASEURL; ?>/home/explore" class="btn btn-outline-brand btn-sm"><i class="fas fa-th-large me-1"></i> Lihat Katalog Lainnya</a>
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const s = document.getElementById('start_date'), e = document.getElementById('end_date');
    const sum = document.getElementById('price_summary'), dur = document.getElementById('display_duration'), tot = document.getElementById('display_total');
    const btn = document.getElementById('btn_submit');
    const dp = parseFloat(document.getElementById('daily_price').value);
    const tnc = document.getElementById('tnc_owner');
    
    if(s && e) {
        s.addEventListener('change', function(){ e.disabled=false; e.min=this.value; calc(); });
        e.addEventListener('change', calc);
    }
    
    if(tnc) tnc.addEventListener('change', calc);
    
    function calc(){
        if(s.value && e.value){
            const d = Math.ceil((new Date(e.value)-new Date(s.value))/(86400000))+1;
            if(d>0){ 
                const t=d*dp; 
                dur.innerText=d+" Hari"; 
                tot.innerText='Rp '+new Intl.NumberFormat('id-ID').format(t);
                sum.classList.remove('d-none');
                if(btn) btn.disabled = (tnc && !tnc.checked);
            } else { 
                sum.classList.add('d-none'); 
                if(btn) btn.disabled=true; 
            }
        }
    }
});
</script>