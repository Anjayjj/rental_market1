</div><!-- /.container -->

<footer class="site-footer mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="nav-logo fs-4 mb-2"><i class="fas fa-box-open me-1"></i>RentalMarket</div>
                <p class="small mb-3">Platform sewa barang P2P terpercaya di Indonesia. Sewa alat foto, outdoor, kendaraan, dan lainnya dengan mudah, aman, dan terjangkau.</p>
                <div class="footer-social d-flex gap-2">
                    <a href="https://www.instagram.com/_._._.i.k.a/" class="nav-icon" style="background:rgba(255,255,255,.1);color:#fff;"><i class="fab fa-instagram"></i></a>
                    <a href="https://t.me/+6285845826761" class="nav-icon" style="background:rgba(255,255,255,.1);color:#fff;"><i class="fab fa-telegram"></i></a>
                    <a href="https://www.facebook.com/andika.790626?locale=id_ID" class="nav-icon" style="background:rgba(255,255,255,.1);color:#fff;"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@ikaa_000000" class="nav-icon" style="background:rgba(255,255,255,.1);color:#fff;"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Belanja</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= BASEURL; ?>/home/explore">Katalog Barang</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/home/explore?category=1">Kamera & Lensa</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/home/explore?category=2">Peralatan Camping</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/home/explore?category=3">Kendaraan</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Penyedia</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= BASEURL; ?>/useritem/create">Pasang Iklan</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/useritem/index">Kelola Barang</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/user/dashboard">Dashboard</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Bantuan</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= BASEURL; ?>/home/explore">Cara Sewa</a></li>
                    <li class="mb-2"><a href="#">Syarat & Ketentuan</a></li>
                    <li class="mb-2"><a href="#">Kebijakan Privasi</a></li>
                    <li class="mb-2"><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Kontak</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-envelope me-1"></i> sentral.gg@yahoo.com</li>
                    <li class="mb-2"><i class="fas fa-phone me-1"></i> 0858-4582-6761</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-1"></i> Sambas, Indonesia</li>
                </ul>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,.12);">
        <div class="d-flex flex-wrap justify-content-between small">
            <span>&copy; <?= date('Y'); ?> RentalMarket. All rights reserved.</span>
            <span>Metode pembayaran: <i class="fab fa-cc-visa me-1"></i><i class="fab fa-cc-mastercard me-1"></i> BCA &middot; Mandiri &middot; GoPay &middot; OVO</span>
        </div>
    </div>
</footer>

<!-- Toast stack + scroll top -->
<div class="toast-stack" id="toastStack"></div>
<button class="scroll-top" id="scrollTop" aria-label="Ke atas"><i class="fas fa-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Navbar scrolled shadow */
window.addEventListener('scroll', function(){
    var n = document.querySelector('.site-nav'); if(n) n.classList.toggle('scrolled', window.scrollY > 8);
    var st = document.getElementById('scrollTop'); if(st) st.classList.toggle('show', window.scrollY > 320);
});
document.getElementById('scrollTop')?.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });
/* Reveal on scroll */
var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } });
}, {threshold:.12});
document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
/* Toast helper */
function rmToast(msg, type){
    type = type || 'info';
    var stack = document.getElementById('toastStack');
    var icons = {success:'fa-check', error:'fa-times', info:'fa-info'};
    var el = document.createElement('div');
    el.className = 'rm-toast ' + type;
    el.innerHTML = '<div class="ic"><i class="fas '+icons[type]+'"></i></div><div><div style="font-weight:700">'+msg+'</div></div>';
    stack.appendChild(el);
    setTimeout(function(){ el.style.opacity='0'; el.style.transform='translateX(120%)'; setTimeout(function(){ el.remove(); }, 300); }, 2800);
}
/* Wishlist toggle */
document.querySelectorAll('.fav').forEach(function(btn){
    btn.addEventListener('click', function(e){
        e.preventDefault();
        var id = btn.getAttribute('data-item');
        if(!id) return;
        
        fetch('<?= BASEURL; ?>/item/toggle_wishlist', {
            method:'POST', 
            headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
            body:'item_id='+id
        })
        .then(function(r){ return r.json(); })
        .then(function(res){
            // 1. Tangkap instruksi redirect dari PHP jika belum login
            if(res.status === 'redirect'){
                window.location.href = res.url;
                return;
            }
            
            // 2. Eksekusi perubahan ikon jika sukses
            if(res.status === 'success'){
                var on = res.action === 'added';
                btn.classList.toggle('active', on);
                btn.innerHTML = on ? '<i class="fas fa-heart"></i>' : '<i class="far fa-heart"></i>';
                rmToast(on ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist', 'success');
            } 
            // 3. Fallback (cadangan) jika masih menggunakan format error lama
            else if(res.status === 'error'){ 
                window.location.href = '<?= BASEURL; ?>/auth/login'; 
            }
        }).catch(function(){});
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ambil semua input pencarian (di Navbar dan di Hero Section)
    const searchInputs = document.querySelectorAll('input[name="search"]');

    searchInputs.forEach(input => {
        // Cari pembungkus input tersebut agar dropdown bisa menempel dengan benar
        const parent = input.parentElement;
        parent.style.position = 'relative'; // Wajib relative

        // Buat elemen kotak prediksi secara dinamis
        const suggestionBox = document.createElement('div');
        suggestionBox.className = 'search-suggestions';
        parent.appendChild(suggestionBox);

        let debounceTimer;

        // Saat user mengetik...
        input.addEventListener('input', function() {
            const keyword = this.value.trim();
            clearTimeout(debounceTimer);

            // Jika ketikan kurang dari 2 huruf, sembunyikan kotak
            if (keyword.length < 2) {
                suggestionBox.style.display = 'none';
                return;
            }

            // Delay 300ms agar tidak spam request ke server
            debounceTimer = setTimeout(() => {
                fetch('<?= BASEURL; ?>/home/suggestions?q=' + encodeURIComponent(keyword))
                    .then(response => response.json())
                    .then(data => {
                        suggestionBox.innerHTML = '';
                        if (data.length > 0) {
                            // Isi kotak dengan hasil dari database
                            // Isi kotak dengan hasil dari database
                            data.forEach(item => {
                                const a = document.createElement('a');
                                
                                // URL sudah disiapkan langsung dari PHP (Model)
                                a.href = item.url;
                                
                                // Bedakan ikon: Jika kategori pakai ikon 'tag', jika barang pakai ikon 'search'
                                const iconClass = item.type === 'category' ? 'fas fa-tags text-brand' : 'fas fa-search';
                                
                                // Tambahkan penanda ketebalan huruf jika itu adalah kategori
                                const textName = item.type === 'category' ? `<strong>${item.name}</strong>` : item.name;

                                a.innerHTML = `<i class="${iconClass} me-3"></i> ${textName}`;
                                suggestionBox.appendChild(a);
                            });
                            suggestionBox.style.display = 'block';
                        } else {
                            suggestionBox.style.display = 'none';
                        }
                    })
                    .catch(err => console.error(err));
            }, 300);
        });

        // Sembunyikan kotak jika user klik area di luar form pencarian
        document.addEventListener('click', function(e) {
            if (!parent.contains(e.target)) {
                suggestionBox.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>
