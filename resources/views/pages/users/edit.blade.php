@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
    <div class="users-form-page">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">EDIT USER</h1>
                <p class="page-description">
                    Perbarui data user di bawah ini.
                    Password hanya perlu diisi jika ingin mengubah password pengguna.
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
                <form method="POST" action="{{ route('users.update', $user->id) }}" class="user-form">
                    @csrf
                    @method('PUT')

                    {{-- Diisi oleh index.js dari page number di sessionStorage returnUrl --}}
                    <input type="hidden" name="current_page" id="currentPageInput" value="1">

                    @include('pages.users.form', ['user' => $user, 'isEdit' => true])

                    <div class="form-actions">
                        <button type="submit" class="btn-primary-form-user">
                            <span class="btn-text">Perbarui User</span>
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
        };
    </script>
    @vite(['resources/js/pages/users/index.js'])
@endpush
