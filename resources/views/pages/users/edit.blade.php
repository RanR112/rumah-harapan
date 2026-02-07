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
        {{-- Button Kembali di Header (Hanya tampil di ≤480px) --}}
        <button type="button" class="btn-header-back" onclick="window.location.href='{{ route('users.index') }}'">
            <i data-lucide="chevron-left" class="btn-icon"></i>
            <span>Kembali</span>
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="user-form">
                @csrf
                @method('PUT')
                @include('pages.users.form', ['user' => $user, 'isEdit' => true])
                
                <div class="form-actions">
                    {{-- Button Submit tanpa icon --}}
                    <button type="submit" class="btn-primary">
                        Perbarui User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection