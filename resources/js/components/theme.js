/**
 * theme.js
 * Utilitas untuk mengelola preferensi dark/light mode per user.
 *
 * Alur:
 * - Blade sudah set data-theme di <html> dari database saat server render
 * - localStorage dipakai sebagai backup untuk halaman auth (saat belum login)
 * - applyTheme()      → set data-theme di <html>
 * - saveTheme()       → AJAX PATCH /settings/theme → simpan ke DB + localStorage
 * - getCurrentTheme() → baca tema aktif dari DOM
 * - toggleTheme()     → shortcut toggle light ↔ dark
 *
 * Lokasi: resources/js/components/theme.js
 * Dipakai oleh: resources/js/pages/settings/index.js
 */

/**
 * Terapkan tema ke <html> element via data-theme attribute
 * @param {'light'|'dark'} theme
 */
export function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
}

/**
 * Dapatkan tema yang sedang aktif dari <html> data-theme attribute
 * @returns {'light'|'dark'}
 */
export function getCurrentTheme() {
    return document.documentElement.getAttribute("data-theme") || "light";
}

/**
 * Simpan preferensi tema ke database via AJAX.
 * Setelah berhasil, tema diterapkan ke DOM dan disync ke localStorage
 * agar halaman auth bisa membaca tema saat user logout.
 *
 * @param {'light'|'dark'} theme        - Tema yang ingin disimpan
 * @param {Function}       [onSuccess]  - Callback (theme) => void setelah berhasil
 * @param {Function}       [onError]    - Callback (error) => void jika gagal
 */
export function saveTheme(theme, onSuccess, onError) {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;

    if (!csrfToken) {
        console.error("[Theme] CSRF token tidak ditemukan.");
        return;
    }

    fetch("/settings/theme", {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: JSON.stringify({ theme }),
    })
        .then((res) => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then((data) => {
            if (data.success) {
                applyTheme(theme);
                // Sync ke localStorage agar halaman auth membaca tema terbaru
                localStorage.setItem("theme", theme);
                if (typeof onSuccess === "function") onSuccess(theme);
            } else {
                throw new Error(data.message || "Gagal menyimpan tema.");
            }
        })
        .catch((err) => {
            console.error("[Theme] Gagal menyimpan preferensi tema:", err);
            if (typeof onError === "function") onError(err);
        });
}

/**
 * Toggle tema antara light dan dark.
 * Shortcut dari getCurrentTheme() + saveTheme().
 *
 * @param {Function} [onSuccess] - Callback (theme) => void setelah berhasil
 * @param {Function} [onError]   - Callback (error) => void jika gagal
 */
export function toggleTheme(onSuccess, onError) {
    const next = getCurrentTheme() === "light" ? "dark" : "light";
    saveTheme(next, onSuccess, onError);
}
