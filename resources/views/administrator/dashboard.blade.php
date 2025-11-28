@extends('layouts.index')

@section('kontent')

<style>
    #map {
        width: 100%;
        height: 500px;
        z-index: 1;
    }

</style>

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Good Morning Administrator!</h3>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-5 align-self-center">
            <div class="customize-input float-right">
                <button class="btn btn-outline-cyan btn-rounded">
                    <h5 id="date" class="pt-2"></h5>
                </button>
            </div>
        </div>
    </div>
</div>
           
<!-- End Bread crumb and right sidebar toggle -->

<!-- Container fluid  -->

<div class="container-fluid">
    <!-- Start First Cards -->
    <div class="card-group">
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 font-weight-medium" id="count-active"></h2>
                            <span class="badge bg-success font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none">Parking</span>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Vehicle Parked</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="log-in"></i> <i class="fa-solid fa-right-to-bracket"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 font-weight-medium" id="count-booked"></h2>
                            <span class="badge bg-warning font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none">Reserved Slot</span>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Parking Reservation</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="plus-square"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-right">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <div class="d-inline-flex align-items-center">
                            <h2 class="text-dark mb-1 font-weight-medium" id="count-completed"></h2>
                            <span
                                class="badge bg-danger font-12 text-white font-weight-medium badge-pill ml-2 d-md-none d-lg-block">Exit</span>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Vehicle exit</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="log-out"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex d-lg-flex d-md-block align-items-center">
                    <div>
                        <h2 class="text-dark mb-1 font-weight-medium" id="count-slots"></h2>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Available Slot</h6>
                    </div>
                    <div class="ml-auto mt-md-3 mt-lg-0">
                        <span class="opacity-7 text-muted"><i data-feather="grid"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- End First Cards -->
    
  <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Recent Activity</h4>

                <!-- 🔹 Keterangan Warna (Legend Manual di atas grafik) -->
                <div class="mb-3 text-center">
                    <span style="display:inline-block; width:18px; height:18px; background-color:#f6c23e; border-radius:3px; margin-right:5px;"></span>
                    <span class="me-3">Booking</span>

                    <span style="display:inline-block; width:18px; height:18px; background-color:#1cc88a; border-radius:3px; margin-right:5px;"></span>
                    <span class="me-3">Parking</span>

                    <span style="display:inline-block; width:18px; height:18px; background-color:#e74a3b; border-radius:3px; margin-right:5px;"></span>
                    <span>Exit</span>
                </div>

                <!-- Grafik -->
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->


</div>


{{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

<script>
    function loadDashboard() {
        $.get("/dashboard/json", function(res){

            // UPDATE CARD
            $("#count-active").text(res.active);
            $("#count-booked").text(res.booked);
            $("#count-completed").text(res.completed);
            $("#count-slots").text(res.slots);

            // UPDATE CHART
            activityChart.data.datasets[0].data = [
                res.booked,
                res.active,
                res.completed
            ];
            activityChart.update();
        });
    }

    // Jalankan realtime setiap 3 detik
    setInterval(loadDashboard, 3000);
    loadDashboard();


    // === FIX: SATUKAN CHART AGAR TIDAK TABRAKAN ===
    var ctx = document.getElementById('activityChart').getContext('2d');
    var activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Booking", "Parking", "Exit"],
            datasets: [{
                label: "Jumlah Kendaraan",
                data: @json([$booked, $active, $completed]),
                backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Grafik Aktivitas Kendaraan'
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>



@endsection