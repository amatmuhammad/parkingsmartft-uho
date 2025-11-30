@extends('layouts.index')

@section('kontent')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Exit</h3>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item text-muted active" aria-current="page">Kendaraan Keluar</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-5 align-self-center">
            {{-- <div class="customize-input float-right">
                <button class="btn btn-outline-cyan btn-rounded">
                    <h5 id="date" class="pt-2"></h5>
                </button>
            </div> --}}
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Kendaraan Keluar Parkir</h4>

                    <div class="table-responsive">
                        <table id="reservationTable" class="table table-striped align-middle-center">
                            <thead class="text-center bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Pengguna</th>
                                    <th>Tipe Kendaraan</th>
                                    <th>Nomor Polisi</th>
                                    <th>Slot Parkir</th>
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Berakhir</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="reservationBody" class="text-center">
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>


// ================================
//  ESCAPE STRING AMAN UNTUK JS
// ================================
function escapeJsString(str) {
    if (!str) return "";
    return str.replace(/\\/g, "\\\\")
              .replace(/'/g, "\\'")
              .replace(/"/g, '\\"')
              .replace(/\n/g, "\\n")
              .replace(/\r/g, "\\r");
}


// ================================
//  DOCUMENT READY
// ================================
$(document).ready(function () {

    // Set tanggal
    // const d = new Date();
    // document.getElementById("date").innerHTML =
    //     `${String(d.getDate()).padStart(2,"0")}/${d.getMonth()+1}/${d.getFullYear()}`;

    // Init datatable
    $("#reservationTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        language: {
            search: "Search :",
            lengthMenu: "Show _MENU_ data",
            info: "Show _START_ to _END_ from _TOTAL_ data",
            paginate: { previous: "Previous", next: "Next" }
        }
    });

    // 🚀 LANGSUNG LOAD DATA TANPA DELAY
    loadActiveParkings();

    // Auto refresh tiap 3 detik
    setInterval(loadActiveParkings, 3000);
});



// ====================================================
//  LOAD REALTIME DATA — PERBAIKAN BUTTON Scan Out
// ====================================================
function loadActiveParkings() {
    $.ajax({
        url: "{{ route('completed.json') }}",
        method: "GET",
        success: function (reservations) {

            const table = $("#reservationTable").DataTable();

            // Hanya bersihkan isi tabel, TIDAK destroy
            table.clear();

            if (!reservations || reservations.length === 0) {
                table.row.add([
                    "-", "-", "-", "-", "-", "-", "-", 
                    "<span class='text-muted'>Tidak ada kendaraan yang telah selesai parkir.</span>"
                ]);
            } else {
                reservations.forEach((r, index) => {

                    const startTime = r.start_time 
                        ? moment(r.start_time).format("YYYY-MM-DD HH:mm")
                        : "-";

                    const endTime = r.end_time 
                        ? moment(r.end_time).format("YYYY-MM-DD HH:mm")
                        : "-";

                    const kendaraanBadge =
                        r.vehicle.vehicle_type === "car"
                            ? '<span class="badge badge-info text-white rounded-pill px-3 py-2">Mobil</span>'
                            : '<span class="badge badge-warning rounded-pill px-3 py-2">Motor</span>';

                    table.row.add([
                        index + 1,
                        `
                        <div class="d-flex align-items-center">
                            
                            <div>
                                <h6 class="mb-0 ml-3">${r.user.name}</h6>
                                <small class="text-muted ml-3">${r.user.email}</small>
                            </div>
                        </div>
                        `,
                        kendaraanBadge,
                        `<div class="text-center">${r.vehicle.plate_number}</div>`,
                        `<div class="text-center">${r.slot.slot_name}</div>`,
                        `<div class="text-center">${startTime}</div>`,
                        `<div class="text-center">${endTime}</div>`,
                        `<span class="badge badge-primary text-white rounded-pill px-3 py-2">Completed</span>`
                    ]);
                });
            }

            // draw(false) = tidak reset pagination, tidak scroll ke atas
            table.draw(false);  
        },
        error: function (xhr) {
            console.error("Gagal mengambil data", xhr);
        }
    });
}


</script>



@endsection