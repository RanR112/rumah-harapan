// Import Lucide Icons yang dibutuhkan
import {
    createIcons,
    Users,
    UserPlus,
    Building2,
    Clock,
    UserCog,
    LogIn,
    LogOut,
    KeyRound,
    Plus,
    ChevronRight,
    ChevronLeft,
    Save,
    Edit,
    Trash2,
    RotateCcw,
    X,
    ChevronDown,
    Download,
    Upload,
    Search,
    Funnel,
    Camera,
    FileText,
    CheckCircle,
    Calendar,
    Eye,
    Layers,
    Info,
    GitCompare,
    CircleAlert,
    SunMoon,
    User,
    ClockAlert,
    WifiOff,
} from "lucide";

// Helper untuk lazy load
const lazyLoad = (condition, importer) => {
    if (condition) {
        importer().catch((err) => console.error("Lazy load error:", err));
    }
};

window.lucide = window.lucide || {};
window.lucide.createIcons = () => {
    createIcons({
        icons: {
            Users,
            UserPlus,
            Building2,
            Clock,
            UserCog,
            LogIn,
            LogOut,
            KeyRound,
            Plus,
            ChevronRight,
            ChevronLeft,
            Save,
            Edit,
            Trash2,
            RotateCcw,
            X,
            ChevronDown,
            Download,
            Upload,
            Search,
            Funnel,
            Camera,
            FileText,
            CheckCircle,
            Calendar,
            Eye,
            Layers,
            Info,
            GitCompare,
            CircleAlert,
            SunMoon,
            User,
            ClockAlert,
            WifiOff,
        },
    });
};

document.addEventListener("DOMContentLoaded", () => {
    window.lucide.createIcons();

    // Lazy Load: OTP Timer
    lazyLoad(!!document.getElementById("countdown"), () =>
        import("./components/otp.js").then((m) => m.initOtpTimer()),
    );

    // Lazy Load: Button Loader
    lazyLoad(document.querySelector("form .btn-loader"), () =>
        import("./components/loader/button-loader.js").then((m) =>
            m.initButtonLoader(),
        ),
    );

    // Lazy Load: Sidebar Toggle
    lazyLoad(
        document.getElementById("sidebar") ||
            document.getElementById("hamburgerMenu"),
        async () => {
            const { default: SidebarToggle } =
                await import("./components/sidebar-toggle.js");
            new SidebarToggle();
        },
    );

    // Lazy Load: Page Transition + Initial Loader Hide (dashboard)
    // Digabung karena keduanya bergantung pada elemen yang sama
    lazyLoad(document.getElementById("dashboardTransitionLoader"), () =>
        import("./components/page-transition.js").then((module) => {
            module.PageTransition.init();
            hideInitialLoader();
        }),
    );

    // Lazy Load: Session Timeout (hanya di halaman dashboard)
    lazyLoad(document.getElementById("sessionTimeoutModal"), () =>
        import("./components/session-timeout.js").then((m) =>
            m.initSessionTimeout(),
        ),
    );
});

/**
 * Sembunyikan loader awal dashboard setelah halaman siap.
 * Dipindahkan dari inline script di dashboard.blade.php ke sini
 * agar tidak ada inline JS di layout.
 */
function hideInitialLoader() {
    const loader = document.getElementById("dashboardTransitionLoader");
    if (loader) {
        setTimeout(() => {
            loader.classList.remove("dashboard-transition-loader--active");
            setTimeout(() => {
                loader.remove();
            }, 200);
        }, 1500);
    }
}
