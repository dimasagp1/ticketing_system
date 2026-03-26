<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}</title>

    @if(\App\Helpers\SettingsHelper::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('app_favicon')) }}" type="image/x-icon">
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #020617;
            --bg-accent: #0f172a;
            --neon-blue: #3b82f6;
            --neon-cyan: #06b6d4;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-bg: rgba(30, 41, 59, 0.4);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            overflow-x: hidden;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .register-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        .brand-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
        }

        .brand-content {
            max-width: 620px;
            margin: 0 auto;
        }

        .brand-logo-wrap {
            margin-bottom: 1.25rem;
        }

        .brand-logo-image {
            max-height: 72px;
            width: auto;
            max-width: 260px;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(2, 6, 23, 0.35));
        }

        .sys-badge {
            font-family: 'Fira Code', monospace;
            color: var(--neon-cyan);
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border: 1px solid var(--neon-cyan);
            border-radius: 4px;
            background: rgba(6, 182, 212, 0.1);
            animation: pulse-border 2s infinite;
        }

        .brand-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.2rem;
            background: linear-gradient(to right, #fff, var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        .brand-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.8rem;
            font-weight: 300;
        }

        .typewriter-text {
            font-family: 'Fira Code', monospace;
            color: var(--neon-blue);
            font-size: 0.9rem;
            border-right: 2px solid var(--neon-blue);
            white-space: nowrap;
            overflow: hidden;
            animation: typing 3.5s steps(40, end), blink-caret .75s step-end infinite;
        }

        .form-section {
            width: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: rgba(2, 6, 23, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
        }

        .glass-card {
            width: 100%;
            max-width: 420px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), transparent 55%);
            pointer-events: none;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .form-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.8rem;
        }

        .mobile-kicker {
            display: none;
            font-family: 'Fira Code', monospace;
            font-size: 0.72rem;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #bfdbfe;
            margin-bottom: 0.6rem;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
            outline: none;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--neon-blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-group.is-password .form-control {
            padding-right: 3rem;
        }

        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 0.2rem;
        }

        .password-toggle:hover {
            color: #fff;
        }

        .btn-register {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(to right, var(--neon-blue), #2563eb);
            color: #fff;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
            margin-top: 0.4rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .muted-text {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .auth-link {
            color: var(--neon-cyan);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link:hover {
            color: #fff;
            text-shadow: 0 0 8px var(--neon-cyan);
        }

        .alert {
            padding: 0.9rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            border: 1px solid transparent;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        .field-error {
            color: #fca5a5;
            font-size: 0.8rem;
            margin-top: 0.45rem;
        }

        @keyframes typing { from { width: 0 } to { width: 100% } }
        @keyframes blink-caret { from, to { border-color: transparent } 50% { border-color: var(--neon-blue); } }
        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(6, 182, 212, 0); }
            100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0); }
        }

        @media (max-width: 900px) {
            .register-wrapper {
                flex-direction: column;
                min-height: 100svh;
            }

            body {
                background:
                    radial-gradient(circle at 15% 10%, rgba(14, 165, 233, 0.2), transparent 38%),
                    radial-gradient(circle at 85% 85%, rgba(59, 130, 246, 0.22), transparent 42%),
                    var(--bg-dark);
            }

            .form-section {
                width: 100%;
                border-left: none;
                border-top: 1px solid var(--glass-border);
                order: -1;
                padding: 1.25rem 1rem;
                background: transparent;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

            .brand-section {
                padding: 1.5rem 1rem 2rem;
                justify-content: flex-start;
            }

            .brand-content {
                background: rgba(15, 23, 42, 0.55);
                border: 1px solid rgba(148, 163, 184, 0.24);
                border-radius: 1rem;
                padding: 1rem;
            }

            .brand-title {
                font-size: 2.4rem;
            }

            .glass-card {
                max-width: 560px;
                padding: 1.5rem;
                border-radius: 1rem;
            }

            .btn-register {
                min-height: 48px;
            }
        }

        @media (max-width: 640px) {
            .brand-content {
                max-width: 100%;
            }

            .mobile-kicker {
                display: inline-block;
            }

            .brand-logo-image {
                max-height: 56px;
                max-width: 220px;
            }

            .sys-badge {
                font-size: 0.72rem;
                letter-spacing: 1.2px;
                margin-bottom: 0.9rem;
            }

            .brand-title {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }

            .brand-desc {
                font-size: 0.92rem;
                line-height: 1.45;
                margin-bottom: 0.9rem;
            }

            .typewriter-text {
                font-size: 0.76rem;
            }

            .form-title {
                font-size: 1.4rem;
                margin-bottom: 0.35rem;
            }

            .form-subtitle {
                font-size: 0.82rem;
                margin-bottom: 1.25rem;
            }

            .glass-card {
                box-shadow: 0 22px 40px -26px rgba(2, 6, 23, 0.95), 0 0 0 1px rgba(148, 163, 184, 0.2);
            }

            .form-group {
                margin-bottom: 0.9rem;
            }

            .form-control {
                font-size: 0.95rem;
                min-height: 48px;
                padding: 0.85rem 0.9rem 0.85rem 2.65rem;
            }

            .input-icon {
                left: 1rem;
            }

            .password-toggle {
                right: 0.7rem;
                font-size: 1rem;
                min-width: 32px;
                min-height: 32px;
            }

            .mt-4 {
                margin-top: 0.85rem;
            }

            #particles-js {
                display: none;
            }
        }

        @media (max-width: 420px) {
            .form-section {
                padding: 0.9rem 0.65rem;
            }

            .glass-card {
                padding: 1.1rem;
            }

            .brand-section {
                padding: 1.1rem 0.85rem 1.5rem;
            }

            .brand-desc {
                display: none;
            }

            .typewriter-text {
                display: none;
            }

            .brand-content {
                padding: 0.75rem 0.8rem;
                border-radius: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <div class="register-wrapper">
        <div class="brand-section">
            <div class="brand-content">
                @if(\App\Helpers\SettingsHelper::get('app_logo'))
                    <div class="brand-logo-wrap">
                        <img
                            src="{{ asset('storage/' . ltrim(\App\Helpers\SettingsHelper::get('app_logo'), '/')) }}"
                            alt="{{ \App\Helpers\SettingsHelper::get('app_name', config('app.name', 'Antrian Project')) }}"
                            class="brand-logo-image"
                        >
                    </div>
                @endif
                <div class="sys-badge"><i class="fas fa-user-shield"></i> Access Provisioning</div>
                <h1 class="brand-title">
                    @if(\App\Helpers\SettingsHelper::get('app_name'))
                        {{ \App\Helpers\SettingsHelper::get('app_name') }}
                    @else
                        Developer & IT<br>Ticketing Portal
                    @endif
                </h1>
                <p class="brand-desc">
                    Daftarkan akun client baru untuk mulai membuat tiket, memantau progres, dan berkolaborasi langsung dengan tim developer.
                </p>
                <div style="display: inline-block;">
                    <div class="typewriter-text">> provisioning_new_client_profile...</div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="glass-card">
                <div class="mobile-kicker">Client Onboarding</div>
                <h2 class="form-title">Create Account</h2>
                <p class="form-subtitle">Isi data di bawah untuk membuat akun client</p>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        Mohon periksa kembali data yang diinput.
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <input id="name" type="text" name="name" class="form-control" placeholder="Nama lengkap" value="{{ old('name') }}" required autofocus autocomplete="name">
                        <i class="fas fa-user input-icon"></i>
                        @error('name')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input id="email" type="email" name="email" class="form-control" placeholder="email@domain.com" value="{{ old('email') }}" required autocomplete="username">
                        <i class="fas fa-envelope input-icon"></i>
                        @error('email')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group is-password">
                        <input id="password" type="password" name="password" class="form-control" placeholder="Kata sandi" required autocomplete="new-password">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle js-password-toggle" data-target="password" aria-label="Tampilkan kata sandi">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group is-password">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi kata sandi" required autocomplete="new-password">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <button type="button" class="password-toggle js-password-toggle" data-target="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password_confirmation')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-register">
                        Register Now <i class="fas fa-arrow-right"></i>
                    </button>

                    <div class="text-center mt-4">
                        <span class="muted-text">Sudah punya akun?</span>
                        <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: '#3b82f6' },
                    shape: { type: 'circle' },
                    opacity: {
                        value: 0.5,
                        random: true,
                        anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false }
                    },
                    size: { value: 3, random: true, anim: { enable: false } },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#3b82f6',
                        opacity: 0.2,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: 'none',
                        random: false,
                        straight: false,
                        out_mode: 'out',
                        bounce: false,
                        attract: { enable: false, rotateX: 600, rotateY: 1200 }
                    }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: {
                        onhover: { enable: true, mode: 'grab' },
                        onclick: { enable: true, mode: 'push' },
                        resize: true
                    },
                    modes: {
                        grab: { distance: 140, line_linked: { opacity: 1 } },
                        push: { particles_nb: 4 }
                    }
                },
                retina_detect: true
            });

            var toggles = document.querySelectorAll('.js-password-toggle');
            toggles.forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = button.getAttribute('data-target');
                    var input = document.getElementById(targetId);
                    if (!input) {
                        return;
                    }

                    var icon = button.querySelector('i');
                    var reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';

                    if (icon) {
                        icon.classList.toggle('fa-eye', !reveal);
                        icon.classList.toggle('fa-eye-slash', reveal);
                    }

                    button.setAttribute('aria-label', reveal ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                });
            });
        });
    </script>
</body>
</html>
