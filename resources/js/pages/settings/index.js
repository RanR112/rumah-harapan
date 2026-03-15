/**
 * settings.js
 * Mengelola interaksi halaman pengaturan sistem.
 * Hanya diload di halaman settings — tidak global.
 */

import { saveTheme } from "../../components/theme.js";

document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("themeToggle");
    const label = document.getElementById("themeLabel");

    if (!toggle || !label) return;

    toggle.addEventListener("change", () => {
        const next = toggle.checked ? "dark" : "light";

        // Disable sementara agar tidak double-click saat saving
        toggle.disabled = true;

        saveTheme(
            next,
            (theme) => {
                label.textContent =
                    theme === "dark" ? "Mode Gelap" : "Mode Terang";
                toggle.disabled = false;
            },
            () => {
                // Rollback jika gagal
                toggle.checked = !toggle.checked;
                toggle.disabled = false;
            },
        );
    });
});
