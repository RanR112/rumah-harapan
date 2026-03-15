/**
 * Rumah Harapan Management Page - Main Entry Point
 */

import {
    initAlertModal,
    resetAlertModal,
    showSuccessAlert,
    showErrorAlert,
    waitForPageTransition,
} from "../../components/alert-modal.js";

import {
    setupSearchHandler,
    fetchData,
    setupFilterHandlers,
    resetFilters,
    updateResetButtonVisibility,
} from "./modules/search-pagination.js";

import { initRumahHarapanForm } from "./modules/form-handler.js";

// Storage key untuk menyimpan URL index sebelum navigasi ke form
const RETURN_URL_KEY = "rumahHarapan_returnUrl";

/**
 * Simpan URL index saat ini ke sessionStorage sebelum navigasi ke edit/show.
 */
function saveReturnUrl() {
    sessionStorage.setItem(RETURN_URL_KEY, window.location.href);
}

// Context didefinisikan di dalam init() agar tidak error saat
// module load di halaman form yang tidak punya elemen index
let context = null;

// ==================== CUSTOM DROPDOWN STATUS FILTER ====================
function initStatusFilterDropdown() {
    const trigger = document.getElementById("statusFilterTrigger");
    const menu = document.getElementById("statusFilterMenu");
    const hiddenInput = document.getElementById("statusFilterValue");
    const resetBtn = document.getElementById("resetFilterBtnRh");

    if (!trigger || !menu || !hiddenInput) {
        console.warn("Status filter dropdown elements not found");
        return;
    }

    const dropdownValue = trigger.querySelector(".dropdown-value");

    function toggleDropdown(show = null) {
        const isExpanded = trigger.getAttribute("aria-expanded") === "true";
        const shouldShow = show !== null ? show : !isExpanded;
        trigger.setAttribute("aria-expanded", String(shouldShow));
        menu.style.display = shouldShow ? "block" : "none";
        const chevron = trigger.querySelector(".dropdown-chevron");
        if (chevron) {
            chevron.style.transform = shouldShow
                ? "rotate(180deg)"
                : "rotate(0deg)";
        }
    }

    function handleOptionClick(option) {
        const value = option.dataset.value;
        const text = option.textContent.trim();
        if (dropdownValue) dropdownValue.textContent = text;
        hiddenInput.value = value;
        toggleDropdown(false);
        updateResetButtonVisibility(context);
        fetchData(context, 1, false);
    }

    trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleDropdown();
    });

    menu.querySelectorAll(".dropdown-option").forEach((option) => {
        option.addEventListener("click", (e) => {
            e.stopPropagation();
            handleOptionClick(option);
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            resetFilters(context);
        });
    }

    document.addEventListener("click", (e) => {
        const dropdownContainer = trigger.closest(
            ".rumah-harapan-status-filter",
        );
        if (dropdownContainer && !dropdownContainer.contains(e.target)) {
            toggleDropdown(false);
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") toggleDropdown(false);
    });

    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get("is_active");
    if (statusParam !== null) {
        hiddenInput.value = statusParam;
        const option = menu.querySelector(`[data-value="${statusParam}"]`);
        if (option && dropdownValue) {
            dropdownValue.textContent = option.textContent.trim();
        }
    }
}

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
    // Tombol delete — hanya ada untuk admin
    document.querySelectorAll(".btn-rumah-harapan-delete").forEach((button) => {
        button.addEventListener("click", function () {
            resetAlertModal(context);
            const rumahHarapanId = this.dataset.rumahHarapanId;
            const rumahHarapanName = this.dataset.rumahHarapanName;

            context.alertTitle.textContent = "Konfirmasi Hapus Permanen";
            context.alertMessage.textContent = `Hapus permanen data asrama ${rumahHarapanName}? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
            context.alertConfirmBtn.textContent = "Hapus Permanen";
            context.alertConfirmBtn.className = "alert-btn alert-btn-confirm";
            context.alertConfirmBtn.onclick = () => {
                document
                    .getElementById(`delete-form-${rumahHarapanId}`)
                    .submit();
            };

            context.alertModal.style.display = "flex";
            document.body.style.overflow = "hidden";
        });
    });

    // Tombol edit — hanya ada untuk admin, simpan returnUrl sebelum navigasi
    document.querySelectorAll(".btn-rumah-harapan-edit").forEach((button) => {
        button.addEventListener("click", function () {
            saveReturnUrl();
            const rumahHarapanId = this.dataset.rumahHarapanId;
            window.location.href = `${context.baseUrl}/rumah-harapan/${rumahHarapanId}/edit`;
        });
    });

    // Tombol show — hanya ada untuk petugas (berupa <a> tag native)
    // Tambah event listener untuk menyimpan returnUrl sebelum navigasi
    document.querySelectorAll(".btn-rumah-harapan-show").forEach((link) => {
        link.addEventListener("click", () => {
            saveReturnUrl();
        });
    });
}

// ==================== INITIALIZATION ====================
function init() {
    "use strict";

    // Halaman form (create / edit / show)
    if (document.querySelector(".rumah-harapan-form-page")) {
        initRumahHarapanForm();

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
                    window.location.href = `${window.userData?.baseUrl ?? ""}/rumah-harapan`;
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
        tableContainer: document.getElementById("rumahHarapanTableBody"),
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
        userRole: window.userData.userRole,
        onAfterRender: attachActionButtonsListeners,
    };

    initAlertModal(context);
    setupSearchHandler(context);
    setupFilterHandlers(context);
    initStatusFilterDropdown();
    updateResetButtonVisibility(context);
    attachActionButtonsListeners();
    checkQueryParamsForAlerts();

    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get("page")) || 1;
        const search = params.get("search") || "";
        const isActive = params.get("is_active") || "";

        if (context.searchInput) context.searchInput.value = search;

        const statusFilterValue = document.getElementById("statusFilterValue");
        const statusFilterTrigger = document.getElementById(
            "statusFilterTrigger",
        );
        const statusFilterMenu = document.getElementById("statusFilterMenu");

        if (statusFilterValue) {
            statusFilterValue.value = isActive;
            const dropdownValue =
                statusFilterTrigger?.querySelector(".dropdown-value");
            if (dropdownValue) {
                const option = statusFilterMenu?.querySelector(
                    `[data-value="${isActive}"]`,
                );
                dropdownValue.textContent = option
                    ? option.textContent.trim()
                    : "Semua Status";
            }
        }

        updateResetButtonVisibility(context);
        fetchData(context, page);
    });

    // Initial data load
    const params = new URLSearchParams(window.location.search);
    const initialPage = parseInt(params.get("page")) || 1;
    const initialSearch = params.get("search") || "";
    const initialStatus = params.get("is_active") || "";

    if (context.searchInput) context.searchInput.value = initialSearch;

    const statusFilterValue = document.getElementById("statusFilterValue");
    if (statusFilterValue) statusFilterValue.value = initialStatus;

    // Bersihkan sessionStorage saat kembali ke index
    sessionStorage.removeItem(RETURN_URL_KEY);

    fetchData(context, initialPage);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
