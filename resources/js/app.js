// Import Lucide Icons yang dibutuhkan
import {
    createIcons,
    Users,
    UserPlus,
    Building2,
    Clock,
    Plus,
    ChevronRight,
    ChevronLeft,
    Save,
    Eye,
    EyeOff,
    Edit,
    Trash2,
    RotateCcw,
    X,
    ChevronDown,
    Download,
    Upload,
    Search,
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
            Plus,
            ChevronRight,
            ChevronLeft,
            Save,
            Eye,
            EyeOff,
            Edit,
            Trash2,
            RotateCcw,
            X,
            ChevronDown,
            Download,
            Upload,
            Search,
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

    // Lazy Load: Page Transition (untuk dashboard)
    lazyLoad(document.getElementById("dashboardTransitionLoader"), () =>
        import("./components/page-transition.js").then((module) => {
            module.PageTransition.init();
        }),
    );
});
