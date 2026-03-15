{{--
    Komponen modal peringatan sesi tidak aktif.
    Di-include di layouts/dashboard.blade.php.
    Dikontrol oleh resources/js/components/session-timeout.js
--}}
<div id="sessionTimeoutModal" class="session-timeout-modal" aria-hidden="true" role="dialog" aria-modal="true"
    aria-labelledby="sessionTimeoutTitle">
    <div class="session-timeout-modal__backdrop"></div>

    <div class="session-timeout-modal__box">

        {{-- Icon --}}
        <div class="session-timeout-modal__icon">
            <i data-lucide="clock-alert"></i>
        </div>

        {{-- Judul --}}
        <h2 class="session-timeout-modal__title" id="sessionTimeoutTitle">
            Sesi Akan Berakhir
        </h2>

        {{-- Pesan utama --}}
        <p class="session-timeout-modal__message">
            Anda tidak aktif selama 20 menit. Sesi akan otomatis berakhir dalam:
        </p>

        {{-- Countdown --}}
        <div class="session-timeout-modal__countdown">
            <span id="sessionCountdown">02:00</span>
        </div>

        {{-- Pesan koneksi terputus — tersembunyi by default --}}
        <div class="session-timeout-modal__network" id="sessionNetworkMsg" aria-live="polite">
            <div class="session-timeout-modal__network-icon">
                <i data-lucide="wifi-off"></i>
            </div>
            <p class="session-timeout-modal__network-text">
                Koneksi jaringan terputus. Periksa koneksi Anda lalu klik <strong>Coba Lagi</strong>.
                Sesi tetap berjalan selama ada koneksi.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="session-timeout-modal__actions">

            {{-- Tombol Keluar --}}
            <button type="button" id="sessionLogoutBtn"
                class="session-timeout-modal__btn session-timeout-modal__btn--secondary">
                <span class="btn-text">Keluar</span>
                <div class="btn-loader" style="display: none;">
                    @include('components.loader.loader-pulse')
                </div>
            </button>
            
            {{-- Tombol Tetap Masuk --}}
            <button type="button" id="sessionStayBtn"
                class="session-timeout-modal__btn session-timeout-modal__btn--primary">
                <span class="btn-text">Tetap Masuk</span>
                <div class="btn-loader" style="display: none;">
                    @include('components.loader.loader-pulse')
                </div>
            </button>


        </div>

    </div>
</div>
