@extends('layouts.index')

@section('kontent')

<style>
    .profile-container {
        max-width: 900px;
        margin: 20px auto;
        background: #fff;
        border-radius: 15px;
        padding: 25px 30px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    .profile-header {
        text-align: center;
        margin-bottom: 25px;
    }
    .profile-header img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #4e73df;
    }
    .profile-header h3 { margin-top: 15px; font-weight: bold; }
    .form-control { height: 45px; border-radius: 10px; }
    .btn-primary, .btn-danger { height: 45px; border-radius: 10px; }
    label { font-weight: 600; }
</style>

<div class="profile-container">

    <div class="profile-header">
        <h3>{{ Auth::user()->name }}</h3>
        <p class="text-muted">{{ Auth::user()->email }}</p>
    </div>

    <form id="formUpdateProfile">
    @csrf

    <h5 class="mb-3 text-primary"><strong>Account Information</strong></h5>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label>Full Name</label>
            <input type="text" class="form-control"
                   value="{{ Auth::user()->name }}" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Email</label>
            <input type="text" class="form-control"
                   value="{{ Auth::user()->email }}" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control"
                   value="{{ Auth::user()->phone }}" required>
        </div>

        <div class="col-md-6 mb-3">
            <label>New Password (optional)</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Enter new password">
        </div>

        <div class="col-md-6 mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Confirm new password">
        </div>

    </div>

    <button class="btn btn-primary btn-block mt-2">Save Changes</button>
</form>


    <!-- VEHICLE SECTION -->
    <hr class="my-4">

    <h5 class="mb-3 text-primary"><strong>Your Vehicles</strong></h5>

    <button id="btnAddVehicle" 
            class="btn btn-primary btn-block mt-3" 
            data-toggle="modal" 
            data-target="#modalAddVehicle"
            style="{{ $vehicles->isEmpty() ? '' : 'display:none' }}">
        Add Vehicle
    </button>


    <table class="table table-striped" id="tableVehicle">
        <thead>
            <tr>
                <th>Plate Number</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vehicles as $v)
                <tr id="vehicle-{{ $v->id }}">
                    <td>{{ $v->plate_number }}</td>
                    <td>{{ $v->vehicle_type }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm deleteVehicle" data-id="{{ $v->id }}">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


</div>


<!-- MODAL ADD VEHICLE -->
<div class="modal fade" id="modalAddVehicle" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vehicle</h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formAddVehicle">

                    @csrf

                    <label>Plate Number</label>
                    <input type="text" name="plate_number" class="form-control" required>

                    <label class="mt-2">Vehicle Type</label>
                    <select name="vehicle_type" class="form-control" required>
                        <option value="">-- Select Vehicle Type --</option>
                        <option value="car">Car</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-block mt-3">
                        Save Vehicle
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- AJAX SCRIPT -->
<script>
/* ======================================================
   UPDATE PROFILE AJAX + SWEETALERT
====================================================== */
    $("#formUpdateProfile").on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('user.profile.update') }}",
            method: "POST",
            data: formData,
            success: function(response) {

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            },

            error: function(xhr) {
                if (xhr.status === 422) {
                    let errorText = "";
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorText += value + "<br>";
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorText
                    });
                }
            }
        });
    });



    $('#formAddVehicle').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "/profile/vehicle/add",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {

                Swal.fire({
                    icon: "success",
                    title: "Vehicle Added",
                    text: res.message,
                });

                // Append row realtime ke tabel
                $('#tableVehicle tbody').append(`
                    <tr id="vehicle-${res.data.id}">
                        <td>${res.data.plate_number}</td>
                        <td>${res.data.vehicle_type.charAt(0).toUpperCase() + res.data.vehicle_type.slice(1)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm deleteVehicle" data-id="${res.data.id}">
                                Delete
                            </button>
                        </td>
                    </tr>
                `);

                // Hide tombol Add Vehicle supaya tidak muncul lagi
                $('#btnAddVehicle').hide();

                $('#modalAddVehicle').modal('hide');
                $('#formAddVehicle')[0].reset();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: xhr.responseJSON.message,
                });
            }
        });
    });



    $(document).on('click', '.deleteVehicle', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This vehicle will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "/profile/vehicle/" + id + "/delete",
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){

                        $('#vehicle-' + id).remove();

                            if($('#tableVehicle tbody tr').length === 0){
                                $('#btnAddVehicle').show();
                            }

                        Swal.fire("Deleted!", res.message, "success");
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                        Swal.fire("Error", "Failed to delete vehicle", "error");
                    }
                });

            }
        });
    });



</script>

@endsection
