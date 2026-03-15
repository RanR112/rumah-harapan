@props(['rumahHarapan' => null, 'isEdit' => false, 'isShow' => false])

@php
    // Saat mode show: semua input readonly, required & placeholder dihilangkan
    $readonlyAttr = $isShow ? 'readonly' : '';
    $disabledAttr = $isShow ? 'disabled' : '';
    $requiredAttr = $isShow ? '' : 'required';
    $showRequired = !$isShow;
@endphp

<div class="form-container">
    <div class="form-columns">
        <!-- Kolom Kiri -->
        <div class="form-left">
            <div class="form-group">
                <label for="kode" class="form-label">
                    Kode Asrama
                    @if ($showRequired)
                        <span class="required">*</span>
                    @endif
                </label>
                <input type="text" id="kode" name="kode" value="{{ old('kode', $rumahHarapan?->kode) }}"
                    {{ $requiredAttr }} {{ $readonlyAttr }}
                    class="form-input @error('kode') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan kode asrama" @endif>
                @error('kode')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Asrama
                    @if ($showRequired)
                        <span class="required">*</span>
                    @endif
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $rumahHarapan?->nama) }}"
                    {{ $requiredAttr }} {{ $readonlyAttr }}
                    class="form-input @error('nama') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan nama asrama" @endif>
                @error('nama')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">
                    Alamat
                    @if ($showRequired)
                        <span class="required">*</span>
                    @endif
                </label>
                <textarea id="alamat" name="alamat" rows="3" {{ $requiredAttr }} {{ $readonlyAttr }}
                    class="form-input @error('alamat') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan alamat lengkap" @endif>{{ old('alamat', $rumahHarapan?->alamat) }}</textarea>
                @error('alamat')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kota" class="form-label">
                    Kota
                    @if ($showRequired)
                        <span class="required">*</span>
                    @endif
                </label>
                <input type="text" id="kota" name="kota" value="{{ old('kota', $rumahHarapan?->kota) }}"
                    {{ $requiredAttr }} {{ $readonlyAttr }}
                    class="form-input @error('kota') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan kota" @endif>
                @error('kota')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="provinsi" class="form-label">
                    Provinsi
                    @if ($showRequired)
                        <span class="required">*</span>
                    @endif
                </label>
                <input type="text" id="provinsi" name="provinsi"
                    value="{{ old('provinsi', $rumahHarapan?->provinsi) }}" {{ $requiredAttr }} {{ $readonlyAttr }}
                    class="form-input @error('provinsi') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan provinsi" @endif>
                @error('provinsi')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="form-right">
            <div class="form-group">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="text" id="telepon" name="telepon"
                    value="{{ old('telepon', $rumahHarapan?->telepon) }}" {{ $readonlyAttr }}
                    class="form-input @error('telepon') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan nomor telepon" @endif>
                @error('telepon')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" autocomplete="on"
                    value="{{ old('email', $rumahHarapan?->email) }}" {{ $readonlyAttr }}
                    class="form-input @error('email') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan alamat email" @endif>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="koordinator" class="form-label">Koordinator</label>
                <input type="text" id="koordinator" name="koordinator"
                    value="{{ old('koordinator', $rumahHarapan?->koordinator) }}" {{ $readonlyAttr }}
                    class="form-input @error('koordinator') is-invalid @enderror @if ($isShow) form-input--readonly @endif"
                    @if (!$isShow) placeholder="Masukkan nama koordinator" @endif>
                @error('koordinator')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Status: dropdown interaktif saat edit, teks badge saat show, disembunyikan saat create --}}
            @if ($isEdit)
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <input type="hidden" name="is_active" id="is_active"
                        value="{{ old('is_active', $rumahHarapan?->is_active ? '1' : '0') }}">
                    <div class="custom-dropdown" data-dropdown>
                        <button type="button" data-dropdown-trigger class="form-input dropdown-trigger"
                            aria-expanded="false">
                            <span class="dropdown-value">
                                @php
                                    $selectedStatus = old('is_active', $rumahHarapan?->is_active ?? true);
                                    echo $selectedStatus ? 'Aktif' : 'Non-Aktif';
                                @endphp
                            </span>
                            <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div data-dropdown-menu class="dropdown-menu">
                            <div data-dropdown-option data-value="1"
                                class="dropdown-option {{ old('is_active', $rumahHarapan?->is_active ?? true) ? 'selected' : '' }}">
                                Aktif
                            </div>
                            <div data-dropdown-option data-value="0"
                                class="dropdown-option {{ !old('is_active', $rumahHarapan?->is_active ?? true) ? 'selected' : '' }}">
                                Non-Aktif
                            </div>
                        </div>
                    </div>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            @elseif ($isShow)
                {{-- Mode show: tampilkan status sebagai badge read-only --}}
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="form-status-display">
                        <span
                            class="status-badge {{ $rumahHarapan?->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $rumahHarapan?->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
