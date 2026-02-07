<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/sass/app.scss'])
</head>

<body class="reset-password-page">
    <div class="reset-password-container">
        <div class="reset-password__logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="Logo">
        </div>

        <div class="reset-password__header">
            <h1 class="reset-password__title">Buat Password Baru</h1>
            <p class="reset-password__subtitle">
                Password baru Anda harus berbeda dari password sebelumnya.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="reset-password__form" id="resetPasswordForm">
            @csrf

            <!-- Hidden email field (diperlukan untuk validasi) -->
            <input type="hidden" name="email" value="{{ $email ?? session('email_for_password_reset') }}">

            <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Masukkan password baru"
                        class="form-input @error('password') is-invalid @enderror"
                    >
                    <button type="button" class="toggle-password" data-target="password">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <div class="password-requirements">
                    <small>Minimal 8 karakter</small>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        placeholder="Masukkan ulang password baru" 
                        class="form-input @error('password_confirmation') is-invalid @enderror"
                    >
                    <button type="button" class="toggle-password" data-target="password_confirmation">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="btn-action">
                <button type="submit" class="btn-primary" id="resetPasswordButton">
                    <span class="btn-text">Atur Ulang Password</span>
                    <div class="btn-loader" style="display: none;">
                        @include('components.loader.loader-pulse')
                    </div>
                </button>
            </div>
        </form>
    </div>

    {{-- Toggle Password Script (Inline - Khusus Password) --}}
    <script>
        // Toggle password visibility
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
    </script>

    @vite(['resources/js/app.js'])
</body>

</html>