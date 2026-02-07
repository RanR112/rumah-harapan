@extends('layouts.dashboard')

@section('title', 'Tambah Anak Asuh')
@section('page-title', 'Tambah Anak Asuh')

@push('styles')
    @vite(['resources/sass/app.scss'])
@endpush

@section('content')
    <div class="anak-asuh-create">
        <div class="page-header">
            <h2 class="page-title">Tambah Data Anak Asuh</h2>
            <a href="{{ route('anak-asuh.index') }}" class="btn-secondary">
                <i data-lucide="arrow-left" class="btn-icon"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="card card--form">
            <form method="POST" action="{{ route('anak-asuh.store') }}" class="form-container" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required
                            class="form-input @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                            placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required
                            class="form-input @error('tanggal_lahir') is-invalid @enderror"
                            value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                            class="form-input @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}"
                            placeholder="Masukkan tempat lahir">
                        @error('tempat_lahir')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="form-input @error('alamat') is-invalid @enderror"
                        placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="foto" class="form-label">Foto (Opsional)</label>
                    <input type="file" id="foto" name="foto" accept="image/*"
                        class="form-file @error('foto') is-invalid @enderror">
                    @error('foto')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="btn-icon"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
