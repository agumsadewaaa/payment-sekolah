<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <title>Login | SMK YPE SAMPANG</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Vendor & Style -->
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="h-100">
    <div class="login-account">
        <div class="row h-100">
            
            <!-- Left Side -->
            <div class="col-lg-6 align-self-start">
                <div class="account-info-area" style="background-image: url({{ asset('images/rainbow.gif') }})">
                    <div class="login-content text-white">
                        <p class="sub-title">Sistem Informasi Akademik</p>
                        <h1 class="title">SMK YPE <span>Sampang</span></h1>
                        <p class="text">Silakan login dengan akun Anda untuk melanjutkan ke dashboard.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="col-lg-6 col-md-7 col-sm-12 mx-auto align-self-center">
                <div class="login-form">
                    
                    <div class="login-head">
                        <h3 class="title">Selamat Datang</h3>
                        <p>Masuk untuk mengakses dashboard admin.</p>
                    </div>

                    <h6 class="login-title"><span>Login</span></h6>

                    {{-- Form Laravel Auth --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror" required autofocus>
                            @error('email')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4 position-relative">
                            <label class="mb-1 form-label required">Password</label>
                            <input type="password" id="dz-password" name="password"
                                placeholder="Password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            <span class="show-pass eye">
                                <i class="fa fa-eye-slash"></i>
                                <i class="fa fa-eye"></i>
                            </span>
                            @error('password')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                            <div class="mb-4">
                                <div class="form-check custom-checkbox mb-3">
                                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Ingat saya</label>
                                </div>
                            </div>
                            <!-- <div class="mb-4">
                                <a href="{{ route('password.request') }}" class="btn-link text-primary">Lupa Password?</a>
                            </div> -->
                        </div>

                        <!-- Submit -->
                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>

                        <!-- Register link -->
                        <!-- @if (Route::has('register'))
                            <p class="text-center">Belum punya akun?  
                                <a class="btn-link text-primary" href="{{ route('register') }}">Daftar</a>
                            </p>
                        @endif -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/deznav-init.js') }}"></script>
</body>
</html>
