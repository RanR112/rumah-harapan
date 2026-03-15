<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ auth()->user()->theme ?? 'light' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Sync tema ke localStorage agar halaman auth bisa membaca saat logout --}}
    <script>
        localStorage.setItem('theme', '{{ auth()->user()->theme ?? 'light' }}');
    </script>

    @vite(['resources/sass/app.scss'])
</head>

<body class="dashboard-layout">
    @include('components.loader-transition')

    <div class="dashboard-container">
        @include('components.sidebar')

        <div class="main-content">
            @include('components.topbar')
            <main class="page-content">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('components.alert-modal')
    @include('components.session-timeout-modal')

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>

</html>
