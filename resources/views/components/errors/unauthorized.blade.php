<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/sass/app.scss'])
</head>

<body class="error-page">
    <div class="error-container">
        <div class="error-logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="{{ config('app.name') }}">
        </div>

        <div class="error-code error-code--danger">403</div>

        <h1 class="error-title">Akses Ditolak</h1>
        <p class="error-message">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>

        <div class="error-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-error-primary">
                    Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-error-primary">
                    Kembali ke Login
                </a>
            @endauth
        </div>
    </div>
</body>

</html>
