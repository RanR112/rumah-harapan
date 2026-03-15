@extends('layouts.dashboard')

@section('title', 'Edit Data Asrama')
@section('page-title', 'Edit Data Asrama')

@section('content')
    <div class="rumah-harapan-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">EDIT DATA ASRAMA</h1>
                <p class="page-description">
                    Perbarui data asrama di bawah ini.
                    Semua field wajib diisi.
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
                <form method="POST" action="{{ route('rumah-harapan.update', $rumahHarapan->id) }}"
                    class="rumah-harapan-form">
                    @csrf
                    @method('PUT')

                    {{-- Diisi oleh index.js dari page number di sessionStorage returnUrl --}}
                    <input type="hidden" name="current_page" id="currentPageInput" value="1">

                    @include('pages.rumah-harapan.form', [
                        'rumahHarapan' => $rumahHarapan,
                        'isEdit' => true,
                    ])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-rh">
                            <span class="btn-text">Perbarui Data</span>
                            <div class="btn-loader" style="display: none;">
                                @include('components.loader.loader-pulse')
                            </div>
                        </button>
                    </div>
                </form>
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
