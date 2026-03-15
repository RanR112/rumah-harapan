@extends('layouts.dashboard')

@section('title', 'Edit Anak Asuh')
@section('page-title', 'Edit Anak Asuh')

@section('content')
    <div class="anak-asuh-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">EDIT ANAK ASUH</h1>
                <p class="page-description">
                    Perbarui data anak asuh di bawah ini.
                    Semua field wajib diisi kecuali yang bersifat opsional.
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
                <form method="POST" action="{{ route('anak-asuh.update', $anakAsuh->id) }}" class="anak-asuh-form"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Diisi oleh index.js dari page number di sessionStorage returnUrl --}}
                    <input type="hidden" name="current_page" id="currentPageInput" value="1">

                    @include('pages.anak-asuh.form', [
                        'anakAsuh' => $anakAsuh,
                        'asramas' => $asramas,
                        'isEdit' => true,
                        'isReadOnly' => false,
                    ])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-anak-asuh">
                            <span class="btn-text">Perbarui Anak Asuh</span>
                            <div class="btn-loader" style="display: none">
                                @include('components.loader.loader-pulse')
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('pages.anak-asuh.modal-upload-berkas')
    @include('components.alert-modal')
@endsection

@push('scripts')
    <script>
        window.pageConfig = {
            isReadOnly: false,
            canManageBerkas: true,
        };

        window.berkasConfig = {
            anakAsuhId: {{ $anakAsuh->id }},
            uploadUrl: "{{ route('anak-asuh.upload-berkas', $anakAsuh->id) }}",
            deleteUrl: "{{ url('anak-asuh/' . $anakAsuh->id . '/berkas') }}",
            csrfToken: "{{ csrf_token() }}",
        };
    </script>
    @vite(['resources/js/pages/anak-asuh/index.js'])
@endpush
