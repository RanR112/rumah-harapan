@extends('layouts.dashboard')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
    <div class="audit-logs-index">
        <div class="page-header">
            <div class="page-header-content">
                <h2 class="page-title">AKTIVITAS SISTEM</h2>
                <p class="page-description">
                    Riwayat aktivitas sistem untuk monitoring dan keamanan.
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="audit-logs-filters">
            <div class="filter-row">
                <!-- Search Input -->
                <div class="search-container">
                    <div class="search-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Cari user, model, atau aksi..." aria-label="Cari audit log">
                        <div class="search-loader" id="searchLoader">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                <!-- FILTERS WRAPPER (DITAMBAHKAN) -->
                <div class="filters-wrapper">
                    <!-- RESET FILTER BUTTON (di dalam filters-wrapper) -->
                    <button type="button" id="resetFilterBtn" class="reset-filter-btn" title="Reset model dan aksi filter"
                        style="display: none;">
                        Reset
                    </button>

                    <!-- Model Type Filter - CUSTOM DROPDOWN -->
                    <div class="audit-logs-model-filter">
                        <div class="custom-dropdown" id="modelTypeDropdown">
                            <button type="button" class="dropdown-trigger">
                                <span class="selected-text">Semua Model</span>
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($modelTypes as $modelType)
                                    <div class="dropdown-item" data-value="{{ $modelType['value'] }}">
                                        {{ $modelType['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" id="modelTypeFilterValue" name="model_type"
                            value="{{ request('model_type') }}">
                    </div>

                    <!-- Action Filter - CUSTOM DROPDOWN -->
                    <div class="audit-logs-action-filter">
                        <div class="custom-dropdown" id="actionDropdown">
                            <button type="button" class="dropdown-trigger">
                                <span class="selected-text">Semua Aksi</span>
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($allActions as $action)
                                    <div class="dropdown-item" data-value="{{ $action['value'] }}">
                                        {{ $action['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" id="actionFilterValue" name="action" value="{{ request('action') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="data-table audit-logs-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Model</th>
                            <th>Aksi</th>
                            <th>Tanggal & Waktu</th>
                            <th class="text-center action-column">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="auditLogsTableBody">
                        <tr class="table-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="loading-spinner">
                                    <div class="spinner"></div>
                                    <p class="mt-2">Memuat data audit logs...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Container -->
        <div class="pagination-container" id="paginationContainer"></div>
    </div>

    @push('scripts')
        <script>
            window.userData = {
                csrfToken: '{{ csrf_token() }}',
                baseUrl: '{{ url('/') }}',
                initialFilters: {
                    search: '{{ request('search') }}',
                    model_type: '{{ request('model_type') }}',
                    action: '{{ request('action') }}'
                },
                modelActionMap: {!! json_encode($modelActionMap) !!}
            };
        </script>
        @vite(['resources/js/pages/audit-log/index.js'])
    @endpush
@endsection
