@props(['user' => null, 'isEdit' => false])

<div class="form-container">
    <div class="form-columns">
        <!-- Kolom Kiri (Nama, Email, Nomor Telepon) -->
        <div class="form-left">
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" autocomplete="on"
                    value="{{ old('name', $user->name ?? '') }}" required
                    class="form-input @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" autocomplete="on"
                    value="{{ old('email', $user->email ?? '') }}" required
                    class="form-input @error('email') is-invalid @enderror" placeholder="Masukkan alamat email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" autocomplete="on"
                    value="{{ old('phone', $user->phone ?? '') }}"
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
                <label class="form-label">Role</label>
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
