<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .footer {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/public/template/img/sn.png" alt="Logo">
            <p class="centered">Struk Pembayaran Internet</p>
        </div>
        <div class="content">
            <p>ID Pelanggan: {{ $tagihan->id_pelanggan }}</p>
            <p>Nama Pelanggan: {{ $tagihan->pelanggan->nama }}</p>
            <p>Bulan/Tahun: {{ $tagihan->bulan }}/{{ $tagihan->tahun }}</p>
            <p>Tagihan: Rp {{ number_format($tagihan->tagihan, 0, ',', '.') }}</p>
            <p>Status: Lunas</p>
            <p>Tanggal Bayar: {{ date('d-M-Y', strtotime($tagihan->tgl_bayar)) }}</p>
        </div>
        <div class="footer">
            Terima kasih atas pembayaran Anda!
        </div>
    </div>
</body>
</html>






