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
        <!-- Card 1: Total Users -->
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

        <!-- Card 2: Total Children -->
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

        <!-- Card 3: Total Cabang -->
        <div class="card">
            <h3 class="card-title">Total Cabang</h3>
            <div class="card-main">
                <div class="card-icon card-icon--cabang">
                    <i data-lucide="building-2" class="card-icon-lucide"></i>
                </div>
                <p class="card-value">{{ $totalCabang }}</p>
            </div>
            <p class="card-description">Cabang rumah harapan</p>
        </div>

        <!-- Card 4: Recent Activity -->
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
    </div>

    <!-- Recent Activity Section -->
    <div class="recent-activity-section">
        <div class="section-header">
            <h3 class="section-title">Aktivitas Terbaru</h3>
            <a href="#" class="view-all-link">Lihat Semua</a>
        </div>
        
        <div class="activity-list">
            @forelse($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-icon activity-icon--{{ $activity->category ?? 'default' }}">
                    @php
                        $icon = match($activity->category ?? 'default') {
                            'anak_asuh' => 'user-plus',
                            'user' => 'users',
                            'cabang' => 'building-2',
                            'document' => 'file-text',
                            default => 'clock'
                        };
                    @endphp
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
</div>
@endsection