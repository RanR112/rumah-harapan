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
        {{-- Button Kembali di Header (Hanya tampil di ≤480px) --}}
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
                    {{-- Button Submit tanpa icon --}}
                    <button type="submit" class="btn-primary">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection