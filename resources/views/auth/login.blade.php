<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Login</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/assets/images/fav.png') }}">
    <link href="{{ asset('assets/dist/css/style.min.css') }}" rel="stylesheet">
</head>

<body>

    <style>
        /* Hapus overlay putih yang menutupi background */
        .auth-wrapper::before {
            background: transparent !important;
        }

        /* Pastikan background kelihatan penuh */
        .auth-wrapper {
            background-image: url("{{ asset('assets/assets/images/big/auth-bg4.png') }}") !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            height: 100vh !important;
        }

        /* Perbesar box agar tidak kecil di kiri */
        .auth-box {
            background: transparent !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            border-radius: 20px;
        }

        /* Paksa auth-box agar rounded */
        .auth-box {
            border-radius: 20px !important;
            overflow: hidden !important; /* supaya gambar kiri juga ikut melengkung */
        }

        /* Kolom kiri jangan bikin radius hilang */
        .auth-box .modal-bg-img {
            border-radius: 0 !important;
        }
        /* Jika ingin box tetap di tengah */
        .auth-wrapper .auth-box {
            margin: auto !important;
            border-radius: 10px;
        }
    </style>

    <div class="main-wrapper">

        <!-- Loader -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>

        <!-- Background -->
       <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">


            <div class="auth-box row rounded" style="width: 950px; max-width: 100%;">

                <!-- Left Image -->
                <div class="col-lg-7 col-md-6 modal-bg-img"
                    style="background-image: url({{ asset('assets/assets/images/tp.jpg') }}); background-size: cover; background-position: center;">
                </div>

                <!-- Right Login Form -->
                <div class="col-lg-5 col-md-6 bg-white">
                    <div class="p-4">

                        <div class="text-center">
                            <img src="{{ asset('assets/assets/images/fav.png') }}" alt="Logo"
                                style="height:100px; width:100px;">
                        </div>

                        <h2 class="mt-3 text-center">Sign In</h2>
                        <p class="text-center">Enter your email and password to access your account.</p>

                        <!-- Alert error -->
                        @if(session('error'))
                            <div class="alert alert-danger text-center">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- FORM LOGIN -->
                        <form action="{{ url('/login') }}" method="POST" class="mt-4">
                            @csrf

                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-dark" for="email">Email</label>
                                        <input class="form-control" id="email" type="email" name="email"
                                            placeholder="Enter your email" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group position-relative">
                                        <label class="text-dark" for="pwd">Password</label>
                                        <input class="form-control" id="pwd" type="password" name="password"
                                            placeholder="Enter your password" required>

                                        <!-- Icon Mata -->
                                        <span id="togglePwd"
                                            style="position:absolute; right:15px; top:38px; cursor:pointer; font-size:18px;">
                                            👁️
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-block btn-primary">Sign In</button>
                                </div>

                                <div class="col-lg-12 text-center mt-3 mb-5">
                                    <a href="{{ route('register') }}" class="btn btn-block btn-secondary">Sign Up</a>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
                <!-- End Right Column -->

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <script>
        $(".preloader").fadeOut();

        const pwd = document.getElementById('pwd');
        const toggle = document.getElementById('togglePwd');

        toggle.addEventListener('click', function () {
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);

            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>

</body>

</html>
