@extends('layouts.index')

@section('kontent')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Parked</h3>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                        <li class="breadcrumb-item text-muted active" aria-current="page">Kendaraan Terparkir</li>
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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Kendaraan Sedang Parkir</h4>

                    <div class="table-responsive">
                        <table id="reservationTable" class="table table-striped align-middle">
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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reservations as $index => $reservation)
                                    <tr id="row-reservation-{{ $reservation->id }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://i.pravatar.cc/45?img={{ $index + 1 }}" class="rounded-circle me-2" width="45" height="45">
                                                <div>
                                                    <h6 class="mb-0">{{ $reservation->user->name ?? '-' }}</h6>
                                                    <small class="text-muted">{{ $reservation->user->email ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($reservation->vehicle->vehicle_type === 'car')
                                                <span class="badge badge-info text-white rounded-pill px-3 py-2">Mobil</span>
                                            @elseif ($reservation->vehicle->vehicle_type === 'motorcycle')
                                                <span class="badge badge-secondary rounded-pill px-3 py-2">Motor</span>
                                            @else
                                                <span class="badge badge-light text-dark rounded-pill px-3 py-2">Tidak Diketahui</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $reservation->vehicle->plate_number ?? '-' }}</td>
                                        <td class="text-center">{{ $reservation->slot->slot_name ?? '-' }}</td>
                                        <td class="text-center">{{ $reservation->start_time ? \Carbon\Carbon::parse($reservation->start_time)->format('Y-m-d H:i') : '-' }}</td>
                                        <td class="text-center">{{ $reservation->end_time ? \Carbon\Carbon::parse($reservation->end_time)->format('Y-m-d H:i') : '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success text-white rounded-pill px-3 py-2">Active</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary"
                                                onclick="openScanOutModal(
                                                    {{ $reservation->id }},
                                                    '{{ $reservation->user->name }}',
                                                    '{{ $reservation->vehicle->plate_number }}',
                                                    '{{ $reservation->slot->slot_name }}',
                                                    '{{ $reservation->start_time }}',
                                                    '{{ $reservation->qrcode_token }}'
                                                )">
                                                Scan Out
                                            </button>

                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-data-row">
                                        <td colspan="9" class="text-center text-muted py-4">
                                            Tidak ada kendaraan yang sedang parkir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Scan Out (Bootstrap 4) -->
<div class="modal fade" id="scanOutModal" tabindex="-1" role="dialog" aria-labelledby="scanOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanOutModalLabel">Konfirmasi Keluar Parkir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memproses keluar untuk kendaraan berikut?</p>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Pengguna:</strong> <span id="modal-user"></span></li>
                    <li class="list-group-item"><strong>Nomor Polisi:</strong> <span id="modal-plate"></span></li>
                    <li class="list-group-item"><strong>Slot:</strong> <span id="modal-slot"></span></li>
                    <li class="list-group-item"><strong>Waktu Masuk:</strong> <span id="modal-start"></span></li>
                </ul>

                <div class="text-center">
                    <h6>QR Code untuk Scan Out</h6>
                    <p class="text-muted">Scan QR Code ini untuk memproses keluar parkir</p>
                    <div id="qrcode-container" class="d-flex justify-content-center">
                        <img id="qrcode-out" src="" alt="QR Code Scan Out" width="200" height="200">
                    </div>
                    <small class="text-muted">Tampilkan QR ini ke scanner di pintu keluar</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                {{-- <button type="button" class="btn btn-danger" id="confirmScanOutBtn">Proses Keluar</button> --}}
            </div>
        </div>
    </div>
</div>

@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>

// ================================
//  GLOBAL FUNCTION — HARUS DI WINDOW
// ================================
window.openScanOutModal = function(id, userName, plateNumber, slotName, startTime, token) {

    if (!token) {
        alert("Token scan-out tidak ditemukan!");
        return;
    }

    // URL untuk proses scan out (dibaca oleh scanner)
    const scanOutUrl = `https://fe7875cce83b.ngrok-free.app/scan-out/token/${token}`;

    // QR Code Generator API
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(scanOutUrl)}`;

    // Set nilai modal
    $("#modal-user").text(userName);
    $("#modal-plate").text(plateNumber);
    $("#modal-slot").text(slotName);
    $("#modal-start").text(startTime);

    // Tampilkan QR Code
    $("#qrcode-out").attr("src", qrUrl);

    // Tampilkan modal
    $("#scanOutModal").modal("show");

    // Simpan token untuk tombol konfirmasi
    $("#confirmScanOutBtn").data("token", token);
};


// ================================
//  KONFIRMASI SCAN OUT (POST)
// ================================
$("#confirmScanOutBtn").on("click", function () {
    let token = $(this).data("token");

    if (!token) {
        alert("Token tidak ditemukan!");
        return;
    }

    $.ajax({
        url: "/scan-out/" + token,
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            $("#scanOutModal").modal("hide");
            alert("Kendaraan berhasil keluar parkir.");
            loadActiveParkings();
        },
        error: function (xhr) {
            console.error(xhr);
            alert("Gagal memproses scan-out.");
        }
    });
});


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
    const d = new Date();
    document.getElementById("date").innerHTML =
        `${String(d.getDate()).padStart(2,"0")}/${d.getMonth()+1}/${d.getFullYear()}`;

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

    // Auto refresh tiap 3 detik
    setInterval(loadActiveParkings, 3000);
});


// ====================================================
//  LOAD REALTIME DATA — PERBAIKAN BUTTON Scan Out
// ====================================================
function loadActiveParkings() {
    $.ajax({
        url: "{{ route('parked.json') }}",
        method: "GET",
        success: function (reservations) {

            const table = $("#reservationTable").DataTable();
            const currentPage = table.page();
            table.clear();

           if (!reservations || reservations.length === 0) {
                table.row.add([
                    "-", "-", "-", "-", "-", "-", "-", "-","-", 
                    "<span class='text-muted'>Tidak ada kendaraan yang sedang parkir.</span>"
                ]);
            }else {
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
                            : '<span class="badge badge-secondary rounded-pill px-3 py-2">Motor</span>';

                    const userName = escapeJsString(r.user.name);
                    const plate = escapeJsString(r.vehicle.plate_number);
                    const slot = escapeJsString(r.slot.slot_name);

                    // FIX: Lengkapi 6 parameter
                    const actionBtn = `
                        <button class="btn btn-sm btn-primary"
                            onclick="openScanOutModal(
                                ${r.id},
                                '${userName}',
                                '${plate}',
                                '${slot}',
                                '${startTime}',
                                '${r.qrcode_token}'
                            )">
                            Scan Out
                        </button>
                    `;

                    table.row.add([
                        index + 1,
                        `
                        <div class="d-flex align-items-center">
                            <img src="https://i.pravatar.cc/45?img=${index + 1}" class="rounded-circle me-2" width="45" height="45">
                            <div>
                                <h6 class="mb-0">${r.user.name}</h6>
                                <small class="text-muted">${r.user.email}</small>
                            </div>
                        </div>
                        `,
                        kendaraanBadge,
                        `<div class="text-center">${plate}</div>`,
                        `<div class="text-center">${slot}</div>`,
                        `<div class="text-center">${startTime}</div>`,
                        `<div class="text-center">${endTime}</div>`,
                        `<div class="text-center">
                            <span class="badge badge-success text-white rounded-pill px-3 py-2">Active</span>
                        </div>`,
                        `<div class="text-center">${actionBtn}</div>`
                    ]);
                });
            }

            table.draw(false);
            table.page(currentPage).draw("page");
        },
        error: function (xhr) {
            console.error("Gagal mengambil data", xhr);
        }
    });
}

</script>

