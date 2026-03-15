<div id="importModal" class="import-modal-backdrop">
    <div class="import-modal-container">
        <div class="import-modal-header">
            <h3 class="import-modal-title">Import Data Anak Asuh</h3>
            <button type="button" id="closeImportModal" class="import-modal-close" aria-label="Tutup modal">
                <i data-lucide="x" class="import-modal-close-icon"></i>
            </button>
        </div>

        <div class="import-modal-body">
            <p class="import-modal-description">
                Unggah file Excel (.xlsx/.xls) atau CSV yang berisi data anak asuh.
                Pastikan kolom sesuai ketentuan, <button type="button" id="openFormatKolomBtn"
                    class="import-format-link">lihat format kolom</button>.
            </p>

            <form id="importForm" method="POST" action="{{ route('anak-asuh.import') }}" enctype="multipart/form-data"
                class="import-modal-form">

                {{-- Catatan Penting --}}
                <div class="import-note">
                    <p class="import-note-text">
                        <strong>Catatan:</strong> Data dengan NIK yang sudah terdaftar akan dilewati.
                        Foto dan berkas tidak diimpor, upload manual setelah impor data berhasil.
                    </p>
                </div>

                {{-- File Input --}}
                <div class="import-modal-form-group">
                    <label for="importFile" class="import-modal-label">Pilih File</label>
                    <div class="import-modal-file-wrapper">
                        <label for="importFile" class="import-modal-file-label" id="importFileLabel">
                            <i data-lucide="upload" class="import-modal-upload-icon"></i>
                            <span id="importFileName" class="import-modal-file-name">Pilih file...</span>
                        </label>
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv"
                            class="import-modal-file-input" aria-describedby="importFileHint">
                    </div>
                    <p id="importFileHint" class="import-modal-hint">
                        Format yang didukung: .xlsx, .xls, .csv (maksimal 2MB)
                    </p>
                    <p id="importFileError" class="import-modal-error" role="alert"></p>
                </div>

                <div class="import-modal-footer">
                    <button type="button" id="cancelImportModal" class="import-modal-btn import-modal-btn-cancel">
                        Batal
                    </button>
                    <button type="submit" id="importSubmitBtn" class="import-modal-btn import-modal-btn-upload">
                        <span class="btn-text">Import Data</span>
                        <span class="btn-loader" style="display: none;">
                            @include('components.loader.loader-pulse')
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Format Kolom --}}
<div id="formatKolomModal" class="format-kolom-backdrop">
    <div class="format-kolom-container">
        <div class="format-kolom-header">
            <h3 class="format-kolom-title">Format Kolom Import</h3>
            <button type="button" id="closeFormatKolomModal" class="import-modal-close" aria-label="Tutup modal">
                <i data-lucide="x" class="import-modal-close-icon"></i>
            </button>
        </div>
        <div class="format-kolom-body">
            <h4 class="import-format-title">Kolom Wajib:</h4>
            <ul class="import-format-list">
                <li>RH <span class="import-format-note">(Kode Asrama)</span></li>
                <li>Nama Anak <span class="import-format-note">atau NAMA_ANAK</span></li>
                <li>NIK</li>
                <li>No Kartu Keluarga <span class="import-format-note">atau NO_KK</span></li>
                <li>Jenis Kelamin <span class="import-format-note">atau Jenis Kel &mdash; isi: L / P</span></li>
                <li>Tanggal Lahir <span class="import-format-note">(YYYY-MM-DD)</span></li>
                <li>Status <span class="import-format-note">(yatim / piatu / yatim_piatu / dhuafa)</span></li>
                <li>Grade <span class="import-format-note">(A / B / C / D / E)</span></li>
                <li>Nama Orang Tua <span class="import-format-note">atau Nama Wali</span></li>
                <li>Tanggal Masuk RH <span class="import-format-note">(YYYY-MM-DD)</span></li>
            </ul>
            <h4 class="import-format-title" style="margin-top: 16px;">Kolom Opsional:</h4>
            <ul class="import-format-list">
                <li>Alamat Lengkap</li>
                <li>Tempat Lahir</li>
                <li>Pendidikan Kelas</li>
                <li>No Handphone <span class="import-format-note">atau No HP</span></li>
                <li>Yang Mengasuh Sebelum Diasrama <span class="import-format-note">atau Yang Mengasuh</span></li>
                <li>Rekomendasi</li>
            </ul>
            <p class="import-format-note" style="margin-top: 8px;">
                * Nama kolom fleksibel, huruf besar/kecil dan spasi atau underscore diterima. Contoh: "nama anak", "Nama
                Anak", "NAMA_ANAK"
            </p>
        </div>
        <div class="format-kolom-footer">
            <button type="button" id="closeFormatKolomBtn" class="import-modal-btn import-modal-btn-cancel">
                Tutup
            </button>
        </div>
    </div>
</div>
