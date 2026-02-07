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
            
            @if(Auth::check())
                @if(Auth::user()->role === 'admin')
                    <li class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            @include('components.icon-svg', ['name' => 'user-group', 'class' => 'nav-icon'])
                            <span class="nav-text">Manajemen User</span>
                        </a>
                    </li>
                @endif

                @if(in_array(Auth::user()->role, ['admin', 'petugas']))
                    <li class="nav-item {{ request()->routeIs('anak-asuh*') ? 'active' : '' }}">
                        <a href="{{ route('anak-asuh.index') }}" class="nav-link">
                            @include('components.icon-svg', ['name' => 'users', 'class' => 'nav-icon'])
                            <span class="nav-text">Data Anak Asuh</span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                @include('components.icon-svg', ['name' => 'right-to-bracket', 'class' => 'nav-icon'])
                <span class="nav-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>