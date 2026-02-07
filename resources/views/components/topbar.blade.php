<header class="topbar">
    <!-- Hamburger Menu (Mobile Only) -->
    <button type="button" class="hamburger-menu" id="hamburgerMenu" aria-label="Toggle sidebar" aria-expanded="false">
        @include('components.icon-svg', ['name' => 'bars', 'class' => 'hamburger-icon'])
    </button>

    <!-- Profile Dropdown -->
    <div class="profile-dropdown js-dropdown" data-dropdown>
        <button type="button" class="profile-trigger" data-dropdown-trigger aria-haspopup="true" aria-expanded="false"
            aria-label="Open profile menu">
            <div class="profile-avatar-icon">
                @include('components.icon-svg', ['name' => 'circle-user', 'class' => 'user-icon'])
            </div>
            @include('components.icon-svg', ['name' => 'chevron-down', 'class' => 'profile-chevron'])
        </button>

        <div class="profile-menu" data-dropdown-menu role="menu">
            <div class="profile-header">
                <div class="profile-avatar-icon-large">
                    @include('components.icon-svg', ['name' => 'circle-user', 'class' => 'user-icon'])
                </div>
                <div class="profile-info">
                    @if (Auth::user())
                        <div class="profile-name">{{ Auth::user()->name }}</div>
                        @if (Auth::user()->role === 'admin')
                            <span class="role-badge role-badge--admin">Admin</span>
                        @elseif(Auth::user()->role === 'petugas')
                            <span class="role-badge role-badge--petugas">Petugas</span>
                        @else
                            <span class="role-badge role-badge--default">{{ ucfirst(Auth::user()->role) }}</span>
                        @endif
                        <div class="profile-email" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</div>
                    @else
                        <div class="profile-name">Guest User</div>
                        <span class="role-badge role-badge--guest">Guest</span>
                        <div class="profile-email" title="guest@example.com">guest@example.com</div>
                    @endif
                </div>
            </div>

            <!-- MENU EDIT PROFIL -->
            @auth
                <a href="{{ route('profile.edit') }}" class="profile-menu-item" role="menuitem">
                    @include('components.icon-svg', [
                        'name' => 'user-pen',
                        'class' => 'profile-menu-icon',
                    ])
                    Edit Profil
                </a>
            @endauth

            <form method="POST" action="{{ route('logout') }}" class="profile-logout-form">
                @csrf
                <button type="submit" class="profile-logout-btn" role="menuitem">
                    @include('components.icon-svg', [
                        'name' => 'right-to-bracket',
                        'class' => 'logout-icon',
                    ])
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    (function() {
        'use strict';

        // ==================== PROFILE DROPDOWN INITIALIZATION ====================
        function initProfileDropdown() {
            const dropdown = document.querySelector('.profile-dropdown[data-dropdown]');

            if (!dropdown || dropdown.dataset.initialized === 'true') return;

            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');
            const chevron = trigger.querySelector('.profile-chevron');

            if (!trigger || !menu) return;

            // Toggle dropdown
            const toggleDropdown = (show = null) => {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                const shouldShow = show !== null ? show : !isExpanded;

                trigger.setAttribute('aria-expanded', String(shouldShow));
                menu.classList.toggle('show', shouldShow);

                // Rotate chevron icon
                if (chevron) {
                    chevron.style.transform = shouldShow ? 'rotate(180deg)' : 'rotate(0deg)';
                    chevron.style.transition = 'transform 0.2s ease';
                }
            };

            // Event listeners
            const handleClick = (e) => {
                e.stopPropagation();
                toggleDropdown();
            };

            const handleOutsideClick = (e) => {
                if (!dropdown.contains(e.target)) {
                    toggleDropdown(false);
                }
            };

            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    toggleDropdown(false);
                }
            };

            trigger.addEventListener('click', handleClick);
            document.addEventListener('click', handleOutsideClick);
            document.addEventListener('keydown', handleEscape);

            // CLOSE DROPDOWN WHEN CLICKING ON PROFILE MENU ITEMS
            const profileMenuItems = dropdown.querySelectorAll('.profile-menu-item');
            profileMenuItems.forEach(item => {
                item.addEventListener('click', () => {
                    setTimeout(() => toggleDropdown(false), 100); // Delay untuk animasi
                });
            });

            // Mark as initialized
            dropdown.dataset.initialized = 'true';
        }

        // Run initialization when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfileDropdown);
        } else {
            initProfileDropdown();
        }
    })();
</script>