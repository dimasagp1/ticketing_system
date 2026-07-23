<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'TOONWORLD Ticketing Portal')) }}</title>
    
    @if(\App\Helpers\SettingsHelper::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_favicon')) }}" type="image/x-icon">
    @endif
    
    <!-- Google Fonts: Fredoka & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --toon-yellow: #FFE600;
            --toon-blue: #0055FF;
            --toon-pink: #FF007A;
            --toon-orange: #FF6B00;
            --toon-green: #00E676;
            --toon-cream: #FFFBEA;
            --toon-black: #000000;
            --toon-white: #FFFFFF;
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--toon-cream);
            color: var(--toon-black);
        }

        /* Comic Dots Background */
        .bg-dots {
            background-image: radial-gradient(#000000 15%, transparent 16%), radial-gradient(#000000 15%, transparent 16%);
            background-size: 24px 24px;
            background-position: 0 0, 12px 12px;
            opacity: 0.06;
            position: fixed;
            inset: 0;
            pointer-events: none;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 2;
        }

        .login-card {
            background-color: var(--toon-white);
            border: 4px solid var(--toon-black);
            border-radius: 2rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 520px;
            box-shadow: 10px 10px 0px 0px var(--toon-black);
            position: relative;
            overflow: hidden;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--toon-yellow);
            border: 3px solid var(--toon-black);
            padding: 0.5rem 1.2rem;
            border-radius: 9999px;
            box-shadow: 4px 4px 0px 0px var(--toon-black);
            font-family: 'Fredoka', cursive;
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .form-title {
            font-family: 'Fredoka', cursive;
            font-size: 2.2rem;
            font-weight: 900;
            margin: 0 0 0.5rem 0;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: var(--toon-black);
        }

        .form-subtitle {
            font-size: 0.95rem;
            font-weight: 700;
            color: #4b5563;
            margin: 0 0 1.5rem 0;
        }

        .form-group {
            margin-bottom: 1.4rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-family: 'Fredoka', cursive;
            font-weight: 800;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 2.8rem;
            border: 3px solid var(--toon-black);
            border-radius: 1.2rem;
            background-color: var(--toon-cream);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--toon-black);
            box-shadow: 3px 3px 0px 0px var(--toon-black);
            transition: all 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            background-color: var(--toon-white);
            box-shadow: 5px 5px 0px 0px var(--toon-blue);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 2.7rem;
            color: var(--toon-black);
            font-size: 1.1rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.8rem;
            top: 2.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: var(--toon-black);
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .btn-toon-submit {
            width: 100%;
            background-color: var(--toon-blue);
            color: var(--toon-white);
            font-family: 'Fredoka', cursive;
            font-size: 1.25rem;
            font-weight: 900;
            padding: 0.9rem 1.5rem;
            border: 4px solid var(--toon-black);
            border-radius: 1.2rem;
            box-shadow: 6px 6px 0px 0px var(--toon-black);
            cursor: pointer;
            transition: all 0.15s ease;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-toon-submit:hover {
            background-color: var(--toon-pink);
            transform: translateY(-2px);
            box-shadow: 9px 9px 0px 0px var(--toon-black);
        }

        .btn-toon-submit:active {
            transform: translate(3px, 3px);
            box-shadow: 2px 2px 0px 0px var(--toon-black);
        }

        .alert-box {
            padding: 0.8rem 1rem;
            border: 3px solid var(--toon-black);
            border-radius: 1rem;
            margin-bottom: 1.2rem;
            font-weight: 800;
            font-size: 0.9rem;
            box-shadow: 3px 3px 0px 0px var(--toon-black);
        }

        .alert-success { background-color: var(--toon-green); color: var(--toon-black); }
        .alert-error { background-color: var(--toon-pink); color: var(--toon-white); }

        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--toon-blue);
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
            color: var(--toon-pink);
        }
    </style>
</head>
<body>

    <div class="bg-dots"></div>

    <div class="login-wrapper">
        <div class="login-card">
            
            <!-- Brand Badge -->
            <div class="brand-header">
                <a href="{{ route('home') }}" style="text-decoration: none; color: inherit;">
                    <div class="brand-logo-badge">
                        <span style="background: var(--toon-pink); color: white; border: 2px solid black; border-radius: 50%; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">T</span>
                        TOONWORLD PORTAL
                    </div>
                </a>
                <h1 class="form-title">MASUK SISTEM 🚀</h1>
                <p class="form-subtitle">Masukkan email dan kata sandi akun ticketing Anda</p>
            </div>

            <!-- Alerts -->
            @if (session('status') || session('success'))
                <div class="alert-box alert-success">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('status') ?? session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert-box alert-error">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">EMAIL ADDRESS</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@domain.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                    <i class="fas fa-envelope input-icon"></i>
                    @error('email')
                        <div style="color: var(--toon-pink); font-size: 0.85rem; font-weight: 800; margin-top: 0.4rem;">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">KATA SANDI</label>
                    <input id="login_password" type="password" name="password" class="form-control" placeholder="••••••••" required>
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle js-password-toggle" data-target="login_password" aria-label="Tampilkan kata sandi">
                        <i class="fas fa-eye"></i>
                    </button>
                    @error('password')
                        <div style="color: var(--toon-pink); font-size: 0.85rem; font-weight: 800; margin-top: 0.4rem;">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="checkbox-wrapper">
                    <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer;">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--toon-blue);">
                        <span>Ingat Sesi Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: var(--toon-blue); text-decoration: none;">Lupa Sandi?</a>
                    @endif
                </div>

                <button type="submit" class="btn-toon-submit">
                    MASUK PORTAL <i class="fas fa-arrow-right ml-1"></i>
                </button>
                
                <div class="footer-links">
                    <span style="color: #64748b;">Belum punya akun?</span> 
                    <a href="{{ route('register') }}">Daftar Sekarang</a>
                    <span style="margin: 0 0.5rem;">•</span>
                    <a href="{{ route('home') }}">Kembali ke Beranda</a>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggles = document.querySelectorAll('.js-password-toggle');
            toggles.forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = button.getAttribute('data-target');
                    var input = document.getElementById(targetId);
                    if (!input) return;

                    var icon = button.querySelector('i');
                    var reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';

                    if (icon) {
                        icon.classList.toggle('fa-eye', !reveal);
                        icon.classList.toggle('fa-eye-slash', reveal);
                    }
                });
            });
        });
    </script>
</body>
</html>
