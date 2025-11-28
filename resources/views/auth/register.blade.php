<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Register</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/assets/images/fav.png') }}">
    <link href="{{ asset('assets/dist/css/style.min.css') }}" rel="stylesheet">
</head>

<body>

<style>
    .auth-wrapper {
        background-image: url("{{ asset('assets/assets/images/big/auth-bg4.png') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        padding: 20px;
    }

    .auth-box {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        overflow: hidden;
    }

    .form-control {
        height: 45px;
        font-size: 14px;
        border-radius: 10px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 37px;
        cursor: pointer;
        font-size: 18px;
    }

    button.btn-primary {
        height: 45px;
        border-radius: 10px;
    }

    button.btn-secondary {
        height: 45px;
        border-radius: 10px;
    }
</style>

<div class="main-wrapper">

    <div class="auth-wrapper d-flex justify-content-center align-items-center">
        <div class="auth-box row" style="width: 900px; max-width: 100%;">

            <!-- Left Image -->
            <div class="col-lg-6 col-md-5 d-none d-md-block p-0">
                <img src="{{ asset('assets/assets/images/tp.jpg') }}" 
                     style="width:100%; height:100%; object-fit:cover;">
            </div>

            <!-- Right Form -->
            <div class="col-lg-6 col-md-7 bg-white p-5">

                <div class="text-center mb-3">
                    <img src="{{ asset('assets/assets/images/fav.png') }}" 
                         style="height: 90px;">
                </div>

                <h3 class="text-center mb-2">Create Account</h3>
                <p class="text-center text-muted mb-4">Fill the form below to register a new account</p>

                <!-- Error Alerts -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger text-center">{{ session('error') }}</div>
                @endif

                <!-- FORM -->
                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name">Full Name</label>
                        <input class="form-control" id="name" type="text" name="name"
                               placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input class="form-control" id="email" type="email" name="email"
                               placeholder="Enter your email" required>
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <label for="password">Password</label>
                        <input class="form-control" id="password" type="password" name="password"
                               placeholder="Enter your password" required>
                        <span class="password-toggle" id="togglePwd">👁️</span>
                    </div>

                    <div class="form-group mb-4 position-relative">
                        <label for="password_confirmation">Confirm Password</label>
                        <input class="form-control" id="password_confirmation" type="password"
                               name="password_confirmation" placeholder="Confirm your password" required>
                        <span class="password-toggle" id="togglePwd2">👁️</span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mb-3">Register</button>

                    <a href="{{ route('login') }}" class="btn btn-secondary btn-block">Back to Login</a>

                </form>

            </div>
        </div>
    </div>

</div>

<script src="{{ asset('assets/assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script>
    const toggle1 = document.getElementById('togglePwd');
    const toggle2 = document.getElementById('togglePwd2');
    const pwd = document.getElementById('password');
    const pwd2 = document.getElementById('password_confirmation');

    toggle1.onclick = () => {
        const type = pwd.type === 'password' ? 'text' : 'password';
        pwd.type = type;
        toggle1.textContent = type === 'password' ? '👁️' : '🙈';
    };

    toggle2.onclick = () => {
        const type = pwd2.type === 'password' ? 'text' : 'password';
        pwd2.type = type;
        toggle2.textContent = type === 'password' ? '👁️' : '🙈';
    };
</script>

</body>
</html>
