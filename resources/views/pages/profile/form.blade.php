@props(['user'])

<div class="form-container">
    <div class="form-columns">
        <!-- Kolom Kiri (Data Pribadi) -->
        <div class="form-left">
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    class="form-input @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="form-input @error('email') is-invalid @enderror" placeholder="Masukkan alamat email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="form-input @error('phone') is-invalid @enderror" placeholder="Masukkan nomor telepon (opsional)">
                @error('phone')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Role (Readonly) -->
            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <input type="text" id="role" value="{{ ucfirst($user->role) }}" 
                    class="form-input" readonly disabled>
                <p class="form-hint">Role tidak dapat diubah. Hubungi admin untuk mengubah role.</p>
            </div>
        </div>

        <!-- Kolom Kanan (Password) -->
        <div class="form-right">
            <div class="form-group">
                <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                <div class="password-input-wrapper">
                    <input type="password" id="current_password" name="current_password"
                        class="form-input password-input @error('current_password') is-invalid @enderror"
                        placeholder="Masukkan kata sandi saat ini (kosongkan jika tidak mengubah password)">
                    <button type="button" class="toggle-password" data-target="current_password">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <p class="form-hint">Kosongkan jika tidak ingin mengubah password</p>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi Baru</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password"
                        class="form-input password-input @error('password') is-invalid @enderror"
                        placeholder="Masukkan kata sandi baru (minimal 8 karakter)">
                    <button type="button" class="toggle-password" data-target="password">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                <p class="form-hint">Minimal 8 karakter</p>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-input password-input @error('password_confirmation') is-invalid @enderror"
                        placeholder="Konfirmasi kata sandi baru">
                    <button type="button" class="toggle-password" data-target="password_confirmation">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        'use strict';

        // Password Toggle untuk semua field password
        function initPasswordToggle() {
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('.eye-icon');

                    if (!input || !icon) return;

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        input.type = 'password';
                        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                });
            });
        }

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPasswordToggle);
        } else {
            initPasswordToggle();
        }
    })();
</script>
@endpush

<style>
    .form-hint {
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
        font-style: italic;
    }
    
    /* Role readonly styling */
    input[readonly] {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.8;
    }
</style>