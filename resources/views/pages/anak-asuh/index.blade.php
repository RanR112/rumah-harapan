@extends('layouts.dashboard')

@section('title', 'Manajemen Anak Asuh')
@section('page-title', 'Manajemen Anak Asuh')

@section('content')
    <div class="anak-asuh-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">MANAJEMEN ANAK ASUH</h1>
                <p class="page-description">
                    Halaman pengelolaan data anak asuh Rumah Harapan untuk pendataan yang konsisten.
                </p>
            </div>

            <!-- Action Buttons - Struktur konsisten -->
            <div class="page-actions">
                <!-- Export Button - Gunakan button langsung -->
                <button type="button" class="btn-export" onclick="submitExportForm()">
                    <i data-lucide="download" class="btn-icon"></i>
                    <span>Export Data</span>
                </button>

                <!-- Import Button - Tetap button langsung -->
                <button type="button" class="btn-import" data-modal-trigger="import-modal">
                    <i data-lucide="upload" class="btn-icon"></i>
                    <span>Import Data</span>
                </button>

                <!-- Tambah Anak Asuh -->
                <button type="button" class="btn-create-anak-asuh"
                    onclick="window.location.href='{{ route('anak-asuh.create') }}'">
                    <i data-lucide="plus" class="btn-icon"></i>
                    <span>Tambah Anak Asuh</span>
                </button>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-input-wrapper">
                    <!-- Icon search di sebelah kiri -->
                    <i data-lucide="search" class="search-icon-left"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama atau NIK anak asuh...">
                    <div id="searchLoader" class="search-loader"></div>
                </div>

                <!-- Filter Button untuk Mobile -->
                <button type="button" class="btn-filter-mobile" data-modal-trigger="filter-modal">
                    <i data-lucide="funnel" class="btn-icon"></i>
                </button>

                <!-- Filter Dropdowns (untuk desktop/tablet) -->
                <div class="filter-buttons">
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
                        <input type="hidden" id="statusFilter" name="status" value="">
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
                        <input type="hidden" id="gradeFilter" name="grade" value="">
                    </div>

                    <!-- Cabang Filter -->
                    <div class="custom-dropdown custom-dropdown--cabang" data-filter="rh">
                        <button type="button" class="custom-dropdown-trigger">
                            <span class="dropdown-text">Semua Cabang</span>
                            <i data-lucide="chevron-down" class="dropdown-icon"></i>
                        </button>
                        <div class="custom-dropdown-menu">
                            <div class="dropdown-item" data-value="">Semua Cabang</div>
                            @foreach ($cabangs as $cabang)
                                <div class="dropdown-item" data-value="{{ $cabang->kode }}">{{ $cabang->nama }}</div>
                            @endforeach
                        </div>
                        <input type="hidden" id="rhFilter" name="rh" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Container -->
        <div class="anak-asuh-container" id="anakAsuhContainer">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p class="mt-2">Memuat data...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer" style="display: none;">
            <!-- Pagination will be rendered here by JavaScript -->
        </div>
    </div>

    <!-- Alert Modal Overlay -->
    <div id="alertModal" class="alert-modal-overlay">
        <div class="alert-modal-container" data-alert-type="confirm">
            <div class="alert-modal-content">
                <div class="alert-icon alert-icon--confirm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="alert-title" id="alertModalTitle">Konfirmasi</h3>
                <p class="alert-message" id="alertModalMessage">Apakah Anda yakin?</p>
                <div class="alert-actions">
                    <button class="alert-btn alert-btn-cancel" id="alertCancelBtn">Batal</button>
                    <button class="alert-btn alert-btn-confirm" id="alertConfirmBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form untuk Export -->
    <form id="exportForm" method="POST" action="{{ route('anak-asuh.export') }}" style="display: none;">
        @csrf
        <input type="hidden" name="format" value="xlsx">
    </form>

    <!-- Import Modal -->
    <div id="importModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Import Data Anak Asuh</h3>
                <button type="button" class="modal-close" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-description">
                    Unggah file Excel (.xlsx) atau CSV yang berisi data anak asuh.
                    Pastikan format kolom sesuai dengan template yang disediakan.
                </p>

                <form method="POST" action="{{ route('anak-asuh.import') }}" enctype="multipart/form-data"
                    class="import-form">
                    @csrf
                    <div class="form-group">
                        <label for="importFile" class="form-label">Pilih File</label>
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required
                            class="form-input">
                        <p class="form-hint">Format yang didukung: .xlsx, .xls, .csv (max 2MB)</p>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" data-modal-close>Batal</button>
                        <button type="submit" class="btn-primary">Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('pages.anak-asuh.modal-filter')

@endsection

@push('scripts')
    <script>
        window.userData = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>
    @vite(['resources/js/pages/anak-asuh/index.js'])

    {{-- Custom Dropdown JavaScript --}}
    <script>
        (function() {
            'use strict';

            // Initialize all custom dropdowns
            function initCustomDropdowns() {
                const dropdowns = document.querySelectorAll('.custom-dropdown');

                dropdowns.forEach(dropdown => {
                    const trigger = dropdown.querySelector('.custom-dropdown-trigger');
                    const menu = dropdown.querySelector('.custom-dropdown-menu');
                    const hiddenInput = dropdown.querySelector('input[type="hidden"]');
                    const dropdownText = dropdown.querySelector('.dropdown-text');
                    const filterType = dropdown.dataset.filter;

                    // Close other dropdowns when opening this one
                    function closeOtherDropdowns() {
                        dropdowns.forEach(otherDropdown => {
                            if (otherDropdown !== dropdown) {
                                otherDropdown.querySelector('.custom-dropdown-menu').classList.remove(
                                    'show');
                                otherDropdown.querySelector('.custom-dropdown-trigger').classList
                                    .remove('active');
                            }
                        });
                    }

                    // Toggle dropdown
                    trigger.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const isMenuVisible = menu.classList.contains('show');

                        // Close all dropdowns first
                        dropdowns.forEach(d => {
                            d.querySelector('.custom-dropdown-menu').classList.remove('show');
                            d.querySelector('.custom-dropdown-trigger').classList.remove(
                                'active');
                        });

                        // Toggle current dropdown if it't already open
                        if (!isMenuVisible) {
                            menu.classList.add('show');
                            trigger.classList.add('active');
                        }
                    });

                    // Handle item selection
                    menu.querySelectorAll('.dropdown-item').forEach(item => {
                        item.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const value = this.dataset.value;
                            const text = this.textContent;

                            // Update hidden input and display text
                            hiddenInput.value = value;
                            dropdownText.textContent = text;

                            // Close menu
                            menu.classList.remove('show');
                            trigger.classList.remove('active');

                            // Trigger search with new filter
                            if (window.anakAsuhSearchHandler) {
                                window.anakAsuhSearchHandler();
                            }
                        });
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            menu.classList.remove('show');
                            trigger.classList.remove('active');
                        }
                    });

                    // Close dropdown when pressing Escape
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            menu.classList.remove('show');
                            trigger.classList.remove('active');
                        }
                    });
                });
            }

            // Function untuk submit export form
            function submitExportForm() {
                document.getElementById('exportForm').submit();
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initCustomDropdowns();
                    window.submitExportForm = submitExportForm;
                });
            } else {
                initCustomDropdowns();
                window.submitExportForm = submitExportForm;
            }
        })();
    </script>
@endpush
