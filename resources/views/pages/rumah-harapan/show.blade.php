{{--
    ============================================================
    HALAMAN INI HANYA UNTUK ROLE PETUGAS (read-only)
    - Diakses via route: GET /rumah-harapan/{id}
    - Route dijaga oleh middleware role:admin,petugas
    - Semua field ditampilkan dalam mode read-only
    - Tidak ada tombol simpan / ubah data
    ============================================================
--}}

@extends('layouts.dashboard')

@section('title', 'Detail Asrama')
@section('page-title', 'Detail Data Asrama')

@section('content')
    <div class="rumah-harapan-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">DETAIL DATA ASRAMA</h1>
                <p class="page-description">
                    Informasi lengkap data asrama. Hubungi admin untuk melakukan perubahan data.
                </p>
            </div>
            {{-- onclick diisi oleh index.js via sessionStorage --}}
            <button type="button" class="btn-header-back" id="btnBack">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="rumah-harapan-form">
                    {{--
                        form.blade.php dipakai ulang dengan isShow=true:
                        - Semua input menjadi readonly + disabled
                        - Label required (*) disembunyikan
                        - Placeholder dihilangkan
                        - Status ditampilkan sebagai badge, bukan dropdown
                        - Tidak ada form tag dan tombol submit
                    --}}
                    @include('pages.rumah-harapan.form', [
                        'rumahHarapan' => $rumahHarapan,
                        'isEdit' => false,
                        'isShow' => true,
                    ])
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
