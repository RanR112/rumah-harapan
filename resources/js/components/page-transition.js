/**
 * Page Transition Handler
 * Handles loading transitions ONLY for sidebar navigation (excluding logout)
 * Form submissions are NOT handled by this component
 */
export class PageTransition {
    // Konfigurasi waktu loading
    static get CONFIG() {
        return {
            TRANSITION_LOADING_TIME: 800,  // 0.8 detik untuk navigasi sidebar
            MINIMUM_LOADING_TIME: 500      // Minimum 0.5 detik
        };
    }

    constructor() {
        this.init();
    }

    init() {
        this.handleSidebarNavigation();
    }

    showLoader() {
        // Buat loader dengan struktur yang sama seperti komponen blade
        const loader = document.createElement('div');
        loader.id = 'transition-loader';
        loader.className = 'dashboard-transition-loader dashboard-transition-loader--active';
        loader.innerHTML = `
            <div class="dashboard-transition-loader__content">
                <div class="loader-main">
                    <div class="loader-main__spinner"></div>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    }

    hideLoader() {
        const loader = document.getElementById('transition-loader');
        if (loader) {
            loader.classList.remove('dashboard-transition-loader--active');
            setTimeout(() => {
                loader.remove();
            }, 200);
        }
    }

    handleSidebarNavigation() {
        // Hanya tangani link yang ada di sidebar
        document.addEventListener('click', (e) => {
            // Cari link yang ada di dalam sidebar
            const sidebarLink = e.target.closest('.sidebar .nav-link');
            if (!sidebarLink) return;

            const href = sidebarLink.getAttribute('href');
            if (!href || href.startsWith('#')) return;

            // Exclude logout link
            if (sidebarLink.querySelector('[data-lucide="log-out"]') || 
                sidebarLink.textContent?.includes('Logout')) {
                return;
            }

            e.preventDefault();
            
            // Tampilkan loader
            this.showLoader();
            
            // Redirect setelah delay kecil
            setTimeout(() => {
                window.location.href = href;
            }, 100);
        });
    }

    // Static method untuk penggunaan manual (jika dibutuhkan)
    static show() {
        const instance = new PageTransition();
        instance.showLoader();
    }

    static hide() {
        const instance = new PageTransition();
        instance.hideLoader();
    }

    // Static method untuk inisialisasi
    static init() {
        return new PageTransition();
    }
}