<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/sass/app.scss'])
</head>

<body class="dashboard-layout">
    <!-- Gunakan komponen loader-transition -->
    @include('components.loader-transition')

    <!-- Konten dashboard tetap ada dan terlihat -->
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

    <!-- Overlay untuk mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('components.alert-modal')

    @vite(['resources/js/app.js'])
    @stack('scripts')

    <!-- Script untuk handle initial loading -->
    <script>
        // Sembunyikan loader setelah delay yang sesuai
        function hideInitialLoader() {
            const loader = document.getElementById('dashboardTransitionLoader');
            if (loader) {
                // Delay 1.5 detik sebelum sembunyikan
                setTimeout(() => {
                    loader.classList.remove('dashboard-transition-loader--active');
                    setTimeout(() => {
                        loader.remove();
                    }, 200);
                }, 1500);
            }
        }

        // Jalankan secepat mungkin
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hideInitialLoader);
        } else {
            hideInitialLoader();
        }
    </script>
</body>

</html>
