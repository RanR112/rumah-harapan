@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('content')
    <div class="users-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">TAMBAH USER</h1>
                <p class="page-description">
                    Isi form di bawah ini untuk menambah user baru ke sistem.
                    Pastikan data yang dimasukkan valid dan lengkap.
                </p>
            </div>
            <button type="button" class="btn-header-back" onclick="window.location.href='{{ route('users.index') }}'">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}" class="user-form">
                    @csrf
                    @include('pages.users.form', ['isEdit' => false])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-user">
                            <span class="btn-text">Simpan User</span>
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
            window.userData = {
                csrfToken: '{{ csrf_token() }}',
                baseUrl: '{{ url('/') }}'
            };
        </script>
        
        @vite(['resources/js/pages/users/index.js'])
    @endpush
@endsection
