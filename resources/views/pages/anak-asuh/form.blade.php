@props(['anakAsuh' => null, 'asramas', 'isEdit' => false, 'isReadOnly' => false, 'canManageBerkas' => false])

<div class="anak-asuh-form-container">
    <!-- Foto Preview Section -->
    <div class="anak-asuh-form-group anak-asuh-foto-preview-group">
        <label class="anak-asuh-form-label">Foto Anak Asuh</label>
        <div class="anak-asuh-foto-wrapper">
            <div class="anak-asuh-foto-preview-container">
                @php
                    $fotoUrl =
                        $anakAsuh?->foto_url ?? asset('images/default-anak-asuh-' . old('jenis_kel', 'L') . '.png');
                @endphp
                <img id="fotoPreview" src="{{ $fotoUrl }}" alt="Foto Preview" class="anak-asuh-foto-preview">
                @if (!$isReadOnly)
                    <div class="anak-asuh-foto-overlay">
                        <i data-lucide="camera" class="anak-asuh-foto-icon"></i>
                    </div>
                @endif
            </div>
            @if (!$isReadOnly)
                <button type="button" id="deleteFotoBtn" class="anak-asuh-foto-delete-btn" style="display: none;">
                    <i data-lucide="trash-2" class="anak-asuh-foto-delete-icon"></i>
                </button>
            @endif
        </div>
        @if (!$isReadOnly)
            <input type="file" id="foto_upload" name="foto" accept="image/*" class="anak-asuh-foto-upload">
            <p class="anak-asuh-form-hint">Format: JPG, JPEG, PNG (max 2MB)</p>
        @endif

        @if ($anakAsuh?->foto_path)
            <input type="hidden" name="existing_foto_path" value="{{ $anakAsuh->foto_path }}">
        @endif
        {{-- Signal eksplisit ke server bahwa user ingin hapus foto --}}
        <input type="hidden" name="delete_foto" id="deleteFotoSignal" value="0">
    </div>

    <div class="anak-asuh-form-columns">
        <!-- Kolom Kiri -->
        <div class="anak-asuh-form-left">
            <div class="anak-asuh-form-group">
                <label for="nama_anak" class="anak-asuh-form-label">
                    Nama Lengkap @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="text" id="nama_anak" name="nama_anak"
                    value="{{ old('nama_anak', $anakAsuh?->nama_anak) }}"
                    {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                    class="anak-asuh-form-input @error('nama_anak') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan nama lengkap">
                @error('nama_anak')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="nik" class="anak-asuh-form-label">
                    NIK @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="text" id="nik" name="nik" value="{{ old('nik', $anakAsuh?->nik) }}"
                    {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                    class="anak-asuh-form-input @error('nik') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan NIK">
                @error('nik')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="no_kartu_keluarga" class="anak-asuh-form-label">
                    No. Kartu Keluarga @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="text" id="no_kartu_keluarga" name="no_kartu_keluarga"
                    value="{{ old('no_kartu_keluarga', $anakAsuh?->no_kartu_keluarga) }}"
                    {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                    class="anak-asuh-form-input @error('no_kartu_keluarga') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan no. kartu keluarga">
                @error('no_kartu_keluarga')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="alamat_lengkap" class="anak-asuh-form-label">Alamat Lengkap</label>
                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('alamat_lengkap') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan alamat lengkap">{{ old('alamat_lengkap', $anakAsuh?->alamat_lengkap) }}</textarea>
                @error('alamat_lengkap')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="tempat_lahir" class="anak-asuh-form-label">Tempat Lahir</label>
                <input type="text" id="tempat_lahir" name="tempat_lahir"
                    value="{{ old('tempat_lahir', $anakAsuh?->tempat_lahir) }}"
                    {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('tempat_lahir') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan tempat lahir">
                @error('tempat_lahir')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Tanggal Lahir @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <div class="anak-asuh-datepicker-wrapper {{ $isReadOnly ? 'disabled' : '' }}">
                    <input type="text" id="tanggal_lahir" name="tanggal_lahir"
                        value="{{ old('tanggal_lahir', $anakAsuh?->tanggal_lahir) }}"
                        {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                        class="anak-asuh-form-input @error('tanggal_lahir') anak-asuh-is-invalid @enderror"
                        placeholder="Pilih tanggal lahir">
                    <span class="anak-asuh-datepicker-icon">
                        <i data-lucide="calendar" class="anak-asuh-datepicker-lucide-icon"></i>
                    </span>
                </div>
                @error('tanggal_lahir')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="anak-asuh-form-right">
            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Jenis Kelamin @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="hidden" name="jenis_kel" id="jenis_kel"
                    value="{{ old('jenis_kel', $anakAsuh?->jenis_kel) }}">
                <div class="anak-asuh-custom-dropdown {{ $isReadOnly ? 'disabled' : '' }}" data-dropdown>
                    <button type="button" data-dropdown-trigger {{ $isReadOnly ? 'disabled' : '' }}
                        class="anak-asuh-form-input anak-asuh-dropdown-trigger" aria-expanded="false">
                        <span class="anak-asuh-dropdown-value">
                            @php
                                $selectedJenisKel = old('jenis_kel', $anakAsuh?->jenis_kel);
                                echo $selectedJenisKel
                                    ? ($selectedJenisKel === 'L'
                                        ? 'Laki-laki'
                                        : 'Perempuan')
                                    : 'Pilih jenis kelamin';
                            @endphp
                        </span>
                        <svg class="anak-asuh-dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    @if (!$isReadOnly)
                        <div data-dropdown-menu class="anak-asuh-dropdown-menu">
                            <div data-dropdown-option value=""
                                class="anak-asuh-dropdown-option {{ old('jenis_kel', $anakAsuh?->jenis_kel ?? '') == '' ? 'selected' : '' }}">
                                Pilih jenis kelamin
                            </div>
                            <div data-dropdown-option value="L"
                                class="anak-asuh-dropdown-option {{ old('jenis_kel', $anakAsuh?->jenis_kel ?? '') == 'L' ? 'selected' : '' }}">
                                Laki-laki
                            </div>
                            <div data-dropdown-option value="P"
                                class="anak-asuh-dropdown-option {{ old('jenis_kel', $anakAsuh?->jenis_kel ?? '') == 'P' ? 'selected' : '' }}">
                                Perempuan
                            </div>
                        </div>
                    @endif
                </div>
                @error('jenis_kel')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Status @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="hidden" name="status" id="status"
                    value="{{ old('status', $anakAsuh?->status) }}">
                <div class="anak-asuh-custom-dropdown {{ $isReadOnly ? 'disabled' : '' }}" data-dropdown>
                    <button type="button" data-dropdown-trigger {{ $isReadOnly ? 'disabled' : '' }}
                        class="anak-asuh-form-input anak-asuh-dropdown-trigger" aria-expanded="false">
                        <span class="anak-asuh-dropdown-value">
                            @php
                                $selectedStatus = old('status', $anakAsuh?->status);
                                $statusOptions = \App\Models\AnakAsuh::STATUS_OPTIONS;
                                echo $selectedStatus && isset($statusOptions[$selectedStatus])
                                    ? $statusOptions[$selectedStatus]
                                    : 'Pilih status';
                            @endphp
                        </span>
                        <svg class="anak-asuh-dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    @if (!$isReadOnly)
                        <div data-dropdown-menu class="anak-asuh-dropdown-menu">
                            <div data-dropdown-option value=""
                                class="anak-asuh-dropdown-option {{ old('status', $anakAsuh?->status ?? '') == '' ? 'selected' : '' }}">
                                Pilih status
                            </div>
                            @foreach (\App\Models\AnakAsuh::STATUS_OPTIONS as $val => $label)
                                <div data-dropdown-option value="{{ $val }}"
                                    class="anak-asuh-dropdown-option {{ old('status', $anakAsuh?->status ?? '') == $val ? 'selected' : '' }}">
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('status')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Grade @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="hidden" name="grade" id="grade" value="{{ old('grade', $anakAsuh?->grade) }}">
                <div class="anak-asuh-custom-dropdown {{ $isReadOnly ? 'disabled' : '' }}" data-dropdown>
                    <button type="button" data-dropdown-trigger {{ $isReadOnly ? 'disabled' : '' }}
                        class="anak-asuh-form-input anak-asuh-dropdown-trigger" aria-expanded="false">
                        <span class="anak-asuh-dropdown-value">
                            @php
                                $selectedGrade = old('grade', $anakAsuh?->grade);
                                echo $selectedGrade ? 'Grade ' . $selectedGrade : 'Pilih grade';
                            @endphp
                        </span>
                        <svg class="anak-asuh-dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    @if (!$isReadOnly)
                        <div data-dropdown-menu class="anak-asuh-dropdown-menu">
                            <div data-dropdown-option value=""
                                class="anak-asuh-dropdown-option {{ old('grade', $anakAsuh?->grade ?? '') == '' ? 'selected' : '' }}">
                                Pilih grade
                            </div>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $g)
                                <div data-dropdown-option value="{{ $g }}"
                                    class="anak-asuh-dropdown-option {{ old('grade', $anakAsuh?->grade ?? '') == $g ? 'selected' : '' }}">
                                    Grade {{ $g }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('grade')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="pendidikan_kelas" class="anak-asuh-form-label">Kelas Pendidikan</label>
                <input type="text" id="pendidikan_kelas" name="pendidikan_kelas"
                    value="{{ old('pendidikan_kelas', $anakAsuh?->pendidikan_kelas) }}"
                    {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('pendidikan_kelas') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan kelas pendidikan">
                @error('pendidikan_kelas')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="nama_orang_tua" class="anak-asuh-form-label">
                    Nama Orang Tua/Wali @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="text" id="nama_orang_tua" name="nama_orang_tua"
                    value="{{ old('nama_orang_tua', $anakAsuh?->nama_orang_tua) }}"
                    {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                    class="anak-asuh-form-input @error('nama_orang_tua') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan nama orang tua/wali">
                @error('nama_orang_tua')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="no_handphone" class="anak-asuh-form-label">No. Handphone</label>
                <input type="text" id="no_handphone" name="no_handphone"
                    value="{{ old('no_handphone', $anakAsuh?->no_handphone) }}"
                    {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('no_handphone') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan no. handphone">
                @error('no_handphone')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Tanggal Masuk RH @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <div class="anak-asuh-datepicker-wrapper {{ $isReadOnly ? 'disabled' : '' }}">
                    <input type="text" id="tanggal_masuk_rh" name="tanggal_masuk_rh"
                        value="{{ old('tanggal_masuk_rh', $anakAsuh?->tanggal_masuk_rh) }}"
                        {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                        class="anak-asuh-form-input @error('tanggal_masuk_rh') anak-asuh-is-invalid @enderror"
                        placeholder="Pilih tanggal masuk RH">
                    <span class="anak-asuh-datepicker-icon">
                        <i data-lucide="calendar" class="anak-asuh-datepicker-lucide-icon"></i>
                    </span>
                </div>
                @error('tanggal_masuk_rh')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label class="anak-asuh-form-label">
                    Asrama Rumah Harapan @if (!$isReadOnly)
                        <span class="anak-asuh-required">*</span>
                    @endif
                </label>
                <input type="hidden" name="rh" id="rh"
                    value="{{ old('rh', $anakAsuh?->rumahHarapan?->kode) }}">
                <div class="anak-asuh-custom-dropdown {{ $isReadOnly ? 'disabled' : '' }}" data-dropdown>
                    <button type="button" data-dropdown-trigger {{ $isReadOnly ? 'disabled' : '' }}
                        class="anak-asuh-form-input anak-asuh-dropdown-trigger" aria-expanded="false">
                        <span class="anak-asuh-dropdown-value">
                            @php
                                $selectedRh = old('rh', $anakAsuh?->rumahHarapan?->kode);
                                $selectedAsrama = $asramas->firstWhere('kode', $selectedRh);
                                echo $selectedAsrama ? $selectedAsrama->nama : 'Pilih asrama';
                            @endphp
                        </span>
                        <svg class="anak-asuh-dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    @if (!$isReadOnly)
                        <div data-dropdown-menu class="anak-asuh-dropdown-menu">
                            <div data-dropdown-option value=""
                                class="anak-asuh-dropdown-option {{ old('rh', $anakAsuh?->rumahHarapan?->kode ?? '') == '' ? 'selected' : '' }}">
                                Pilih asrama
                            </div>
                            @foreach ($asramas as $asrama)
                                <div data-dropdown-option value="{{ $asrama->kode }}"
                                    class="anak-asuh-dropdown-option {{ old('rh', $anakAsuh?->rumahHarapan?->kode ?? '') == $asrama->kode ? 'selected' : '' }}">
                                    {{ $asrama->nama }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('rh')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="yang_mengasuh_sebelum_diasrama" class="anak-asuh-form-label">
                    Yang Mengasuh Sebelum di Asrama
                </label>
                <input type="text" id="yang_mengasuh_sebelum_diasrama" name="yang_mengasuh_sebelum_diasrama"
                    value="{{ old('yang_mengasuh_sebelum_diasrama', $anakAsuh?->yang_mengasuh_sebelum_diasrama) }}"
                    {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('yang_mengasuh_sebelum_diasrama') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan nama pengasuh sebelumnya">
                @error('yang_mengasuh_sebelum_diasrama')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="anak-asuh-form-group">
                <label for="rekomendasi" class="anak-asuh-form-label">Rekomendasi</label>
                <textarea id="rekomendasi" name="rekomendasi" rows="3" {{ $isReadOnly ? 'readonly disabled' : '' }}
                    class="anak-asuh-form-input @error('rekomendasi') anak-asuh-is-invalid @enderror"
                    placeholder="Masukkan rekomendasi">{{ old('rekomendasi', $anakAsuh?->rekomendasi) }}</textarea>
                @error('rekomendasi')
                    <span class="anak-asuh-form-error">{{ $message }}</span>
                @enderror
            </div>

            {{--
                Field is_active — masuk kolom kanan:
                - Edit (!isReadOnly) : dropdown Aktif/Tidak Aktif
                - Show (isReadOnly)  : badge readonly
                - Create             : tidak tampil (hardcoded true di service)
            --}}
            @if ($isEdit)
                <div class="anak-asuh-form-group anak-asuh-is-active-group">
                    <label class="anak-asuh-form-label">
                        Status Keaktifan
                        @if (!$isReadOnly)
                            <span class="anak-asuh-required">*</span>
                        @endif
                    </label>

                    @if ($isReadOnly)
                        <input type="text" value="{{ $anakAsuh?->is_active ? 'Aktif' : 'Tidak Aktif' }}" readonly
                            disabled class="anak-asuh-form-input">
                    @else
                        <input type="hidden" name="is_active" id="is_active"
                            value="{{ old('is_active', $anakAsuh?->is_active ? '1' : '0') }}">
                        <div class="anak-asuh-custom-dropdown" data-dropdown>
                            <button type="button" data-dropdown-trigger
                                class="anak-asuh-form-input anak-asuh-dropdown-trigger" aria-expanded="false">
                                <span class="anak-asuh-dropdown-value">
                                    @php
                                        $isActiveVal = old('is_active', $anakAsuh?->is_active ? '1' : '0');
                                        echo $isActiveVal === '1' ? 'Aktif' : 'Tidak Aktif';
                                    @endphp
                                </span>
                                <svg class="anak-asuh-dropdown-chevron" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div data-dropdown-menu class="anak-asuh-dropdown-menu">
                                <div data-dropdown-option value="1"
                                    class="anak-asuh-dropdown-option {{ old('is_active', $anakAsuh?->is_active ? '1' : '0') == '1' ? 'selected' : '' }}">
                                    Aktif
                                </div>
                                <div data-dropdown-option value="0"
                                    class="anak-asuh-dropdown-option {{ old('is_active', $anakAsuh?->is_active ? '1' : '0') == '0' ? 'selected' : '' }}">
                                    Tidak Aktif
                                </div>
                            </div>
                        </div>
                        @error('is_active')
                            <span class="anak-asuh-form-error">{{ $message }}</span>
                        @enderror
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($isEdit)
        <div class="anak-asuh-form-divider"></div>
        <div class="anak-asuh-form-group anak-asuh-berkas-group">
            <label class="anak-asuh-form-label">Berkas Anak Asuh</label>
            <div class="anak-asuh-berkas-container" id="berkasContainer">

                @if ($canManageBerkas || !$isReadOnly)
                    <div class="anak-asuh-berkas-upload-area">
                        <button type="button" class="anak-asuh-btn-berkas-add" id="addBerkasBtn">
                            <i data-lucide="plus" class="anak-asuh-btn-icon"></i>
                            <span>Tambah Berkas</span>
                        </button>
                    </div>
                @endif

                <div class="anak-asuh-berkas-list" id="berkasList">
                    @if ($anakAsuh?->berkasAnak->count() > 0)
                        @foreach ($anakAsuh->berkasAnak as $berkas)
                            <div class="anak-asuh-berkas-item" data-berkas-type="existing"
                                data-berkas-id="{{ $berkas->id }}">
                                <div class="anak-asuh-berkas-info">
                                    <span class="anak-asuh-berkas-name">{{ $berkas->original_name }}</span>
                                </div>
                                <div class="anak-asuh-berkas-actions">
                                    <a href="{{ Storage::url($berkas->file_path) }}" target="_blank"
                                        class="anak-asuh-btn-berkas-view" title="Lihat Berkas">
                                        <i data-lucide="eye"></i>
                                    </a>

                                    @if ($isReadOnly && !$canManageBerkas)
                                        <a href="{{ Storage::url($berkas->file_path) }}" download
                                            class="anak-asuh-btn-berkas-download" title="Download Berkas">
                                            <i data-lucide="download"></i>
                                        </a>
                                    @endif

                                    @if ($canManageBerkas || !$isReadOnly)
                                        <button type="button" class="anak-asuh-btn-berkas-delete"
                                            onclick="deleteBerkas({{ $berkas->id }})" title="Hapus Berkas">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="anak-asuh-berkas-empty">Belum ada berkas yang diunggah.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        window.userData = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>
@endpush
