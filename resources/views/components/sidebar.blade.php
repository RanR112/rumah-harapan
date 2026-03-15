<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="{{ asset('images/Logo.svg') }}" alt="{{ config('app.name') }}">
        </div>
        <h3 class="sidebar-title">YAYASAN RUMAH HARAPAN</h3>
        <div class="sidebar-divider"></div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    @include('components.icon-svg', ['name' => 'house', 'class' => 'nav-icon'])
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @if (Auth::check())
                {{-- Anak Asuh — admin & petugas --}}
                @if (in_array(Auth::user()->role, ['admin', 'petugas']))
                    <li class="nav-item {{ request()->routeIs('anak-asuh*') ? 'active' : '' }}">
                        <a href="{{ route('anak-asuh.index') }}" class="nav-link">
                            @include('components.icon-svg', ['name' => 'users', 'class' => 'nav-icon'])
                            <span class="nav-text">Data Anak Asuh</span>
                        </a>
                    </li>
                @endif

                {{-- Asrama — admin & petugas (petugas: read-only via index) --}}
                @if (in_array(Auth::user()->role, ['admin', 'petugas']))
                    <li class="nav-item {{ request()->routeIs('rumah-harapan*') ? 'active' : '' }}">
                        <a href="{{ route('rumah-harapan.index') }}" class="nav-link">
                            @include('components.icon-svg', ['name' => 'building', 'class' => 'nav-icon'])
                            <span class="nav-text">Asrama</span>
                        </a>
                    </li>
                @endif

                {{-- Manajemen User — admin only --}}
                @if (Auth::user()->role === 'admin')
                    <li class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            @include('components.icon-svg', [
                                'name' => 'user-group',
                                'class' => 'nav-icon',
                            ])
                            <span class="nav-text">Manajemen User</span>
                        </a>
                    </li>

                    {{-- Aktivitas Sistem — admin only --}}
                    <li class="nav-item {{ request()->routeIs('audit-log*') ? 'active' : '' }}">
                        <a href="{{ route('audit-log.index') }}" class="nav-link">
                            @include('components.icon-svg', [
                                'name' => 'clipboard-list',
                                'class' => 'nav-icon',
                            ])
                            <span class="nav-text">Aktivitas Sistem</span>
                        </a>
                    </li>
                @endif

                {{-- Settings — admin & petugas --}}
                <li class="nav-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}" class="nav-link">
                        @include('components.icon-svg', ['name' => 'gear', 'class' => 'nav-icon'])
                        <span class="nav-text">Pengaturan</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                @include('components.icon-svg', [
                    'name' => 'right-to-bracket',
                    'class' => 'nav-icon-logout',
                ])
                <span class="btn-text">Keluar</span>
                <div class="btn-loader" style="display: none;">
                    @include('components.loader.loader-pulse')
                </div>
            </button>
        </form>
    </div>
</aside>
