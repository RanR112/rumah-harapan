/**
 * Sidebar Toggle Component
 * Handles hamburger menu and sidebar toggle functionality with click outside closing
 */
export default class SidebarToggle {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.hamburgerMenu = document.getElementById('hamburgerMenu');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.isSidebarOpen = false;
        
        if (this.sidebar && this.hamburgerMenu) {
            this.init();
        }
    }

    init() {
        // Hamburger menu click
        this.hamburgerMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleSidebar();
        });

        // Overlay click (closes sidebar)
        if (this.sidebarOverlay) {
            this.sidebarOverlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        // Click outside sidebar (for mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && this.isSidebarOpen) {
                const sidebar = this.sidebar;
                const hamburger = this.hamburgerMenu;
                
                // Cek apakah klik di luar sidebar dan hamburger
                if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
                    this.closeSidebar();
                }
            }
        });

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1025) {
                this.closeSidebar();
            }
        });

        // Initialize Lucide icons
        this.initLucideIcons();
    }

    toggleSidebar() {
        if (this.isSidebarOpen) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    }

    openSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.add('sidebar--open');
            this.sidebar.setAttribute('aria-hidden', 'false');
        }
        if (this.sidebarOverlay) {
            this.sidebarOverlay.classList.add('sidebar-overlay--active');
        }
        this.isSidebarOpen = true;
        this.hamburgerMenu.setAttribute('aria-expanded', 'true');
        
        // Change icon to X
        const icon = this.hamburgerMenu.querySelector('i');
        if (icon) {
            icon.setAttribute('data-lucide', 'x');
            window.lucide.createIcons();
        }
    }

    closeSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.remove('sidebar--open');
            this.sidebar.setAttribute('aria-hidden', 'true');
        }
        if (this.sidebarOverlay) {
            this.sidebarOverlay.classList.remove('sidebar-overlay--active');
        }
        this.isSidebarOpen = false;
        this.hamburgerMenu.setAttribute('aria-expanded', 'false');
        
        // Change icon back to menu
        const icon = this.hamburgerMenu.querySelector('i');
        if (icon) {
            icon.setAttribute('data-lucide', 'menu');
            window.lucide.createIcons();
        }
    }

    initLucideIcons() {
        // Ensure Lucide is available
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
}
