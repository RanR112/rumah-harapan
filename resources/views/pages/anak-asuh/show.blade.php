@extends('layouts.dashboard')

@section('title', 'Detail Anak Asuh')

@section('content')
    <div class="anak-asuh-detail-page anak-asuh-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">DETAIL ANAK ASUH</h1>
                @if (request()->query('new') === 'true')
                    <p class="page-description">
                        Data anak asuh berhasil disimpan! Silakan tambahkan berkas anak di bawah form jika diperlukan.
                    </p>
                @else
                    <p class="page-description">
                        Halaman detail lengkap data anak asuh. Pada Halaman ini anda dapat mengunduh berkas yang tersedia di
                        bawah.
                    </p>
                @endif
            </div>
            {{-- onclick diisi oleh index.js via sessionStorage --}}
            <button type="button" class="btn-header-back" id="btnBack">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="anak-asuh-form-show">
                    @include('pages.anak-asuh.form', [
                        'anakAsuh' => $anakAsuh,
                        'asramas' => $asramas,
                        'isEdit' => true,
                        'isReadOnly' => true,
                        'canManageBerkas' => request()->query('new') === 'true',
                    ])

                    @if (request()->query('new') === 'true')
                        <div class="action-button-show">
                            <button type="button" class="btn-edit-show"
                                onclick="window.location.href='{{ route('anak-asuh.edit', $anakAsuh->id) }}'">
                                <i data-lucide="edit" class="btn-icon"></i>
                                <span>Edit Data</span>
                            </button>

                            <button type="button" class="btn-anak-asuh-delete btn-delete-show"
                                data-anak-asuh-id="{{ $anakAsuh->id }}" data-anak-asuh-name="{{ $anakAsuh->nama_anak }}">
                                <i data-lucide="trash-2" class="btn-icon"></i>
                                <span>Hapus Data</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form-{{ $anakAsuh->id }}" method="POST" action="{{ route('anak-asuh.destroy', $anakAsuh->id) }}"
        style="display: none;">
        @csrf
        @method('DELETE')
        {{-- Diisi oleh index.js dari page number di sessionStorage returnUrl --}}
        <input type="hidden" name="current_page" id="deleteCurrentPage" value="1">
    </form>

    @include('pages.anak-asuh.modal-upload-berkas')
    @include('components.alert-modal')
@endsection

@push('scripts')
    <script>
        window.berkasConfig = {
            anakAsuhId: {{ $anakAsuh->id }},
            uploadUrl: "{{ route('anak-asuh.upload-berkas', $anakAsuh->id) }}",
            deleteUrl: "{{ url('anak-asuh/' . $anakAsuh->id . '/berkas') }}",
            csrfToken: "{{ csrf_token() }}",
        };

        window.pageConfig = {
            isReadOnly: true,
            canManageBerkas: {{ request()->query('new') === 'true' ? 'true' : 'false' }},
        };
    </script>
    @vite(['resources/js/pages/anak-asuh/index.js'])
@endpush
