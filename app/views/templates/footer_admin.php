    </div>

    <footer class="text-center text-muted small py-4 mt-4">
        &copy; <?= date('Y'); ?> RentalMarket &middot; Admin Panel
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- === TAMBAHKAN SCRIPT REAL-TIME SEARCH DI BAWAH INI === -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cari semua form pencarian yang menggunakan method GET dan punya input 'q'
        const searchForms = document.querySelectorAll('form[method="GET"]');

        searchForms.forEach(form => {
            const input = form.querySelector('input[name="q"]');
            if (input) {
                let debounceTimer; // Untuk menunda request agar server tidak kelebihan beban

                // Mencegah halaman refresh jika user menekan tombol 'Enter'
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); 
                });

                // Deteksi setiap ketikan huruf
                input.addEventListener('input', function() {
                    const keyword = this.value;
                    const url = new URL(form.action);
                    url.searchParams.set('q', keyword);

                    // Ubah ikon kaca pembesar menjadi ikon loading/spinner
                    const btnIcon = form.querySelector('button i');
                    if (btnIcon) btnIcon.className = 'fas fa-spinner fa-spin';

                    clearTimeout(debounceTimer);
                    
                    // Tunggu 300ms setelah berhenti mengetik baru kirim request ke server
                    debounceTimer = setTimeout(() => {
                        fetch(url)
                            .then(response => response.text())
                            .then(html => {
                                // Ubah teks HTML menjadi elemen DOM
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Ambil bagian <tbody> (isi tabel) dari HTML yang baru
                                const newTbody = doc.querySelector('tbody');
                                // Cari <tbody> di halaman saat ini
                                const currentTbody = document.querySelector('tbody');

                                // Timpa isi tabel lama dengan isi tabel baru
                                if (newTbody && currentTbody) {
                                    currentTbody.innerHTML = newTbody.innerHTML;
                                }

                                // Kembalikan ikon loading menjadi ikon kaca pembesar
                                if (btnIcon) btnIcon.className = 'fas fa-search';
                            })
                            .catch(err => {
                                console.error('Gagal mengambil data:', err);
                                if (btnIcon) btnIcon.className = 'fas fa-search';
                            });
                    }, 300); // Waktu tunda 300 milidetik
                });
            }
        });
    });
    </script>
</body>
</html>
