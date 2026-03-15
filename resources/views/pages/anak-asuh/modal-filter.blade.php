<div id="filterModal" class="modal-overlay-filter">
    <div class="modal-container-filter">
        <div class="modal-header-filter">
            <h3 class="modal-title">Filter Data</h3>
            <button type="button" class="filter-modal-close" data-modal-close>
                <i data-lucide="x" class="filter-modal-close-icon"></i>
            </button>
        </div>
        <div class="modal-body-filter">

            {{-- Status Filter (yatim, piatu, yatim piatu, dhuafa) --}}
            <div class="modal-custom-dropdown" data-filter="status">
                <button type="button" class="modal-dropdown-trigger">
                    <span class="modal-dropdown-text">Semua Status</span>
                    <i data-lucide="chevron-down" class="modal-dropdown-icon"></i>
                </button>
                <div class="modal-dropdown-menu">
                    <div class="modal-dropdown-item" data-value="">Semua Status</div>
                    <div class="modal-dropdown-item" data-value="yatim">Yatim</div>
                    <div class="modal-dropdown-item" data-value="piatu">Piatu</div>
                    <div class="modal-dropdown-item" data-value="yatim_piatu">Yatim Piatu</div>
                    <div class="modal-dropdown-item" data-value="dhuafa">Dhuafa</div>
                </div>
                <input type="hidden" id="modal-status-filter" name="status" value="">
            </div>

            {{-- Keaktifan Filter (is_active) --}}
            <div class="modal-custom-dropdown" data-filter="is_active">
                <button type="button" class="modal-dropdown-trigger">
                    <span class="modal-dropdown-text">Semua Keaktifan</span>
                    <i data-lucide="chevron-down" class="modal-dropdown-icon"></i>
                </button>
                <div class="modal-dropdown-menu">
                    <div class="modal-dropdown-item" data-value="">Semua Keaktifan</div>
                    <div class="modal-dropdown-item" data-value="1">Aktif</div>
                    <div class="modal-dropdown-item" data-value="0">Tidak Aktif</div>
                </div>
                <input type="hidden" id="modal-is-active-filter" name="is_active" value="">
            </div>

            {{-- Grade Filter --}}
            <div class="modal-custom-dropdown" data-filter="grade">
                <button type="button" class="modal-dropdown-trigger">
                    <span class="modal-dropdown-text">Semua Grade</span>
                    <i data-lucide="chevron-down" class="modal-dropdown-icon"></i>
                </button>
                <div class="modal-dropdown-menu">
                    <div class="modal-dropdown-item" data-value="">Semua Grade</div>
                    <div class="modal-dropdown-item" data-value="A">Grade A</div>
                    <div class="modal-dropdown-item" data-value="B">Grade B</div>
                    <div class="modal-dropdown-item" data-value="C">Grade C</div>
                    <div class="modal-dropdown-item" data-value="D">Grade D</div>
                    <div class="modal-dropdown-item" data-value="E">Grade E</div>
                </div>
                <input type="hidden" id="modal-grade-filter" name="grade" value="">
            </div>

            {{-- Asrama Filter --}}
            <div class="modal-custom-dropdown" data-filter="rh">
                <button type="button" class="modal-dropdown-trigger">
                    <span class="modal-dropdown-text">Semua Asrama</span>
                    <i data-lucide="chevron-down" class="modal-dropdown-icon"></i>
                </button>
                <div class="modal-dropdown-menu">
                    <div class="modal-dropdown-item" data-value="">Semua Asrama</div>
                    @foreach ($asramas as $asrama)
                        <div class="modal-dropdown-item" data-value="{{ $asrama->kode }}">{{ $asrama->kode }}</div>
                    @endforeach
                </div>
                <input type="hidden" id="modal-rh-filter" name="rh" value="">
            </div>

        </div>
        <div class="modal-footer-filter">
            <button type="button" class="anak-asuh-btn-filter-back" data-modal-close>Batal</button>
            {{--
                Button apply/reset filter:
                - Jika belum ada filter aktif → teks "Terapkan Filter" (data-action="apply")
                - Jika sudah ada filter aktif  → teks "Reset Filter"   (data-action="reset")
                JavaScript yang mengganti teks dan data-action sesuai kondisi filter aktif
            --}}
            <button type="button" class="anak-asuh-btn-filter-apply" id="applyFilterBtn" data-action="apply">
                Terapkan Filter
            </button>
        </div>
    </div>
</div>
