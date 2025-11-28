@extends('layouts.index')

@section('kontent')

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Manage User</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Manajemen User</li>
                        </ol>
                    </nav>
                </div>
            </div>
            {{-- <div class="col-5 align-self-center">
                <div class="customize-input float-right">
                    <button class="btn btn-outline-cyan btn-rounded">
                        <h5 id="date" class="pt-2"></h5>
                    </button>
                </div>
            </div> --}}
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-center mb-3">Manajemen Pengguna</h4>

                        <div class="table-responsive">
                            <table id="userTable" class="table table-striped align-middle text-center" style="width: 100%;">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>No. HP</th>
                                        <th>Jenis Kendaraan</th>
                                        <th>Plat Nomor</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail User -->
    <div class="modal fade" id="userDetailModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detail Pengguna</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body text-left">
                    <p><strong>Nama:</strong> <span id="dName"></span></p>
                    <p><strong>Email:</strong> <span id="dEmail"></span></p>
                    <p><strong>Role:</strong> <span id="dRole" class="badge badge-info px-2 py-1"></span></p>
                    <p><strong>No HP:</strong> <span id="dPhone"></span></p>

                    <hr>

                    <h6>Informasi Kendaraan</h6>
                    <p><strong>Jenis:</strong> <span id="dVehicleType"></span></p>
                    <p><strong>Plat Nomor:</strong> <span id="dPlate"></span></p>
                </div>

            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {

    // DataTables AJAX
   let table = $('#userTable').DataTable({
        ajax: "{{ route('users.data') }}",
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'name' },
            { data: 'email' },

            // Role dengan badge
            { 
                data: 'role',
                render: function(role){
                    let color = role === 'Admin' ? 'success' : 'primary';
                    return `<span class="badge badge-${color} badge-pill">${role}</span>`;
                }
            },

            { data: 'phone' },

            // Vehicle type badge
            { 
                data: 'vehicle_type',
                render: function(type){
                    if(type === '-' || !type) return '-';
                    let color = type === 'car' ? 'info' : 'warning';
                    return `<span class="badge badge-${color} badge-pill">${type}</span>`;
                }
            },

            { data: 'plate_number' },
            { data: 'created_at' },

            // Tombol Hapus
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-danger btn-sm delete-user"
                            data-id="${data.id}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    `;
                }
            }
        ]
    });

    setInterval(function () {
        table.ajax.reload(null, false); // false = tidak reset pagination
    }, 5000);

// Aksi Hapus
$(document).on('click', '.delete-user', function () {
    let id = $(this).data('id');

    if(confirm("Yakin ingin menghapus user ini? Data kendaraan juga akan terhapus!")) {
        $.ajax({
            url: "/users/delete/" + id,
            type: "DELETE",
            data: {
                "_token": "{{ csrf_token() }}"
            },
            success: function(res){
                alert(res.message);
                table.ajax.reload();
            }
        });
    }
});


    // Tampilkan modal detail
    $(document).on('click', '.view-user', function () {
        $("#dName").text($(this).data('name'));
        $("#dEmail").text($(this).data('email'));
        $("#dRole").text($(this).data('role'));
        $("#dPhone").text($(this).data('phone'));
        $("#dVehicleType").text($(this).data('vehicle_type'));
        $("#dPlate").text($(this).data('plate'));

        $("#userDetailModal").modal('show');
    });
});
</script>

@endsection
