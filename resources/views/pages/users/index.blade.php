@extends('layouts.dashboard')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
    <div class="users-index">
        <div class="page-header">
            <div class="page-header-content">
                <h2 class="page-title">MANAJEMEN USER</h2>
                <p class="page-description">
                    Halaman kelola data user sistem untuk mendukung kelancaran operasional.
                </p>
            </div>
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('users.create') }}" class="btn-primary">
                    <i data-lucide="plus" class="btn-icon"></i>
                    Tambah User
                </a>
            @endif
        </div>

        <!-- Search Input (Tanpa Button Submit) -->
        <div class="search-container">
            <div class="search-wrapper">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="searchInput" class="search-input"
                    placeholder="Cari nama, email, atau role pengguna..." aria-label="Cari pengguna">
                <!-- Loading Spinner untuk Search -->
                <div class="search-loader" id="searchLoader">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

        <!-- Table Card (Struktur lengkap di Blade) -->
        <div class="card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center no-column">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center action-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- Tabel akan diisi oleh JavaScript -->
                        <tr class="table-row">
                            <td colspan="5" class="text-center py-5">
                                <div class="loading-spinner">
                                    <div class="spinner"></div>
                                    <p class="mt-2">Memuat data pengguna...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Container (Kosong - Diisi JavaScript) -->
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
        // Inisialisasi data global untuk JavaScript
        window.userData = {
            // CSRF Token untuk keamanan request POST/PUT/DELETE
            csrfToken: '{{ csrf_token() }}',
            
            // Base URL aplikasi (misal: http://localhost:8000)
            baseUrl: '{{ url('/') }}'
        };
    </script>
    @vite(['resources/js/pages/users/index.js'])
@endpush