@extends('layouts.dashboard')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
<div class="profile-form-page">
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">EDIT PROFIL</h1>
            <p class="page-description">
                Perbarui informasi profil Anda. 
                <strong>Kata sandi hanya perlu diisi jika ingin mengubahnya.</strong>
            </p>
        </div>
        <button type="button" class="btn-header-back" onclick="window.location.href='{{ route('dashboard') }}'">
            <i data-lucide="chevron-left" class="btn-icon"></i>
            <span>Kembali</span>
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                @csrf
                @method('PATCH')
                
                @include('pages.profile.form', ['user' => $user])
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert Modal Overlay (Sama seperti di users index) -->
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
@endsection

@push('scripts')
<script>
    // Setup global userData
    window.userData = {
        csrfToken: '{{ csrf_token() }}',
        baseUrl: '{{ url('/') }}'
    };
</script>
@vite(['resources/js/pages/profile/index.js'])
@endpush