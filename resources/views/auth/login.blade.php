<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | SMK YPE SAMPANG</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Vendor & Style -->
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        .login-main-page {
            min-height: 100vh;
            display: flex;
        }
        
        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        .login-aside-left {
            flex: 1;
            background: linear-gradient(135deg, #1EAAE7 0%, #148abe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .login-aside-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url({{ asset('images/rainbow.gif') }});
            background-size: cover;
            background-position: center;
            opacity: 0.2;
        }
        
        .login-description {
            position: relative;
            z-index: 1;
            max-width: 500px;
        }
        
        .login-description h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .login-description h1 span {
            color: #ffc107;
        }
        
        .login-description p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.95);
            line-height: 1.6;
        }
        
        .login-description .sub-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #ffc107;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }
        
        .login-aside-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: #f8f9fa;
        }
        
        .login-form-wrapper {
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .login-head h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .login-head p {
            color: #64748b;
            margin-bottom: 2rem;
        }
        
        .login-title {
            text-align: center;
            margin: 2rem 0 1.5rem;
            position: relative;
        }
        
        .login-title span {
            background: #fff;
            padding: 0 1rem;
            color: #1EAAE7;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .login-title::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #1EAAE7;
            box-shadow: 0 0 0 3px rgba(30, 170, 231, 0.1);
        }
        
        .btn-primary {
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            background: linear-gradient(135deg, #1EAAE7 0%, #148abe 100%);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(30, 170, 231, 0.4);
        }
        
        @media (max-width: 991.98px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .login-aside-left {
                min-height: 40vh;
                padding: 2rem;
            }
            
            .login-description h1 {
                font-size: 2rem;
            }
            
            .login-aside-right {
                padding: 2rem 1rem;
            }
            
            .login-form-wrapper {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-main-page">
        <div class="login-wrapper">
            
            <!-- Left Side -->
            <div class="login-aside-left">
                <div class="login-description">
                    <p class="sub-title">Sistem Informasi Akademik</p>
                    <h1>SMK YPE <span>Sampang</span></h1>
                    <p>Silakan login dengan akun Anda untuk melanjutkan ke dashboard dan mengelola data akademik dengan mudah.</p>
                </div>
            </div>

            <!-- Right Side -->
            <div class="login-aside-right">
                <div class="login-form-wrapper">
                    
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
