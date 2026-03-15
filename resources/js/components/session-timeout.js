/**
 * session-timeout.js
 * Mengelola deteksi inaktivitas dan auto logout.
 *
 * Alur:
 * - User tidak ada aktivitas 20 menit → modal peringatan muncul
 * - Modal menampilkan countdown 2 menit
 * - Jika user klik "Tetap Masuk" → ping /health
 *     → Ping sukses          : reset timer, tutup modal
 *     → Ping gagal (network) : tampilkan pesan koneksi, jangan logout
 *     → Ping gagal (401/419) : sesi expired di server, logout
 * - Jika user klik "Keluar" → fetch CSRF token → POST /logout → audit log tercatat
 * - Jika countdown habis → fetch CSRF token → POST /logout → audit log tercatat
 *     → Jika POST gagal (session sudah expired) → redirect /login langsung
 * - Setiap aktivitas user sebelum modal muncul → reset timer
 * - Saat modal sudah muncul → semua activity events di-pause
 * - Saat tab tidak aktif → timer tetap berjalan via Date.now()
 * - Saat tab kembali aktif → cek real elapsed time via visibilitychange
 *
 * Lokasi: resources/js/components/session-timeout.js
 */

const INACTIVE_DURATION = 20 * 60 * 1000; // 20 menit dalam ms
const COUNTDOWN_DURATION = 2 * 60; // 2 menit dalam detik

let inactivityTimer = null;
let countdownTimer = null;
let isModalVisible = false;

// Timestamp aktivitas terakhir — referensi real time, tidak terpengaruh throttling
let lastActivityTime = Date.now();

// Timestamp kapan countdown berakhir — untuk akurasi saat tab kembali aktif
let countdownEndTime = null;

// Elemen DOM
let modal = null;
let countdownEl = null;
let stayBtn = null;
let logoutBtn = null;
let networkMsgEl = null;

// Elemen loader di dalam tombol
let stayBtnText = null;
let stayBtnLoader = null;
let logoutBtnText = null;
let logoutBtnLoader = null;

// Simpan referensi handler agar bisa di-remove
let activityHandler = null;
const activityEvents = [
    "mousemove",
    "mousedown",
    "keydown",
    "scroll",
    "touchstart",
    "click",
];

/**
 * Format detik menjadi MM:SS
 */
function formatTime(seconds) {
    const m = String(Math.floor(seconds / 60)).padStart(2, "0");
    const s = String(seconds % 60).padStart(2, "0");
    return `${m}:${s}`;
}

/**
 * Tampilkan / sembunyikan loader pada tombol
 */
function setButtonLoading(btn, loading) {
    if (btn === "stay") {
        stayBtnText.style.display = loading ? "none" : "";
        stayBtnLoader.style.display = loading ? "flex" : "none";
        stayBtn.disabled = loading;
    } else {
        logoutBtnText.style.display = loading ? "none" : "";
        logoutBtnLoader.style.display = loading ? "flex" : "none";
        logoutBtn.disabled = loading;
    }
}

/**
 * Reset tombol "Masuk" ke state awal
 */
function resetStayButton() {
    stayBtnText.textContent = "Tetap Masuk";
    stayBtnText.style.display = "";
    stayBtnLoader.style.display = "none";
    stayBtn.disabled = false;
}

/**
 * Pasang activity event listeners — aktif saat modal tidak tampil
 */
function attachActivityListeners() {
    activityEvents.forEach((event) => {
        document.addEventListener(event, activityHandler, { passive: true });
    });
}

/**
 * Lepas activity event listeners — dipanggil saat modal muncul
 */
function detachActivityListeners() {
    activityEvents.forEach((event) => {
        document.removeEventListener(event, activityHandler);
    });
}

/**
 * Tampilkan modal peringatan dan mulai countdown berbasis timestamp
 */
function showModal() {
    if (isModalVisible) return;

    isModalVisible = true;
    countdownEndTime = Date.now() + COUNTDOWN_DURATION * 1000;

    // Pause activity listeners saat modal tampil
    detachActivityListeners();

    hideNetworkMessage();
    resetStayButton();

    modal.setAttribute("aria-hidden", "false");
    modal.classList.add("session-timeout-modal--visible");

    // Tampilkan sisa waktu awal
    countdownEl.textContent = formatTime(COUNTDOWN_DURATION);

    // Countdown berbasis timestamp — akurat saat tab kembali aktif
    countdownTimer = setInterval(() => {
        const remaining = Math.max(
            0,
            Math.round((countdownEndTime - Date.now()) / 1000),
        );
        countdownEl.textContent = formatTime(remaining);

        if (remaining <= 0) {
            clearInterval(countdownTimer);
            doLogout(true);
        }
    }, 1000);
}

/**
 * Sembunyikan modal dan bersihkan countdown
 */
function hideModal() {
    isModalVisible = false;
    countdownEndTime = null;
    modal.setAttribute("aria-hidden", "true");
    modal.classList.remove("session-timeout-modal--visible");
    clearInterval(countdownTimer);
    hideNetworkMessage();
    resetStayButton();
}

/**
 * Tampilkan pesan koneksi terputus
 */
function showNetworkMessage() {
    networkMsgEl.classList.add("session-timeout-modal__network--visible");
    stayBtnText.textContent = "Coba Lagi";
    stayBtnText.style.display = "";
    stayBtnLoader.style.display = "none";
    stayBtn.disabled = false;
}

/**
 * Sembunyikan pesan koneksi terputus
 */
function hideNetworkMessage() {
    networkMsgEl.classList.remove("session-timeout-modal__network--visible");
}

/**
 * Reset timer inaktivitas — catat timestamp real time aktivitas terakhir
 */
function resetTimer() {
    lastActivityTime = Date.now();
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(showModal, INACTIVE_DURATION);
}

/**
 * Fetch CSRF token fresh dari server
 */
async function fetchCsrfToken() {
    try {
        const res = await fetch("/csrf-token", {
            method: "GET",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            credentials: "same-origin",
        });
        if (!res.ok) return null;
        const data = await res.json();
        return data.token ?? null;
    } catch {
        return null;
    }
}

/**
 * Logout via POST /logout agar audit log tercatat di backend.
 * @param {boolean} isAuto - true jika dipanggil dari countdown otomatis
 */
async function doLogout(isAuto = false) {
    if (isAuto) {
        stayBtn.disabled = true;
        logoutBtn.disabled = true;
    } else {
        setButtonLoading("logout", true);
    }

    const token = await fetchCsrfToken();

    if (!token) {
        // Session sudah tidak valid di server — redirect ke login langsung
        hideModal();
        window.location.href = "/login";
        return;
    }

    hideModal();

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "/logout";

    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "_token";
    csrfInput.value = token;

    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}

/**
 * Handle klik tombol "Tetap Masuk" / "Coba Lagi"
 */
async function handleStayLogin() {
    setButtonLoading("stay", true);
    hideNetworkMessage();

    const result = await pingServer();

    if (result.ok) {
        hideModal();
        attachActivityListeners();
        // Reset lastActivityTime agar timer mulai dari awal
        resetTimer();
    } else if (result.reason === "auth") {
        doLogout(true);
    } else {
        showNetworkMessage();
    }
}

/**
 * Ping server via /health untuk memverifikasi sesi backend
 */
async function pingServer() {
    try {
        const res = await fetch("/health", {
            method: "GET",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            credentials: "same-origin",
        });
        if (res.ok) return { ok: true };
        if (res.status === 401 || res.status === 419) {
            return { ok: false, reason: "auth" };
        }
        return { ok: false, reason: "network" };
    } catch {
        return { ok: false, reason: "network" };
    }
}

/**
 * Handler visibilitychange — cek real elapsed time saat tab kembali aktif.
 * Mengatasi browser tab throttling yang memperlambat setTimeout.
 */
function handleVisibilityChange() {
    if (document.visibilityState !== "visible") return;

    if (isModalVisible) {
        // Tab kembali aktif saat modal sudah tampil
        // Cek apakah countdown sudah habis selama tab tidak aktif
        if (countdownEndTime !== null) {
            const remaining = Math.max(
                0,
                Math.round((countdownEndTime - Date.now()) / 1000),
            );
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                doLogout(true);
            } else {
                // Update tampilan countdown dengan sisa waktu yang akurat
                countdownEl.textContent = formatTime(remaining);
            }
        }
        return;
    }

    // Tab kembali aktif, modal belum tampil
    // Cek apakah sudah melewati 20 menit berdasarkan real elapsed time
    const elapsed = Date.now() - lastActivityTime;
    if (elapsed >= INACTIVE_DURATION) {
        clearTimeout(inactivityTimer);
        showModal();
    } else {
        // Belum 20 menit — reset timer dengan sisa waktu yang benar
        clearTimeout(inactivityTimer);
        const remaining = INACTIVE_DURATION - elapsed;
        inactivityTimer = setTimeout(showModal, remaining);
    }
}

/**
 * Inisialisasi session timeout
 * Dipanggil dari app.js via lazy load
 */
export function initSessionTimeout() {
    modal = document.getElementById("sessionTimeoutModal");
    countdownEl = document.getElementById("sessionCountdown");
    stayBtn = document.getElementById("sessionStayBtn");
    logoutBtn = document.getElementById("sessionLogoutBtn");
    networkMsgEl = document.getElementById("sessionNetworkMsg");

    if (!modal || !countdownEl || !stayBtn || !logoutBtn || !networkMsgEl)
        return;

    stayBtnText = stayBtn.querySelector(".btn-text");
    stayBtnLoader = stayBtn.querySelector(".btn-loader");
    logoutBtnText = logoutBtn.querySelector(".btn-text");
    logoutBtnLoader = logoutBtn.querySelector(".btn-loader");

    stayBtn.addEventListener("click", handleStayLogin);
    logoutBtn.addEventListener("click", () => doLogout(false));

    // Simpan referensi handler agar bisa di-remove saat modal muncul
    activityHandler = resetTimer;

    // Pasang visibilitychange listener untuk mengatasi tab throttling
    document.addEventListener("visibilitychange", handleVisibilityChange);

    // Pasang activity listeners dan mulai timer pertama kali
    attachActivityListeners();
    resetTimer();
}
