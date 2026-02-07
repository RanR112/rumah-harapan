@props(['user' => null, 'isEdit' => false])

<div class="form-container">
    <div class="form-columns">
        <!-- Kolom Kiri (Nama, Email, Nomor Telepon) -->
        <div class="form-left">
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required
                    class="form-input @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                    required class="form-input @error('email') is-invalid @enderror"
                    placeholder="Masukkan alamat email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                    class="form-input @error('phone') is-invalid @enderror"
                    placeholder="Masukkan nomor telepon (opsional)">
                @error('phone')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Kolom Kanan (Role, Password, Konfirmasi Password) -->
        <div class="form-right">
            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <input type="hidden" name="role" id="role" value="{{ old('role', $user?->role ?? '') }}">
                <div class="custom-dropdown" data-dropdown>
                    <button type="button" data-dropdown-trigger class="form-input dropdown-trigger"
                        aria-expanded="false">
                        <span class="dropdown-value">
                            {{ old('role', $user?->role ?? '') ? ucfirst(old('role', $user?->role)) : 'Pilih role' }}
                        </span>
                        <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="#718096" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div data-dropdown-menu class="dropdown-menu">
                        <div data-dropdown-option value=""
                            class="dropdown-option {{ old('role', $user?->role ?? '') == '' ? 'selected' : '' }}">
                            Pilih role
                        </div>
                        <div data-dropdown-option value="admin"
                            class="dropdown-option {{ old('role', $user?->role ?? '') == 'admin' ? 'selected' : '' }}">
                            Admin
                        </div>
                        <div data-dropdown-option value="petugas"
                            class="dropdown-option {{ old('role', $user?->role ?? '') == 'petugas' ? 'selected' : '' }}">
                            Petugas
                        </div>
                    </div>
                </div>
                @error('role')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            @if (!$isEdit || (old('password') || $errors->any()))
                <div class="form-group">
                    <label for="password"
                        class="form-label">{{ $isEdit ? 'Password Baru (Opsional)' : 'Password' }}</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" {{ $isEdit ? '' : 'required' }}
                            class="form-input password-input @error('password') is-invalid @enderror"
                            placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengubah' : 'Masukkan password' }}">
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
                </div>

                <div class="form-group">
                    <label for="password_confirmation"
                        class="form-label">{{ $isEdit ? 'Konfirmasi Password Baru' : 'Konfirmasi Password' }}</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            {{ $isEdit ? '' : 'required' }}
                            class="form-input password-input @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ $isEdit ? 'Konfirmasi password baru' : 'Konfirmasi password' }}">
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
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            'use strict';

            // Password Toggle
            function initPasswordToggle() {
                document.querySelectorAll('.toggle-password').forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const input = document.getElementById(targetId);
                        const icon = this.querySelector('.eye-icon');

                        if (!input || !icon) return;

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
            }

            // Custom Dropdown
            function initCustomDropdowns() {
                const dropdowns = document.querySelectorAll('[data-dropdown]');

                dropdowns.forEach(dropdown => {
                    if (dropdown.dataset.initialized === 'true') return;

                    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
                    const menu = dropdown.querySelector('[data-dropdown-menu]');
                    const options = dropdown.querySelectorAll('[data-dropdown-option]');
                    const valueDisplay = trigger.querySelector('.dropdown-value');
                    const hiddenInput = document.getElementById('role');
                    const chevron = trigger.querySelector('.dropdown-chevron');

                    if (!trigger || !menu || !hiddenInput) return;

                    const toggleDropdown = (show = null) => {
                        const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                        const shouldShow = show !== null ? show : !isExpanded;

                        trigger.setAttribute('aria-expanded', String(shouldShow));
                        menu.classList.toggle('show', shouldShow);

                        if (chevron) {
                            chevron.style.transform = shouldShow ? 'rotate(180deg)' : 'rotate(0deg)';
                            chevron.style.transition = 'transform 0.2s ease';
                        }
                    };

                    const handleOptionClick = (option) => {
                        const value = option.getAttribute('value');
                        const text = option.textContent.trim();

                        if (valueDisplay) valueDisplay.textContent = text;
                        if (hiddenInput) {
                            hiddenInput.value = value;
                            hiddenInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }

                        toggleDropdown(false);
                        options.forEach(opt => opt.classList.remove('selected'));
                        option.classList.add('selected');
                    };

                    options.forEach(option => {
                        option.addEventListener('click', (e) => {
                            e.stopPropagation();
                            handleOptionClick(option);
                        });
                    });

                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        toggleDropdown();
                    });

                    document.addEventListener('click', (e) => {
                        if (!dropdown.contains(e.target)) toggleDropdown(false);
                    });

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') toggleDropdown(false);
                    });

                    dropdown.dataset.initialized = 'true';
                });
            }

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    initPasswordToggle();
                    initCustomDropdowns();
                });
            } else {
                initPasswordToggle();
                initCustomDropdowns();
            }
        })();
    </script>
@endpush
