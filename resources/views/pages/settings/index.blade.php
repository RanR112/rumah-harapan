@extends('layouts.dashboard')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="settings-page">

        {{-- Page Header --}}
        <div class="settings-header">
            <div class="settings-header__text">
                <h1 class="settings-header__title">PENGATURAN SISTEM</h1>
            </div>
        </div>

        <div class="settings-grid">

            {{-- Card: Info Akun --}}
            <div class="settings-card settings-card--account">
                <div class="settings-card__header">
                    <div class="settings-card__header-icon settings-card__header-icon--account">
                        <i data-lucide="user"></i>
                    </div>
                    <h2 class="settings-card__title">Informasi Akun</h2>
                    {{-- Button di kanan atas — disembunyikan di mobile, muncul di desktop --}}
                    <a href="{{ route('profile.edit') }}" class="btn-edit-profile btn-edit-profile--header">
                        <i data-lucide="edit"></i>
                        <span>Edit Profil</span>
                    </a>
                </div>

                <div class="settings-card__body">
                    <div class="account-info">
                        <div class="account-info__details">
                            <div class="account-info__item">
                                <span class="account-info__label">Nama</span>
                                <span class="account-info__value">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="account-info__item">
                                <span class="account-info__label">Email</span>
                                <span class="account-info__value">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="account-info__item">
                                <span class="account-info__label">No. Telepon</span>
                                <span class="account-info__value">
                                    {{ auth()->user()->phone ?? '-' }}
                                </span>
                            </div>
                            <div class="account-info__item">
                                <span class="account-info__label">Role</span>
                                <span class="account-info__badge account-info__badge--{{ auth()->user()->role }}">
                                    {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Petugas' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer button — hanya tampil di mobile (480px ke bawah) --}}
                <div class="settings-card__footer settings-card__footer--mobile">
                    <a href="{{ route('profile.edit') }}" class="btn-edit-profile">
                        <i data-lucide="edit"></i>
                        <span>Edit Profil</span>
                    </a>
                </div>
            </div>

            {{-- Card: Tampilan --}}
            <div class="settings-card settings-card--appearance">
                <div class="settings-card__header">
                    <div class="settings-card__header-icon settings-card__header-icon--appearance">
                        <i data-lucide="sun-moon"></i>
                    </div>
                    <h2 class="settings-card__title">Tampilan</h2>
                </div>

                <div class="settings-card__body">
                    <div class="appearance-row">
                        <span class="appearance-row__label" id="themeLabel">
                            {{ auth()->user()->theme === 'dark' ? 'Mode Gelap' : 'Mode Terang' }}
                        </span>
                        <label class="theme-switch" aria-label="Toggle dark mode">
                            <input type="checkbox" id="themeToggle" class="theme-switch__input"
                                {{ auth()->user()->theme === 'dark' ? 'checked' : '' }}>
                            <span class="theme-switch__track">
                                <span class="theme-switch__thumb"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Card: Tentang Sistem --}}
            <div class="settings-card settings-card--about">
                <div class="settings-card__header">
                    <div class="settings-card__header-icon settings-card__header-icon--about">
                        <i data-lucide="info"></i>
                    </div>
                    <h2 class="settings-card__title">Tentang Sistem</h2>
                </div>

                <div class="settings-card__body">
                    <div class="about-list">
                        <div class="about-list__item">
                            <span class="about-list__label">Nama Aplikasi</span>
                            <span class="about-list__value">{{ config('app.name') }}</span>
                        </div>
                        <div class="about-list__item">
                            <span class="about-list__label">Versi</span>
                            <span class="about-list__value">
                                <span class="about-list__badge">v{{ config('app.version', '1.0.0') }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/settings/index.js'])
@endpush
