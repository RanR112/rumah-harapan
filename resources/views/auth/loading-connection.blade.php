<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Baca tema dari localStorage agar konsisten dengan preferensi user --}}
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    @vite(['resources/sass/app.scss'])
</head>

<body class="loading-connection">
    <div class="loading-connection__container">
        <div class="loading-connection__logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="{{ config('app.name') }}">
        </div>

        @include('components.loader.loader-main')

        <p class="loading-connection__text">Mengecek koneksi...</p>
    </div>

    <script>
        // Konfigurasi delay (dalam milidetik)
        const CONNECTION_CHECK_DELAY = 2000; // 2 detik untuk pengecekan koneksi
        const MINIMUM_LOADING_TIME = 3000; // 3 detik minimum waktu loading
        const CONNECTION_TIMEOUT = 5000; // 5 detik timeout koneksi

        function checkConnection() {
            return new Promise((resolve, reject) => {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => {
                    controller.abort();
                    reject(new Error('Connection timeout'));
                }, CONNECTION_TIMEOUT);

                fetch('/health', {
                        method: 'HEAD',
                        signal: controller.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        if (response.ok) {
                            resolve();
                        } else {
                            reject(new Error('Network response was not ok'));
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        reject(error);
                    });
            });
        }

        async function handleLoading() {
            const startTime = Date.now();

            try {
                // Tampilkan "Mengecek koneksi..." selama 2 detik
                await new Promise(resolve => setTimeout(resolve, CONNECTION_CHECK_DELAY));

                // Cek koneksi
                await checkConnection();

                // Hitung sisa waktu untuk memenuhi minimum loading time
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, MINIMUM_LOADING_TIME - elapsed);

                // Tampilkan "Memuat aplikasi..." jika masih ada waktu tersisa
                if (remainingTime > 0) {
                    document.querySelector('.loading-connection__text').textContent = 'Memuat aplikasi...';
                    await new Promise(resolve => setTimeout(resolve, remainingTime));
                }

                // Redirect ke login
                window.location.href = '{{ route('login') }}';

            } catch (error) {
                console.error('Connection check failed:', error);
                // Tampilkan error setelah delay tambahan
                setTimeout(() => {
                    document.querySelector('.loading-connection__container').innerHTML = `
                        <div class="loading-connection__logo">
                            <img src="{{ asset('images/Logo.svg') }}" alt="{{ config('app.name') }}">
                        </div>
                        <div style="color: #ef4444; font-size: 64px;">
                            <i data-lucide="wifi-off"></i>
                        </div>
                        <p class="loading-connection__text" style="color: #dc2626; font-weight: 600;">Tidak ada koneksi internet</p>
                        <button onclick="location.reload()" style="background: linear-gradient(135deg, #a8c550 0%, #8da434 100%); color: white; border: none; border-radius: 8px; padding: 12px 24px; font-weight: 600; cursor: pointer; margin-top: 16px;">
                            Coba Lagi
                        </button>
                    `;
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                }, 1000); // Tampilkan error setelah 1 detik tambahan
            }
        }

        // Jalankan proses loading saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            handleLoading();
        });
    </script>

    @vite(['resources/js/app.js'])
</body>

</html>
