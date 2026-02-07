<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/sass/app.scss'])
</head>

<body class="reset-password-success-page">
    <div class="reset-password-success-container">
        <div class="success-icon">
            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Background Circle with Pulse -->
                <circle class="bg-circle" cx="50" cy="50" r="45" fill="#10b981" opacity="0.1" />

                <!-- Animated Circle Rings -->
                <circle class="pulse-ring pulse-ring-1" cx="50" cy="50" r="40" stroke="#10b981"
                    stroke-width="2" fill="none" opacity="0" />
                <circle class="pulse-ring pulse-ring-2" cx="50" cy="50" r="40" stroke="#059669"
                    stroke-width="2" fill="none" opacity="0" />

                <!-- Check Circle -->
                <g class="checkmark-group">
                    <circle cx="50" cy="50" r="25" stroke="#10b981" stroke-width="3" fill="none" />
                    <path class="checkmark-path" d="M 38 50 L 46 58 L 62 42" stroke="#10b981" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round" fill="none" />
                </g>
            </svg>
        </div>

        <h1 class="success__title">Password Berhasil Diubah!</h1>
        <p class="success__subtitle">
            Password Anda telah berhasil diperbarui. Silakan login dengan password baru Anda.
        </p>

        <div class="btn-action">
            <a href="{{ route('login') }}" class="btn-primary" role="button">
                <span class="btn-text">Login Sekarang</span>
                <div class="btn-loader" style="display: none;">
                    @include('components.loader.loader-pulse')
                </div>
            </a>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>