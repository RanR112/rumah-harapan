@extends('layouts.dashboard')

@section('title', 'Tambah Anak Asuh')
@section('page-title', 'Tambah Anak Asuh')

@section('content')
    <div class="anak-asuh-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">TAMBAH ANAK ASUH</h1>
                <p class="page-description">
                    Isi form di bawah ini untuk menambah anak asuh baru ke sistem.
                    Pastikan data yang dimasukkan valid dan lengkap.
                </p>
            </div>
            <button type="button" class="btn-header-back" onclick="window.location.href='{{ route('anak-asuh.index') }}'">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('anak-asuh.store') }}" class="anak-asuh-form"
                    enctype="multipart/form-data">
                    @csrf
                    @include('pages.anak-asuh.form', [
                        'asramas' => $asramas,
                        'isEdit' => false,
                        'isReadOnly' => false,
                    ])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-anak-asuh">
                            <span class="btn-text">Simpan Anak Asuh</span>
                            <div class="btn-loader" style="display: none">
                                @include('components.loader.loader-pulse')
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.alert-modal')
@endsection

@push('scripts')
    <script>
        window.pageConfig = {
            isReadOnly: false,
            canManageBerkas: false,
        };

        window.berkasConfig = {
            anakAsuhId: null,
            uploadUrl: null,
            deleteUrl: null,
            csrfToken: "{{ csrf_token() }}",
        };
    </script>
    @vite(['resources/js/pages/anak-asuh/index.js'])
@endpush
