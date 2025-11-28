<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/assets/images/fav.png') }}">
    <title>Smart Parking</title>
    <!-- Custom CSS -->
    <link href="{{ asset('assets/assets/extra-libs/c3/c3.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('assets/assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/dist/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
       @include('layouts.sidebar')

        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb and right sidebar toggle -->
           
            
            @yield('kontent')
            <!-- End Container fluid  -->
          
            <!-- footer -->
           
            @include('layouts.footer')
            
            <!-- End footer -->
           
        </div>
        
        <!-- End Page wrapper  -->
        
    </div>

    <!-- SweetAlert2 -->


<script>
    // ===============================
    // SWEET ALERT GLOBAL
    // ===============================

    const SwalGlobal = {

        // ALERT BERHASIL
        success(message = "Berhasil!") {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: message,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        },

        // ALERT GAGAL / ERROR
        error(message = "Terjadi kesalahan!") {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: message,
                showConfirmButton: true,
            });
        },

        // ALERT WARNING
        warning(message = "Peringatan!") {
            Swal.fire({
                icon: "warning",
                title: "Warning",
                text: message,
                showConfirmButton: true,
            });
        },

        // SUCCESS TOAST (muncul di sudut atas)
        toastSuccess(message = "Berhasil!") {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: "success",
                title: message
            });
        },

        // ERROR TOAST
        toastError(message = "Gagal!") {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: "error",
                title: message
            });
        },

        // KONFIRMASI GLOBAL
        confirm(message = "Apakah anda yakin?", callbackOK = null, callbackCancel = null) {
            Swal.fire({
                title: "Konfirmasi",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof callbackOK === "function") callbackOK();
                } else {
                    if (typeof callbackCancel === "function") callbackCancel();
                }
            });
        }
    };
</script>

    
    <script src="{{ asset('assets/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('assets/dist/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('assets/dist/js/custom.min.js') }}"></script>
    <!--This page JavaScript -->
    <script src="{{ asset('assets/assets/extra-libs/c3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/assets/extra-libs/c3/c3.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/assets/libs/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script> --}}
    <script src="{{ asset('assets/assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- Leaflet CSS -->
    

    {{-- <script src="{{ asset('assets/dist/js/pages/dashboards/dashboard1.min.js') }}"></script> --}}

     <script src="{{ asset('assets/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/pages/datatable/datatable-basic.init.js') }}"></script>
</body>

</html>