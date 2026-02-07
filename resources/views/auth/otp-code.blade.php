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
        <div class="check-email__icon">
            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Background Circle with Pulse -->
                <circle class="bg-circle" cx="50" cy="50" r="45" fill="#10b981" opacity="0.1" />

                <!-- Animated Circle Rings -->
                <circle class="pulse-ring pulse-ring-1" cx="50" cy="50" r="40" stroke="#10b981"
                    stroke-width="2" fill="none" opacity="0" />
                <circle class="pulse-ring pulse-ring-2" cx="50" cy="50" r="40" stroke="#059669"
                    stroke-width="2" fill="none" opacity="0" />

                <!-- Envelope Body -->
                <rect class="envelope-body" x="20" y="35" width="60" height="40" rx="4" stroke="#10b981"
                    stroke-width="3" fill="#fff" />

                <!-- Envelope Flap (will morph) -->
                <path class="envelope-flap" d="M 20 35 L 50 55 L 80 35" stroke="#059669" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round" fill="none" />

                <!-- Check Mark (will appear) -->
                <g class="checkmark" opacity="0">
                    <circle cx="50" cy="50" r="20" fill="#10b981" />
                    <path d="M 40 50 L 46 56 L 60 42" stroke="#fff" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" fill="none" />
                </g>
            </svg>
        </div>

        <h1 class="check-email__title">Verifikasi OTP</h1>
        <p class="check-email__subtitle">
            Kami telah mengirimkan kode verifikasi 6 digit ke
            <strong>{{ session('email_for_password_reset') }}</strong>.
        </p>

        <!-- Timer OTP -->
        <div class="otp-timer" id="otpTimer">
            <span id="timerText">Kirim ulang kode dalam: </span>
            <span id="countdown">2:00</span>
        </div>

        <!-- Form Input Kode OTP -->
        <form method="POST" action="{{ route('otp.verification') }}" class="forgot-password__form" id="otpForm">
            @csrf

            <input type="hidden" name="email" value="{{ session('email_for_password_reset') }}">

            <div class="form-group">
                <label for="otp-input" class="form-label">Kode OTP</label>

                <!-- Input OTP 6 Digit -->
                <div class="otp-inputs" id="otpInputs">
                    <input type="text" maxlength="1" data-index="0"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                    <input type="text" maxlength="1" data-index="1"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                    <input type="text" maxlength="1" data-index="2"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                    <input type="text" maxlength="1" data-index="3"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                    <input type="text" maxlength="1" data-index="4"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                    <input type="text" maxlength="1" data-index="5"
                        class="otp-digit @error('otp') is-invalid @enderror" pattern="[0-9]" inputmode="numeric"
                        required>
                </div>

                <!-- Hidden input untuk mengirim nilai gabungan -->
                <input type="hidden" id="otp_code" name="otp" required>

                @error('otp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="btn-action">
                <button type="submit" class="btn-primary" id="otpSubmitBtn">
                    <span class="btn-text">Verifikasi Kode</span>
                    <div class="btn-loader" style="display: none;">
                        @include('components.loader.loader-pulse')
                    </div>
                </button>
            </div>
        </form>

        <div class="forgot-password__back">
            Ingat password Sebelumnya?<a href="{{ route('login') }}" class="link-back"> Kembali ke Login</a>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
