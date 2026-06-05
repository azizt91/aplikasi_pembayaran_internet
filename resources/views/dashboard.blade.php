@extends('template.app')

@section('title', 'Dashboard')

@section('contents')

  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Paket</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jumlah_paket}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-fw fa-paper-plane fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
          <a class="text-primary stretched-link" href="{{ route('paket.view') }}">Lihat Detail</a>
          <div class="text-primary"><i class="fas fa-angle-right"></i></div>
      </div>
      </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Pelanggan Aktif</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jumlah_pelanggan_aktif}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-fw fa-user-friends fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
          <a class="text-success stretched-link" href="{{ route('pelanggan.aktif') }}">Lihat Detail</a>
          <div class="text-success"><i class="fas fa-angle-right"></i></div>
      </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                          Pelanggan Nonaktif</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_nonaktif }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-user-times fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
            <a class="text-danger stretched-link" href="{{ route('pelanggan.nonaktif') }}">Lihat Detail</a>
            <div class="text-danger"><i class="fas fa-angle-right"></i></div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                          Lunas Bulan Ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_lunas }}</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-fw fa-thumbs-up fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
              <a class="text-success stretched-link" href="{{ route('pelanggan.lunas') }}">Lihat Detail</a>
              <div class="text-success"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                          Belum Bayar Bulan Ini</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_pelanggan_belum_lunas }}</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-thumbs-down fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
              <a class="text-danger stretched-link" href="{{ route('pelanggan.belumLunas') }}">Lihat Detail</a>
              <div class="text-danger"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
              <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                          Pendapatan bulan ini</div>
                      <div id="pendapatan" class="h5 mb-0 font-weight-bold text-gray-800">Rp ••••••••</div>
                  </div>
                  <div class="col-auto">
                      <i class="fas fa-fw fa-arrow-circle-down fa-2x text-gray-300"></i>
                  </div>
              </div>
          </div>
          <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
              <a id="togglePendapatan" class="text-info stretched-link" href="javascript:void(0);" onclick="togglePendapatan()">Lihat</a>
              <div class="text-info"><i class="fas fa-angle-right"></i></div>
          </div>
      </div>
    </div>

    <!-- Pengeluaran Bulan Ini Card -->
<div class="col-xl-4 col-md-6 mb-4">
  <div class="card border-left-warning shadow h-100 py-2">
      <div class="card-body">
          <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                      Pengeluaran Bulan Ini</div>
                  <div id="pengeluaran" class="h5 mb-0 font-weight-bold text-gray-800">Rp ••••••••</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-fw fa-arrow-circle-up fa-2x text-gray-300"></i>
              </div>
          </div>
      </div>
      <div class="card-footer d-flex align-items-center justify-content-between small" style="background-color: #ffffff;">
          <a id="togglePengeluaran" class="text-warning stretched-link" href="javascript:void(0);" onclick="togglePengeluaran()">Lihat</a>
          <div class="text-warning"><i class="fas fa-angle-right"></i></div>
      </div>
  </div>
</div>
</div>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Lihat Pendapatan</h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <form class="form-horizontal" id="pendapatanForm" action="{{ route('dashboard') }}" method="GET">
          @csrf
          <div class="container">
            <!-- Dropdown Bulan -->
            <div class="row">
              <label class="col-md-4 control-label">Bulan</label>
              <div class="col-md-8">
                <div class="form-group">
                  <select name="bulan" id="bulan" class="custom-select" style="width: 100%;" required>
                    <option selected="selected">Pilih Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                      <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                  </select>
                </div>
              </div>
            </div>

            <!-- Dropdown Tahun -->
            <div class="row">
              <label class="col-md-4 control-label">Tahun</label>
              <div class="col-md-8">
                <div class="form-group">
                  <select name="tahun" id="tahun" class="custom-select" style="width: 100%;" required>
                    <option>Pilih Tahun</option>
                    @for($year = 2021; $year <= date('Y')+5; $year++)
                      <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                  </select>
                </div>
              </div>
            </div>

            <!-- Tombol Submit (Bisa dihilangkan jika tidak perlu lagi) -->
            <div class="row" style="display: none;">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary" name="Lihat">Lihat</button>
              </div>
            </div>
          </div>
        </form>
        <p></p>
      </div>
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="text-center" id="pendapatanResult">
          @if(isset($netRevenue))
          <div class="pendapatan-wrapper">
            <div class="pendapatan-item">
              <span class="pendapatan-label">Pendapatan:</span>
              <span class="pendapatan-value">{{ rupiah($totalRevenue) }}</span>
            </div>
            <div class="pendapatan-item">
              <span class="pendapatan-label">Pengeluaran:</span>
              <span class="pendapatan-value">{{ rupiah($pengeluaranBulanIni) }}</span>
            </div>
            <div class="pendapatan-item total">
              <span class="pendapatan-label">Total Pendapatan:</span>
              <span class="pendapatan-value">{{ rupiah($netRevenue) }}</span>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Canvas for the chart -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Ikhtisar Penghasilan</h6>
  </div>
  <div class="card-body">
    <div class="chart-area">
      <canvas id="myAreaChart"></canvas>
    </div>
  </div>
</div>

@endsection

@section('scripts')


<script>
  function togglePendapatan() {
      var pendapatanElement = document.getElementById("pendapatan");
      var toggleButton = document.getElementById("togglePendapatan");
      var pendapatanValue = "{{ rupiah($tagihanBulanIni) }}";

      if (pendapatanElement.innerText === "Rp ••••••••") {
          pendapatanElement.innerText = pendapatanValue;
          toggleButton.innerText = "Tutup";
      } else {
          pendapatanElement.innerText = "Rp ••••••••";
          toggleButton.innerText = "Lihat";
      }
  }

  function togglePengeluaran() {
      var pengeluaranElement = document.getElementById("pengeluaran");
      var toggleButton = document.getElementById("togglePengeluaran");
      var pengeluaranValue = "{{ rupiah($pengeluaranBulanIni) }}";

      if (pengeluaranElement.innerText === "Rp ••••••••") {
          pengeluaranElement.innerText = pengeluaranValue;
          toggleButton.innerText = "Tutup";
      } else {
          pengeluaranElement.innerText = "Rp ••••••••";
          toggleButton.innerText = "Lihat";
      }
  }
</script>

<script>
  function formatRupiah(value) {
    return 'Rp ' + parseFloat(value).toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  $(document).ready(function() {
    $('#bulan, #tahun').on('change', function() {
      var bulan = $('#bulan').val();
      var tahun = $('#tahun').val();
      if (bulan !== "Pilih Bulan" && tahun !== "Pilih Tahun") {
        $.ajax({
          url: '{{ route("dashboard") }}',
          method: 'GET',
          data: {
            bulan: bulan,
            tahun: tahun,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            $('#pendapatanResult').html(`
              <div class="pendapatan-wrapper">
                <div class="pendapatan-item">
                  <span class="pendapatan-label">Pendapatan:</span>
                  <span class="pendapatan-value">${formatRupiah(response.totalRevenue)}</span>
                </div>
                <div class="pendapatan-item">
                  <span class="pendapatan-label">Pengeluaran:</span>
                  <span class="pendapatan-value">${formatRupiah(response.pengeluaranBulanIni)}</span>
                </div>
                <div class="pendapatan-item total">
                  <span class="pendapatan-label">Total Pendapatan:</span>
                  <span class="pendapatan-value">${formatRupiah(response.netRevenue)}</span>
                </div>
              </div>
            `);

            // Update the chart with the new data
            myLineChart.data.datasets[0].data = response.pendapatan;
            myLineChart.data.datasets[1].data = response.pengeluaran;
            myLineChart.update();
          },
          error: function(xhr, status, error) {
            console.error('Error:', error);
          }
        });
      }
    });
  });

  // Initialize the chart with data from server-side variables
  var ctx = document.getElementById("myAreaChart");
  var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [
        {
          label: "Pendapatan",
          lineTension: 0.3,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: @json($pendapatan),
        },
        {
          label: "Pengeluaran",
          lineTension: 0.3,
          backgroundColor: "rgba(255, 99, 132, 0.05)",
          borderColor: "rgba(255, 99, 132, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(255, 99, 132, 1)",
          pointBorderColor: "rgba(255, 99, 132, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(255, 99, 132, 1)",
          pointHoverBorderColor: "rgba(255, 99, 132, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: @json($pengeluaran),
        }
      ],
    },
    options: {
      maintainAspectRatio: false,
      layout: {
        padding: {
          left: 10,
          right: 25,
          top: 25,
          bottom: 0
        }
      },
      scales: {
        xAxes: [{
          time: {
            unit: 'date'
          },
          gridLines: {
            display: false,
            drawBorder: false
          },
          ticks: {
            maxTicksLimit: 7
          }
        }],
        yAxes: [{
          ticks: {
            maxTicksLimit: 5,
            padding: 10,
            callback: function(value, index, values) {
              return formatRupiah(value);
            }
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }],
      },
      legend: {
        display: true
      },
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        titleMarginBottom: 10,
        titleFontColor: '#6e707e',
        titleFontSize: 14,
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        intersect: false,
        mode: 'index',
        caretPadding: 10,
        callbacks: {
          label: function(tooltipItem, chart) {
            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
            return datasetLabel + ': ' + formatRupiah(tooltipItem.yLabel);
          }
        }
      }
    }
  });
</script>

<style>
  .pendapatan-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .pendapatan-item {
    display: flex;
    justify-content: space-between;
    width: 100%;
    max-width: 300px;
    margin-bottom: 10px;
  }

  .pendapatan-label {
    font-weight: bold;
    margin-right: 10px;
  }

  .pendapatan-value {
    font-weight: normal;
  }

  .pendapatan-item.total .pendapatan-label,
  .pendapatan-item.total .pendapatan-value {
    font-weight: bold;
  }
</style>
@endsection
