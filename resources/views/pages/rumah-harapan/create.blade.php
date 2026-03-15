@extends('layouts.dashboard')

@section('title', 'Tambah Asrama')
@section('page-title', 'Tambah Data Asrama')

@section('content')
    <div class="rumah-harapan-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">TAMBAH DATA ASRAMA</h1>
                <p class="page-description">
                    Isi form di bawah ini untuk menambah asrama baru ke sistem.
                    Pastikan data yang dimasukkan valid dan lengkap.
                </p>
            </div>
            {{-- Button Kembali di Header (Hanya tampil di ≤480px) --}}
            <button type="button" class="btn-header-back" onclick="window.location.href='{{ route('rumah-harapan.index') }}'">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('rumah-harapan.store') }}" class="rumah-harapan-form">
                    @csrf
                    @include('pages.rumah-harapan.form', ['isEdit' => false])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-rh">
                            <span class="btn-text">Simpan Data</span>
                            <div class="btn-loader" style="display: none;">
                                @include('components.loader.loader-pulse')
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        
        @vite(['resources/js/pages/rumah-harapan/index.js'])
    @endpush
@endsection
