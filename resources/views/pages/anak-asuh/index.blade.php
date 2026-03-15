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
                    Halaman pengelolaan data anak asuh rumah harapan untuk pendataan yang konsisten.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="page-actions">
                <!-- Export Button -->
                <button type="button" class="btn-export" onclick="submitExportForm()">
                    <i data-lucide="download" class="btn-icon"></i>
                    <span>Ekspor Data</span>
                </button>

                <!-- Import Button -->
                <button type="button" id="openImportModalBtn" class="btn-import">
                    <i data-lucide="upload" class="btn-icon"></i>
                    <span>Impor Data</span>
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
        <div class="anak-asuh-filters">
            <div class="anak-asuh-filter-row">
                <!-- Search Input -->
                <div class="search-container">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon-left"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama atau NIK anak asuh...">
                        <div id="searchLoader" class="search-loader">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                {{-- Reset Filter Button — muncul jika ada filter aktif --}}
                <button type="button" id="anakAsuhResetFilterBtn" class="anak-asuh-reset-filter-btn"
                    title="Reset semua filter" style="display: none;">
                    Reset
                </button>
                {{--
                    Tombol Filter — membuka modal filter untuk semua ukuran layar.
                    Desktop/tablet: tampilkan icon + teks "Filter"
                    Mobile        : tampilkan icon saja (teks disembunyikan via CSS)
                --}}
                <button type="button" class="btn-filter-modal" id="openFilterModalBtn" data-modal-trigger="filter-modal">
                    <i data-lucide="funnel" class="btn-icon"></i>
                    <span class="btn-filter-text">Filter</span>
                </button>
            </div>

            {{-- Hidden inputs — dibaca oleh JS (fetchData, hasActiveFilters, updateResetButtonVisibility) --}}
            <input type="hidden" id="statusFilter" value="">
            <input type="hidden" id="isActiveFilter" value="">
            <input type="hidden" id="gradeFilter" value="">
            <input type="hidden" id="rhFilter" value="">
        </div>

        <!-- Data Container -->
        <div class="anak-asuh-container" id="anakAsuhContainer">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p class="mt-2">Memuat data...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
            <!-- Pagination will be rendered here by JavaScript -->
        </div>
    </div>

    <!-- Alert Modal (Global) -->
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

    <!-- Include modal import -->
    @include('pages.anak-asuh.modal-import')

    <!-- Filter Modal -->
    @include('pages.anak-asuh.modal-filter')
@endsection

@push('scripts')
    <script>
        window.userData = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}',
            initialFilters: {
                search: '{{ request('search') }}',
                status: '{{ request('status') }}',
                is_active: '{{ request('is_active') }}',
                grade: '{{ request('grade') }}',
                rh: '{{ request('rh') }}'
            }
        };

        window.submitExportForm = function() {
            document.getElementById('exportForm').submit();
        };
    </script>
    @vite(['resources/js/pages/anak-asuh/index.js'])
@endpush
