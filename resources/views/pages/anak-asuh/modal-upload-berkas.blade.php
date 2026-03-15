<div id="modalUploadBerkas" class="modal-berkas-backdrop">
    <div class="modal-berkas-container">
        <div class="modal-berkas-header">
            <h3 class="modal-berkas-title">Tambah Berkas</h3>
            <button type="button" class="modal-berkas-close" id="closeModalBerkas">
                <i data-lucide="x" class="modal-berkas-close-icon"></i>
            </button>
        </div>

        <div class="modal-berkas-body">
            <form id="formUploadBerkas" class="modal-berkas-form">
                {{-- Nama Berkas --}}
                <div class="modal-berkas-form-group">
                    <label for="berkasNama" class="modal-berkas-label">
                        Nama Berkas <span class="modal-berkas-required">*</span>
                    </label>
                    <input type="text" id="berkasNama" class="modal-berkas-input"
                        placeholder="Contoh: Akta Kelahiran, KTP, dll" required>
                    <span class="modal-berkas-error" id="berkasNamaError"></span>
                </div>

                {{-- File Upload --}}
                <div class="modal-berkas-form-group">
                    <label for="berkasFile" class="modal-berkas-label">
                        Pilih File <span class="modal-berkas-required">*</span>
                    </label>
                    <div class="modal-berkas-file-wrapper">
                        <input type="file" id="berkasFile" class="modal-berkas-file-input"
                            accept=".jpg,.jpeg,.png,.pdf" required>
                        <label for="berkasFile" class="modal-berkas-file-label">
                            <i data-lucide="upload" class="modal-berkas-upload-icon"></i>
                            <span id="berkasFileName">Pilih file...</span>
                        </label>
                    </div>
                    <p class="modal-berkas-hint">Format: JPG, PNG, PDF (max 5MB)</p>
                    <span class="modal-berkas-error" id="berkasFileError"></span>
                </div>
            </form>
        </div>

        <div class="modal-berkas-footer">
            <button type="button" class="modal-berkas-btn modal-berkas-btn-cancel" id="cancelModalBerkas">
                Batal
            </button>

            {{-- Struktur btn-text + btn-loader konsisten dengan sistem --}}
            <button type="button" class="modal-berkas-btn modal-berkas-btn-upload" id="uploadBerkasBtn">
                <span class="btn-text">
                    Upload Berkas
                </span>
                <div class="btn-loader" style="display: none;">
                    @include('components.loader.loader-pulse')
                </div>
            </button>
        </div>
    </div>
</div>
