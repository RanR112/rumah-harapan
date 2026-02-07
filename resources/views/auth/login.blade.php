<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/sass/app.scss'])
</head>

<body class="login-page">
    {{-- Alert Error (Slide-in dari atas) --}}
    @if ($errors->any())
        <div class="alert-container" id="alertContainer">
            <div class="alert alert-error">
                <div class="alert-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <h4 class="alert-title">Login Gagal</h4>
                    <p class="alert-message">
                        @if ($errors->has('email'))
                            {{ $errors->first('email') }}
                        @elseif ($errors->has('username'))
                            {{ $errors->first('username') }}
                        @elseif ($errors->has('password'))
                            {{ $errors->first('password') }}
                        @else
                            {{ $errors->first() }}
                        @endif
                    </p>
                </div>
                <button type="button" class="alert-close" onclick="closeAlert()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <div class="login-page__container">
        <div class="login-page__logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="Rumah Harapan Logo">
        </div>

        <div class="login-page__header">
            <h1 class="login-page__title">Selamat Datang</h1>
            <p class="login-page__subtitle-main">Di Manajemen Data Anak Asuh Rumah Harapan</p>
            <p class="login-page__subtitle">Silakan masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <form class="login-page__form" method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="Masukkan email Anda" class="form-input">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Masukkan password Anda"
                        class="form-input">
                    <button type="button" class="toggle-password" data-target="password">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            @if (Route::has('password.request'))
                <div class="form-footer">
                    <a href="{{ route('password.request') }}" class="link-forgot">Lupa Password?</a>
                </div>
            @endif

            <div class="btn-action">
                <button type="submit" class="btn-primary" id="loginButton">
                    <span class="btn-text">Masuk</span>
                    <div class="btn-loader" style="display: none;">
                        @include('components.loader.loader-pulse')
                    </div>
                </button>
            </div>
        </form>
    </div>

    {{-- Toggle Password Script (Inline - Khusus Password) --}}
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('.eye-icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML =
                        '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    input.type = 'password';
                    icon.innerHTML =
                        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            });
        });

        // Auto hide alert setelah 5 detik
        @if ($errors->any())
            setTimeout(() => {
                closeAlert();
            }, 5000);
        @endif

        function closeAlert() {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) {
                alertContainer.classList.add('alert-slide-out');
                setTimeout(() => {
                    alertContainer.remove();
                }, 300);
            }
        }

        window.userData = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>

    {{-- Pemanggilan JS Reusable --}}
    @vite(['resources/js/app.js'])
    @vite(['resources/js/pages/users/index.js'])
</body>

</html>
