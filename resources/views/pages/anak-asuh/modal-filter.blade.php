<div id="filterModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Filter Data</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <div class="modal-body">
            <!-- Status Filter -->
            <div class="custom-dropdown" data-filter="status">
                <button type="button" class="custom-dropdown-trigger">
                    <span class="dropdown-text">Semua Status</span>
                    <i data-lucide="chevron-down" class="dropdown-icon"></i>
                </button>
                <div class="custom-dropdown-menu">
                    <div class="dropdown-item" data-value="">Semua Status</div>
                    <div class="dropdown-item" data-value="aktif">Aktif</div>
                    <div class="dropdown-item" data-value="nonaktif">Non-Aktif</div>
                </div>
                <input type="hidden" id="modal-status-filter" name="status" value="">
            </div>
            
            <!-- Grade Filter -->
            <div class="custom-dropdown" data-filter="grade">
                <button type="button" class="custom-dropdown-trigger">
                    <span class="dropdown-text">Semua Grade</span>
                    <i data-lucide="chevron-down" class="dropdown-icon"></i>
                </button>
                <div class="custom-dropdown-menu">
                    <div class="dropdown-item" data-value="">Semua Grade</div>
                    <div class="dropdown-item" data-value="A">Grade A</div>
                    <div class="dropdown-item" data-value="B">Grade B</div>
                    <div class="dropdown-item" data-value="C">Grade C</div>
                    <div class="dropdown-item" data-value="D">Grade D</div>
                    <div class="dropdown-item" data-value="E">Grade E</div>
                </div>
                <input type="hidden" id="modal-grade-filter" name="grade" value="">
            </div>
            
            <!-- Cabang Filter -->
            <div class="custom-dropdown" data-filter="rh">
                <button type="button" class="custom-dropdown-trigger">
                    <span class="dropdown-text">Semua Cabang</span>
                    <i data-lucide="chevron-down" class="dropdown-icon"></i>
                </button>
                <div class="custom-dropdown-menu">
                    <div class="dropdown-item" data-value="">Semua Cabang</div>
                    @foreach($cabangs as $cabang)
                        <div class="dropdown-item" data-value="{{ $cabang->kode }}">{{ $cabang->nama }}</div>
                    @endforeach
                </div>
                <input type="hidden" id="modal-rh-filter" name="rh" value="">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-modal-close>Batal</button>
            <button type="button" class="btn-primary" id="applyFilterBtn">Terapkan Filter</button>
        </div>
    </div>
</div>