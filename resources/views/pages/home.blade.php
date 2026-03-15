@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@push('styles')
    @vite(['resources/sass/app.scss'])
@endpush

@section('content')
    <div class="dashboard-home">
        <div class="welcome-section">
            <h2 class="welcome-title">
                <span class="welcome-greeting">Selamat Datang,</span>
                <strong class="welcome-name">{{ Auth::user()->name }}</strong>
            </h2>
            <p class="welcome-subtitle">Di Website Manajemen Kelola Data Anak Asuh YAYASAN RUMAH HARAPAN.</p>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">

            {{-- Card Total Pengguna — admin only --}}
            @if (Auth::user()->role === 'admin')
                <div class="card">
                    <h3 class="card-title">Total Pengguna</h3>
                    <div class="card-main">
                        <div class="card-icon card-icon--users">
                            <i data-lucide="users" class="card-icon-lucide"></i>
                        </div>
                        <p class="card-value">{{ $totalUsers }}</p>
                    </div>
                    <p class="card-description">Pengguna aktif sistem</p>
                </div>
            @endif

            {{-- Card Total Anak Asuh — admin & petugas --}}
            <div class="card">
                <h3 class="card-title">Total Anak Asuh</h3>
                <div class="card-main">
                    <div class="card-icon card-icon--children">
                        <i data-lucide="user-plus" class="card-icon-lucide"></i>
                    </div>
                    <p class="card-value">{{ $totalAnakAsuh }}</p>
                </div>
                <p class="card-description">Data anak asuh terdaftar</p>
            </div>

            {{-- Card Total Asrama — admin & petugas --}}
            <div class="card">
                <h3 class="card-title">Total Asrama</h3>
                <div class="card-main">
                    <div class="card-icon card-icon--asrama">
                        <i data-lucide="building-2" class="card-icon-lucide"></i>
                    </div>
                    <p class="card-value">{{ $totalAsrama }}</p>
                </div>
                <p class="card-description">Asrama rumah harapan terdaftar</p>
            </div>

            {{-- Card Aktivitas Terbaru — admin only --}}
            @if (Auth::user()->role === 'admin')
                <div class="card">
                    <h3 class="card-title">Aktivitas Terbaru</h3>
                    <div class="card-main">
                        <div class="card-icon card-icon--activity">
                            <i data-lucide="clock" class="card-icon-lucide"></i>
                        </div>
                        <p class="card-value">{{ $totalActivity }}</p>
                    </div>
                    <p class="card-description">Update dalam 24 jam terakhir</p>
                </div>
            @endif

        </div>

        {{-- Quick Access Section — petugas only --}}
        @if (Auth::user()->role === 'petugas')
            <div class="quick-access-section">
                <div class="section-header">
                    <h3 class="section-title">Akses Cepat</h3>
                </div>
                <div class="quick-access-grid">

                    <a href="{{ route('anak-asuh.index') }}" class="quick-access-card">
                        <div class="quick-access-card__icon card-icon--children">
                            <i data-lucide="user-plus" class="quick-access-card__icon-lucide"></i>
                        </div>
                        <div class="quick-access-card__content">
                            <span class="quick-access-card__title">Data Anak Asuh</span>
                            <span class="quick-access-card__subtitle">Kelola data anak asuh terdaftar</span>
                        </div>
                        <i data-lucide="chevron-right" class="quick-access-card__chevron"></i>
                    </a>

                    <a href="{{ route('rumah-harapan.index') }}" class="quick-access-card">
                        <div class="quick-access-card__icon card-icon--asrama">
                            <i data-lucide="building-2" class="quick-access-card__icon-lucide"></i>
                        </div>
                        <div class="quick-access-card__content">
                            <span class="quick-access-card__title">Data Asrama</span>
                            <span class="quick-access-card__subtitle">Lihat informasi asrama rumah harapan</span>
                        </div>
                        <i data-lucide="chevron-right" class="quick-access-card__chevron"></i>
                    </a>

                </div>
            </div>
        @endif

        {{-- Recent Activity Section — admin only --}}
        @if (Auth::user()->role === 'admin')
            <div class="recent-activity-section">
                <div class="section-header">
                    <h3 class="section-title">Aktivitas Terbaru</h3>
                    <a href="{{ route('audit-log.index') }}" class="view-all-link">Lihat Semua</a>
                </div>

                <div class="activity-list">
                    @forelse($recentActivities as $activity)
                        @php
                            $category = $activity->category;
                            $icon = match ($category) {
                                'anak_asuh' => 'user-plus',
                                'asrama' => 'building-2',
                                'user' => 'users',
                                'profil' => 'user-cog',
                                'import' => 'upload',
                                'export' => 'download',
                                'login' => 'log-in',
                                'logout' => 'log-out',
                                'reset_password' => 'key-round',
                                default => 'clock',
                            };
                        @endphp
                        <div class="activity-item">
                            <div class="activity-icon activity-icon--{{ $category }}">
                                <i data-lucide="{{ $icon }}" class="activity-icon-lucide"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">{!! $activity->description !!}</p>
                                <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item activity-item--empty">
                            <p class="activity-empty-text">Tidak ada aktivitas terbaru saat ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
@endsection
