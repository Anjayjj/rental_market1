<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title']; ?></title>
    <style>
        /* Pengaturan ukuran Kertas A4 & Margin Cetak */
        @page {
            size: A4;
            margin: 12mm 15mm;
        }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            line-height: 1.3; 
            color: #000; 
            font-size: 13px; 
            background: #fff; 
            margin: 0;
            padding: 0;
        }
        .container { max-width: 100%; margin: 0 auto; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .title { font-size: 17px; text-decoration: underline; margin-bottom: 2px; }
        .subtitle { font-size: 12px; margin-bottom: 12px; }
        p { margin-top: 3px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .table-data td { padding: 2px 4px; vertical-align: top; }
        .table-bordered th, .table-bordered td { border: 1px solid #000; padding: 5px 8px; }
        ol { margin-top: 3px; margin-bottom: 8px; padding-left: 20px; }
        li { margin-bottom: 2px; }
        
        /* Area Tanda Tangan */
        .signature-box { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 20px; 
            page-break-inside: avoid;
            break-inside: avoid; 
        }
        .signature-space { height: 45px; } /* Ruang ttd dipersingkat agar tidak makan tempat */
        .btn-print { display: inline-block; padding: 8px 16px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; font-family: Arial, sans-serif; border: none; cursor: pointer; }
        
        /* Sembunyikan elemen non-cetak saat print */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="container">
    <div class="text-center">
        <div class="fw-bold title">SURAT PERJANJIAN SEWA MENYEWA BARANG</div>
        <div class="subtitle">No. Invoice: <?= $data['booking']['invoice_no']; ?> | Platform: RentalMarket</div>
    </div>

    <p>Pada hari ini, tanggal <strong><?= date('d-m-Y', strtotime($data['booking']['created_at'])); ?></strong>, telah disepakati perjanjian sewa-menyewa barang melalui platform RentalMarket antara pihak-pihak di bawah ini:</p>

    <p class="fw-bold mb-0">PIHAK PERTAMA (Pemilik Barang)</p>
    <table class="table-data" style="width: 95%; margin-left: 15px;">
        <tr><td width="140">Nama</td><td width="10">:</td><td><?= htmlspecialchars($data['booking']['owner_name']); ?></td></tr>
        <tr><td>No. Telepon / WA</td><td>:</td><td><?= htmlspecialchars($data['booking']['owner_phone']); ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td><?= !empty($data['booking']['owner_address']) ? htmlspecialchars($data['booking']['owner_address']) : '-'; ?></td></tr>
    </table>

    <p class="fw-bold mb-0" style="margin-top: 6px;">PIHAK KEDUA (Penyewa)</p>
    <table class="table-data" style="width: 95%; margin-left: 15px;">
        <tr><td width="140">Nama</td><td width="10">:</td><td><?= htmlspecialchars($data['booking']['renter_name']); ?></td></tr>
        <tr><td>No. Telepon / WA</td><td>:</td><td><?= htmlspecialchars($data['booking']['renter_phone']); ?></td></tr>
        <tr><td>Alamat</td><td>:</td><td><?= !empty($data['booking']['renter_address']) ? htmlspecialchars($data['booking']['renter_address']) : '-'; ?></td></tr>
    </table>

    <p style="margin-top: 6px;">PIHAK PERTAMA sepakat menyewakan barang kepada PIHAK KEDUA dengan rincian berikut:</p>

    <table class="table-bordered">
        <tr>
            <td width="28%"><strong>Nama Barang</strong></td>
            <td><?= htmlspecialchars($data['booking']['item_name']); ?></td>
        </tr>
        <tr>
            <td><strong>Masa Sewa</strong></td>
            <td><?= date('d/m/Y', strtotime($data['booking']['start_date'])); ?> s/d <?= date('d/m/Y', strtotime($data['booking']['end_date'])); ?> (<?= $data['booking']['duration']; ?> Hari)</td>
        </tr>
        <tr>
            <td><strong>Total Harga Sewa</strong></td>
            <td>Rp <?= number_format($data['booking']['total_price'], 0, ',', '.'); ?></td>
        </tr>
    </table>

    <p class="fw-bold" style="margin-top: 6px;">Syarat dan Ketentuan:</p>
    <ol>
        <li>PIHAK KEDUA wajib menjaga kondisi fisik dan fungsi barang dengan baik selama masa sewa berlangsung.</li>
        <li>PIHAK KEDUA tidak diperkenankan memindahtangankan barang kepada pihak lain tanpa izin PIHAK PERTAMA.</li>
        <li>Apabila terjadi keterlambatan pengembalian, PIHAK KEDUA bersedia membayar denda sesuai kesepakatan.</li>
        <li>Kerusakan berat atau kehilangan barang sepenuhnya menjadi tanggung jawab PIHAK KEDUA untuk mengganti kerugian.</li>
    </ol>

    <p>Demikian surat perjanjian ini dibuat dan disetujui bersama oleh kedua belah pihak secara sadar tanpa paksaan.</p>

    <div class="signature-box">
        <div class="text-center" style="width: 40%;">
            <p><strong>PIHAK PERTAMA</strong></p>
            <div class="signature-space">
                <!-- Logika Menampilkan Gambar TTD -->
                <?php if(!empty($data['booking']['owner_signature'])): ?>
                    <img src="<?= $data['booking']['owner_signature']; ?>" alt="TTD Pemilik" style="max-height: 50px;">
                <?php endif; ?>
            </div>
            <p>( <?= htmlspecialchars($data['booking']['owner_name']); ?> )</p>
        </div>
        
        <div class="text-center" style="width: 40%;">
            <p><strong>PIHAK KEDUA</strong></p>
            <div class="signature-space">
                <?php if(!empty($data['booking']['renter_signature'])): ?>
                    <img src="<?= $data['booking']['renter_signature']; ?>" alt="TTD Penyewa" style="max-height: 50px;">
                <?php endif; ?>
            </div>
            <p>( <?= htmlspecialchars($data['booking']['renter_name']); ?> )</p>
        </div>
    </div>

</body>
</html>