<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/sass/app.scss'])
</head>
<body class="forgot-password-page">
    <div class="forgot-password-container">
        <div class="forgot-password__logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="Logo">
        </div>
        
        <div class="forgot-password__header">
            <h1 class="forgot-password__title">Lupa Password</h1>
            <p class="forgot-password__subtitle">
                Masukkan alamat email Anda. Kami akan mengirimkan kode OTP untuk mengatur ulang password Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="forgot-password__form" id="forgotPasswordForm">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="Masukkan alamat email Anda"
                    class="form-input @error('email') is-invalid @enderror"
                >
                @error('email')
                    <span class="form-error" id="emailError">{{ $message }}</span>
                @enderror
            </div>

            <div class="btn-action">
                <button type="submit" class="btn-primary" id="forgotPasswordButton">
                    <span class="btn-text">Kirim OTP Reset</span>
                    <div class="btn-loader" style="display: none;">
                        @include('components.loader.loader-pulse')
                    </div>
                </button>
            </div>

            <div class="forgot-password__back">
                <a href="{{ route('login') }}" class="link-back">Kembali ke Login</a>
            </div>
        </form>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>