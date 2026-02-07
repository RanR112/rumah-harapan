{{-- Loader transition dengan state active dari awal --}}
<div class="dashboard-transition-loader dashboard-transition-loader--active" id="dashboardTransitionLoader">
    <div class="dashboard-transition-loader__content">
        {{-- Gunakan loader-main di dalam loader-transition --}}
        @include('components.loader.loader-main')
    </div>
</div>