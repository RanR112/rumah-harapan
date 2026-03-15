@extends('layouts.dashboard')

@section('title', Auth::user()->role === 'admin' ? 'Manajemen Asrama' : 'Data Asrama')
@section('page-title', Auth::user()->role === 'admin' ? 'Manajemen Asrama' : 'Data Asrama')

@section('content')
    <div class="rumah-harapan-index">
        <div class="page-header">
            <div class="page-header-content">
                @if (Auth::user()->role === 'admin')
                    <h2 class="page-title">MANAJEMEN ASRAMA RUMAH HARAPAN</h2>
                    <p class="page-description">
                        Halaman kelola data asrama rumah harapan untuk mendukung kelancaran operasional.
                    </p>
                @else
                    <h2 class="page-title">DATA ASRAMA RUMAH HARAPAN</h2>
                    <p class="page-description">
                        Informasi data asrama rumah harapan yang terdaftar.
                    </p>
                @endif
            </div>

            {{-- Tombol Tambah Data — hanya untuk admin --}}
            @if (Auth::user()->role === 'admin')
                <div class="page-actions">
                    <button type="button" class="btn-create-rumah-harapan"
                        onclick="window.location.href='{{ route('rumah-harapan.create') }}'">
                        <i data-lucide="plus" class="btn-icon"></i>
                        <span> Tambah Data </span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Filter dan Search Section -->
        <div class="rumah-harapan-filters">
            <div class="filter-row">
                <!-- Search Input -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Cari nama atau kode asrama..." aria-label="Cari asrama">
                        <div class="search-loader" id="searchLoader">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                <div class="filters-wrapper-rh">
                    <button type="button" id="resetFilterBtnRh" class="reset-filter-btn-rh" title="Reset status filter"
                        style="display: none;">
                        Reset
                    </button>

                    <!-- Status Filter Custom Dropdown -->
                    <div class="rumah-harapan-status-filter">
                        <div class="custom-dropdown" data-filter="status">
                            <button type="button" class="form-input dropdown-trigger" id="statusFilterTrigger">
                                <span class="dropdown-value">Semua Status</span>
                                <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="dropdown-menu" id="statusFilterMenu">
                                <div class="dropdown-option" data-value="">Semua Status</div>
                                <div class="dropdown-option" data-value="1">Aktif</div>
                                <div class="dropdown-option" data-value="0">Non-Aktif</div>
                            </div>
                            <input type="hidden" id="statusFilterValue" name="is_active" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="data-table rumah-harapan-table">
                    <thead>
                        <tr>
                            <th class="text-center no-column">No</th>
                            <th>Kode Asrama</th>
                            <th>Nama Asrama</th>
                            <th>Status</th>
                            <th>Alamat</th>
                            <th class="text-center action-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rumahHarapanTableBody">
                        <tr class="table-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="loading-spinner">
                                    <div class="spinner"></div>
                                    <p class="mt-2">Memuat data asrama...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Container -->
        <div class="pagination-container" id="paginationContainer"></div>
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
                    <button class="alert-btn alert-btn-confirm" id="alertConfirmBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.userData = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}',
            userRole: '{{ Auth::user()->role }}',
        };
    </script>
    @vite(['resources/js/pages/rumah-harapan/index.js'])
@endpush
