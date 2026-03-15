/**
 * User Management Page - Main Entry Point
 */

import {
    initAlertModal,
    resetAlertModal,
    showSuccessAlert,
    showErrorAlert,
    waitForPageTransition,
} from "../../components/alert-modal.js";

import { setupSearchHandler, fetchData } from "./modules/search-pagination.js";
import { initUserForm } from "./modules/form-handler.js";

// Storage key untuk menyimpan URL index sebelum navigasi ke form
const RETURN_URL_KEY = "users_returnUrl";

/**
 * Simpan URL index saat ini ke sessionStorage sebelum navigasi ke edit.
 */
function saveReturnUrl() {
    sessionStorage.setItem(RETURN_URL_KEY, window.location.href);
}

// Context didefinisikan di dalam init() agar tidak error saat
// module load di halaman form yang tidak punya elemen index
let context = null;

// ==================== CHECK QUERY PARAMS FOR ALERTS ====================
function checkQueryParamsForAlerts() {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get("success");
    const errorMessage = urlParams.get("error");

    waitForPageTransition(() => {
        if (successMessage) {
            showSuccessAlert(context, successMessage);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("success");
            window.history.replaceState({}, "", newUrl.toString());
        }

        if (errorMessage) {
            showErrorAlert(context, errorMessage);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("error");
            window.history.replaceState({}, "", newUrl.toString());
        }
    });
}

// ==================== ATTACH ACTION BUTTONS LISTENERS ====================
export function attachActionButtonsListeners() {
    // Tombol delete
    document.querySelectorAll(".btn-hard-delete").forEach((button) => {
        button.addEventListener("click", function () {
            resetAlertModal(context);
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            context.alertTitle.textContent = "Konfirmasi Hapus Permanen";
            context.alertMessage.textContent = `Hapus permanen pengguna ${userName}? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
            context.alertConfirmBtn.textContent = "Hapus Permanen";
            context.alertConfirmBtn.className = "alert-btn alert-btn-confirm";
            context.alertConfirmBtn.onclick = () => {
                document.getElementById(`hard-delete-form-${userId}`).submit();
            };

            context.alertModal.style.display = "flex";
            document.body.style.overflow = "hidden";
        });
    });

    // Tombol edit — berupa <a> tag, simpan returnUrl sebelum navigasi
    document.querySelectorAll(".action-btn-edit").forEach((link) => {
        link.addEventListener("click", () => {
            saveReturnUrl();
        });
    });
}

// ==================== INITIALIZATION ====================
function init() {
    "use strict";

    // Halaman form (create / edit)
    if (document.querySelector(".users-form-page")) {
        initUserForm();

        // ── Restore page state dari sessionStorage ──────────────────────────
        const returnUrl = sessionStorage.getItem(RETURN_URL_KEY);
        const btnBack = document.getElementById("btnBack");

        if (btnBack) {
            if (returnUrl) {
                btnBack.onclick = () => {
                    window.location.href = returnUrl;
                };
            } else {
                btnBack.onclick = () => {
                    window.location.href = `${window.userData?.baseUrl ?? ""}/users`;
                };
            }
        }

        // Isi hidden input current_page di form edit (jika ada)
        const currentPageInput = document.getElementById("currentPageInput");
        if (currentPageInput && returnUrl) {
            try {
                const page =
                    parseInt(new URL(returnUrl).searchParams.get("page")) || 1;
                currentPageInput.value = page;
            } catch {
                currentPageInput.value = 1;
            }
        }
        // ────────────────────────────────────────────────────────────────────

        return;
    }

    // Halaman index
    context = {
        searchInput: document.getElementById("searchInput"),
        searchLoader: document.getElementById("searchLoader"),
        tableBody: document.getElementById("usersTableBody"),
        paginationContainer: document.getElementById("paginationContainer"),
        alertModal: document.getElementById("alertModal"),
        alertTitle: document.getElementById("alertModalTitle"),
        alertMessage: document.getElementById("alertModalMessage"),
        alertCancelBtn: document.getElementById("alertCancelBtn"),
        alertConfirmBtn: document.getElementById("alertConfirmBtn"),
        alertContainer: document.querySelector(".alert-modal-container"),
        alertIcon: document.querySelector(".alert-icon"),
        searchTimeout: null,
        currentPage: 1,
        perPage: 7,
        csrfToken: window.userData.csrfToken,
        baseUrl: window.userData.baseUrl,
        onAfterRender: attachActionButtonsListeners,
    };

    initAlertModal(context);
    setupSearchHandler(context);
    attachActionButtonsListeners();
    checkQueryParamsForAlerts();

    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get("page")) || 1;
        const search = params.get("search") || "";
        if (context.searchInput) context.searchInput.value = search;
        fetchData(context, page);
    });

    const params = new URLSearchParams(window.location.search);
    const initialPage = parseInt(params.get("page")) || 1;
    const initialSearch = params.get("search") || "";

    if (context.searchInput) context.searchInput.value = initialSearch;

    // Bersihkan sessionStorage saat kembali ke index
    sessionStorage.removeItem(RETURN_URL_KEY);

    fetchData(context, initialPage);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
