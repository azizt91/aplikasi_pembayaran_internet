<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Struk Pembayaran WiFi</title>

		<style>
			.invoice-box {
				max-width: 210mm; /* A4 width */
                margin: auto; /* Remove margin */
                padding: 30px;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
				
			}
			
			.footer {
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
			
		</style>
	</head>

	<body>
	@php
                      $namaBulan = [
                          1 => 'Januari',
                          2 => 'Februari',
                          3 => 'Maret',
                          4 => 'April',
                          5 => 'Mei',
                          6 => 'Juni',
                          7 => 'Juli',
                          8 => 'Agustus',
                          9 => 'September',
                          10 => 'Oktober',
                          11 => 'November',
                          12 => 'Desember',
                      ];
                  @endphp
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<img
										src="{{ url('template/img/APIKCORPORATION.png') }}"
										style="width: 100%; max-width: 300px"
									/>
								</td>

								<td>
									Invoice #: {{ $tagihan->id }}<br />
									ID Pelanggan: {{ $tagihan->id_pelanggan }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									Apik Corporation<br />
									Dukuhdamu<br />
									52461
								</td>

								<td>
									<b>{{ $tagihan->pelanggan->nama }}</b><br />
									Tgl Bayar: {{ date('d-M-Y', strtotime($tagihan->tgl_bayar)) }}<br/>
									Status Pembayaran : <b>LUNAS</b>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td>Untuk Tagihan</td>

					<td>Jumlah</td>
				</tr>

				<tr class="item last">
					<td>Pembayaran WiFi {{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>

					<td>Rp {{ number_format($tagihan->tagihan, 0, ',', '.') }}</td>
				</tr>

				<tr class="total">
					<td></td>

					<td>Total: Rp {{ number_format($tagihan->tagihan, 0, ',', '.') }}</td>
				</tr>
			</table>
			
			<div class="footer">
            <b>Terima kasih atas pembayaran Anda!</b><br/>
            <p></p>
            Apabila anda mengalami kendala jangan ragu untuk<br/>
            menghubungi kami di WhatsApp 085642828131
        </div>
		</div>
	</body>
</html>







