    <!-- resources/views/reservation/index.blade.php -->
    @extends('layouts.index')

    @section('kontent')

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Reserved</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Reservasi Parkir</li>
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


    <div class="container mx-auto mt-5">
        
        <div class="card p-3 shadow-sm">

            <div class="d-flex justify-content-between align-items-center mb-5">
                <h4 class="mb-0 font-weight-bold text-dark">Daftar Reservasi Parkir</h4>

                <button class="btn btn-rounded btn-primary" data-toggle="modal" data-target="#addReservationModal">
                    Tambah Reservasi
                </button>
            </div>
            <div class="table-responsive">
                <table id="reservationTable" class="table table-striped align-middle ">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Kendaraan</th>
                            <th>Slot Parkir</th>
                            <th>Status</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Berakhir</th>
                            <th>QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $r)
                        <tr id="reservation-row-{{ $r->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $r->user->name }}</td>

                            <!-- Kendaraan -->
                            <td>
                                @if($r->vehicle->vehicle_type == 'car')
                                    <span class="badge badge-pill bg-primary text-white">CAR</span>
                                @else
                                    <span class="badge badge-pill bg-warning text-dark">MOTORCYCLE</span>
                                @endif
                                {{ $r->vehicle->plate_number }}
                            </td>

                            <!-- Slot -->
                            <td>
                                <span class="badge badge-pill bg-success text-white">{{ $r->slot->slot_name }}</span>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($r->status == 'completed')
                                    <span class="badge badge-pill bg-primary text-white">Completed</span>
                                @elseif($r->status == 'expired')
                                    <span class="badge badge-pill bg-danger text-white">Expired</span>
                                @elseif($r->status == 'booked')
                                    <span class="badge badge-pill bg-warning text-dark">Booked</span>
                                @elseif($r->status == 'active')
                                    <span class="badge bg-success badge-pill text-white">Active</span>
                                @endif
                            </td>

                            <!-- Waktu -->
                            <td>{{ $r->start_time ? $r->start_time->format('d-m-Y H:i') : '-' }}</td>
                            <td>{{ $r->end_time ? $r->end_time->format('d-m-Y H:i') : '-' }}</td>

                        <!-- QR Code -->
                                <td>
                                    @php
                                        $scanUrl = "https://smartparkingft.com/scan-in/" . $r->qrcode_token;
                                    @endphp

                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($scanUrl) }}" width="70">
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </div>



    <!-- MODAL TAMBAH RESERVASI -->
    <div class="modal fade" id="addReservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Reservasi</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div id="alert-error-modal" class="alert alert-danger d-none"></div>

                    <form id="reservationForm">
                        @csrf

                        <div class="row mb-3">
                            <!-- USER -->
                            <div class="col-md-6">
                                <label>User</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- KENDARAAN -->
                            <div class="col-md-6">
                                <label>Kendaraan</label>
                                <select name="vehicle_id" class="form-control" id="vehicleSelect" required>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">
                                            {{ $v->plate_number }} ({{ $v->vehicle_type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">

                            <!-- SLOT PARKIR -->
                            <div class="col-md-6">
                                <label>Slot Parkir</label>
                                <select name="slot_id" class="form-control" id="slotSelect" required>
                                    <option value="">-- Pilih Slot --</option>
                                </select>
                            </div>

                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="btnSaveReservation">Simpan</button>
                </div>

            </div>
        </div>
    </div>





    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
    var table;

    $(document).ready(function() {
        // Inisialisasi DataTable
        table = $('#reservationTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            stateSave: true,        // simpan posisi scroll & pagination
            deferRender: true,   
            language: {
                search: "Search :",
                lengthMenu: "Show _MENU_ data",
                info: "Show _START_ to _END_ from _TOTAL_ data",
                paginate: { previous: "Previous", next: "Next" }
            }
        });

        // Refresh data secara real-time
        setInterval(loadReservations, 3000);
    });


    function loadReservations() {
    $.ajax({
        url: "{{ url('/reservations/json') }}",
        type: "GET",
        dataType: "json",
        success: function(reservations) {

            table.clear(); // HAPUS semua row biar tidak double

            reservations.forEach((r, index) => {

                let kendaraan = (r.vehicle?.vehicle_type === 'car')
                    ? `<span class="badge badge-pill bg-primary text-white">CAR</span> ${r.vehicle?.plate_number}`
                    : `<span class="badge badge-pill bg-warning text-dark">MOTORCYCLE</span> ${r.vehicle?.plate_number}`;

                let statusBadge = "";
                if (r.status === 'completed') statusBadge = `<span class="badge badge-pill bg-primary text-white">Completed</span>`;
                if (r.status === 'expired')   statusBadge = `<span class="badge badge-pill bg-danger text-white">Expired</span>`;
                if (r.status === 'booked')    statusBadge = `<span class="badge badge-pill bg-warning text-dark">Booked</span>`;
                if (r.status === 'active')    statusBadge = `<span class="badge badge-pill bg-success text-white">Active</span>`;

                let qrUrl = `https://smartparkingft.com/scan-in/${r.qrcode_token}`;

                let row = table.row.add([
                    index + 1,
                    r.user?.name ?? "-",
                    kendaraan,
                    `<span class="badge bg-success badge-pill text-white">${r.slot?.slot_name ?? "-"}</span>`,
                    statusBadge,
                    r.start_time ? moment(r.start_time).format("DD-MM-YYYY HH:mm") : "-",
                    r.end_time   ? moment(r.end_time).format("DD-MM-YYYY HH:mm") : "-",
                    `<img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=${encodeURIComponent(qrUrl)}" width="70">`
                ]);

            });

            table.draw(false); // REDRAW tabel
        }
    });
}




    $(document).ready(function () {

        // Ketika kendaraan dipilih
        $('#vehicleSelect').on('change', function () {
            var vehicleId = $(this).val();

            // Kosongkan slot dulu
            $('#slotSelect').html('<option value="">Loading...</option>');

            // Jika dropdown kosong
            if (vehicleId === "") {
                $('#slotSelect').html('<option value="">-- Pilih Slot --</option>');
                return;
            }

            // Ajax request ambil slot berdasarkan jenis area (car / motorcycle)
            $.ajax({
                url: '/get-slots-by-vehicle/' + vehicleId,
                type: 'GET',
                success: function (data) {

                    $('#slotSelect').html('<option value="">-- Pilih Slot --</option>');

                    if (data.length === 0) {
                        $('#slotSelect').append('<option value="">Tidak ada slot tersedia</option>');
                    }

                    // Tambahkan hasil slot ke dropdown
                    data.forEach(function (slot) {
                        $('#slotSelect').append(
                            '<option value="' + slot.id + '">' +
                                slot.slot_name + ' (' + slot.area.type + ')' + (' - ' + slot.status) +
                            '</option>'
                        );
                    });
                },
                error: function () {
                    $('#slotSelect').html('<option value="">Gagal memuat slot</option>');
                }
            });

        });

    });


    // Simpan reservasi
    document.getElementById('btnSaveReservation').addEventListener('click', function() {
        let form = document.getElementById('reservationForm');
        let formData = new FormData(form);

        fetch("{{ route('reservations.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                let errBox = document.getElementById('alert-error-modal');
                errBox.classList.remove('d-none');
                errBox.innerHTML = data.message;
                return;
            }
            
            // Tutup modal dan reset form
            $('#addReservationModal').modal('hide');
            form.reset();
            document.getElementById('alert-error-modal').classList.add('d-none');
            
            // Refresh data tabel
            loadReservations();
            showToast("Reservasi berhasil ditambahkan!", "success");
        })
        .catch(error => {
            console.error('Error:', error);
            showToast("Terjadi Kesalahan", "error");
        });
    });
    </script>

    <!-- Tambahkan moment.js untuk format tanggal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    @endsection