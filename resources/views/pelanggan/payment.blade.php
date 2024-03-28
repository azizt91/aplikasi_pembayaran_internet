@extends('layout.app')

@section('contents')
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .bank-info {
        background-color: #f9f9f9;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .bank-info h2 {
        margin-top: 0;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }
    .bank-info table {
        width: 100%;
        border-collapse: collapse;
    }
    .bank-info th, .bank-info td {
        padding: 10px;
        text-align: left;
    }
    .bank-info img {
        vertical-align: middle;
        margin-right: 10px;
    }
    .copy-icon {
        color: blue; /* Ubah warna ikon sesuai keinginan */
        cursor: pointer;
        margin-left: 5px;
    }
    .whatsapp-button {
        background-color: #25D366;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

</style>
</head>
<body>

<div class="container">
    <div class="bank-info">
        <h2>Informasi Pembayaran</h2>
        <p>Silakan transfer jumlah pembayaran ke salah satu rekening berikut :</p>
        <table>
            <tr>
                <th>Bank</th>
                <th>Nama</th>
                <th>Nomor Rek.</th>
            </tr>
            <tr>
                <td>
                    <img src="{{ asset('template/img/SeaBank.svg') }}" alt="Logo SeaBank" height="30">
                </td>
                <td>TAUFIQ AZIZ</td>
                <td>
                    901307925714
                    <!-- Gunakan ikon Font Awesome untuk salin -->
                    <i class="fas fa-copy copy-icon" onclick="copyText('901307925714')"></i>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{{ asset('template/img/Bank_Syariah_Indonesia.svg') }}" alt="Logo BSI" height="30">
                </td>
                <td>TAUFIQ AZIZ</td>
                <td>
                    7211806138
                    <!-- Gunakan ikon Font Awesome untuk salin -->
                    <i class="fas fa-copy copy-icon" onclick="copyText('7211806138')"></i>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{{ asset('template/img/PermataBank_logo.svg') }}" alt="Logo Bank Permata" height="30">
                </td>
                <td>TAUFIQ AZIZ</td>
                <td>
                    9924712438
                    <!-- Gunakan ikon Font Awesome untuk salin -->
                    <i class="fas fa-copy copy-icon" onclick="copyText('9924712438')"></i>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{{ asset('template/img/ShopeePay.svg') }}" alt="Logo ShopeePay" height="30">
                </td>
                <td>AZIZT91</td>
                <td>
                    081914170701
                    <!-- Gunakan ikon Font Awesome untuk salin -->
                    <i class="fas fa-copy copy-icon" onclick="copyText('081914170701')"></i>
                </td>
            </tr>
        </table>
        <br>
        <p>Anda juga bisa melakukan pembayaran langsung ke rumah,
        Silahkan Konfirmasi pembayaran anda agar pembayaran anda selalu ter-update </p>
        <button class="whatsapp-button" onclick="openWhatsApp()">Konfirmasi via WhatsApp</button>
    </div>


</div>

<script>
    function copyText(text) {
        const input = document.createElement('textarea');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('Nomor Rekening Disalin: ' + text);
    }

    function openWhatsApp() {
        window.open("https://api.whatsapp.com/send?phone=+6281914170701&text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran.");
    }
</script>

@endsection