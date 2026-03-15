@extends('layouts.dashboard')

@section('title', 'Detail Aktivitas Sistem')
@section('page-title', 'Detail Aktivitas Sistem')

@section('content')
    <div class="audit-log-show">
        <!-- Header Section -->
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">DETAIL AKTIVITAS SISTEM</h1>
                <p class="page-description">
                    Informasi lengkap aktivitas sistem untuk keperluan audit dan monitoring.
                </p>
            </div>
            {{-- onclick diisi via inline script di bawah dari sessionStorage --}}
            <button type="button" class="btn-back-show-audit" id="btnBack">
                <i data-lucide="chevron-left" class="btn-icon"></i>
                <span>Kembali</span>
            </button>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Basic Information Card -->
            <div class="card card-info">
                <div class="card-header">
                    <div class="card-icon">
                        <i data-lucide="info" class="icon"></i>
                    </div>
                    <h2 class="card-title">Informasi Dasar</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-row">
                            <label class="info-label">User</label>
                            <div class="info-value user-info">
                                <span>{{ $log->user?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <label class="info-label">Model</label>
                            <div class="info-value model-info">
                                <span>{{ $log->human_readable_model }}</span>
                            </div>
                        </div>

                        @if ($log->model_type === 'App\Models\AnakAsuh' && $log->action === 'updated')
                            @php
                                $namaAnakAsuh =
                                    $log->new_values['nama_anak'] ?? ($log->old_values['nama_anak'] ?? null);
                            @endphp
                            @if ($namaAnakAsuh)
                                <div class="info-row">
                                    <label class="info-label">Nama Anak Asuh</label>
                                    <div class="info-value">
                                        <span>{{ $namaAnakAsuh }}</span>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="info-row">
                            <label class="info-label">Aksi</label>
                            <div class="info-value action-info">
                                @php
                                    $actionClass = match ($log->action) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted', 'restored' => 'danger',
                                        'login' => 'primary',
                                        'logout' => 'secondary',
                                        default => 'info',
                                    };
                                @endphp
                                <span class="badge badge-{{ $actionClass }}">
                                    {{ $log->human_readable_action }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <label class="info-label">Waktu</label>
                            <div class="info-value time-info">
                                <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span><br>
                                <span class="time-ago">({{ $log->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Changes Section (CRUD Actions) -->
            @if (in_array($log->action, ['created', 'updated', 'deleted', 'restored']) && count($log->formatted_changes) > 0)
                <div class="card card-changes">
                    <div class="card-header">
                        <div class="card-icon">
                            <i data-lucide="git-compare" class="icon"></i>
                        </div>
                        <h2 class="card-title">Perubahan Data</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="changes-table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th class="old-value">Sebelum Diubah</th>
                                        <th class="new-value">Setelah Diubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($log->formatted_changes as $change)
                                        <tr class="{{ isset($change['is_password_change']) ? 'password-change' : '' }}">
                                            <td class="field-name">
                                                <strong>{{ $change['field'] }}</strong>
                                            </td>
                                            <td class="old-value">
                                                @if (isset($change['is_password_change']))
                                                    <span class="password-mask">********</span>
                                                @else
                                                    {{ $change['old'] ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="new-value">
                                                @if (isset($change['is_password_change']))
                                                    <span class="password-mask">******** <span
                                                            class="password-indicator">(Diubah)</span></span>
                                                @else
                                                    {{ $change['new'] ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Data Section (Custom Actions) -->
            @if (!in_array($log->action, ['created', 'updated', 'deleted', 'restored']) && !empty($log->new_values))
                <div class="card card-additional">
                    <div class="card-header">
                        <div class="card-icon">
                            <i data-lucide="file-text" class="icon"></i>
                        </div>
                        <h2 class="card-title">Detail Tambahan</h2>
                    </div>
                    <div class="card-body">
                        <div class="additional-grid">
                            @foreach ($log->new_values as $key => $value)
                                <div class="additional-item">
                                    <label class="additional-label">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </label>
                                    <div class="additional-value">
                                        @if (is_array($value))
                                            <div class="json-viewer">
                                                <pre>{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @elseif (is_bool($value))
                                            <span class="boolean-value {{ $value ? 'true' : 'false' }}">
                                                {{ $value ? 'Ya' : 'Tidak' }}
                                            </span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Empty State (No Changes) -->
            @if (in_array($log->action, ['created', 'updated', 'deleted', 'restored']) && count($log->formatted_changes) === 0)
                <div class="card card-empty">
                    <div class="card-body">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i data-lucide="info" class="icon"></i>
                            </div>
                            <h3 class="empty-title">Tidak Ada Perubahan Data</h3>
                            <p class="empty-text">
                                Aktivitas ini tidak mencatat perubahan data spesifik.
                                Informasi dasar telah ditampilkan di bagian atas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Init Lucide icons
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            // Restore tombol kembali dari sessionStorage
            // Jika ada returnUrl → kembali ke page & filter yang sama
            // Jika tidak ada → fallback ke index
            const returnUrl = sessionStorage.getItem('auditLog_returnUrl');
            const btnBack = document.getElementById('btnBack');
            if (btnBack) {
                btnBack.onclick = function() {
                    window.location.href = returnUrl || '{{ route('audit-log.index') }}';
                };
            }
        });
    </script>
@endpush
