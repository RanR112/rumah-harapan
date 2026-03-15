/**
 * Anak Asuh Management Page - Main Entry Point
 */

import {
    initAlertModal,
    showSuccessAlert,
    showErrorAlert,
    waitForPageTransition,
} from "../../components/alert-modal.js";

import { setupImportModal } from "./modules/import-modal.js";

import {
    setupSearchHandler,
    fetchData,
    updateResetButtonVisibility,
    resetFilters,
} from "./modules/search-pagination.js";

import { FilterModal } from "./modules/modal-filter.js";
import { initAnakAsuhForm } from "./modules/form-handler.js";

let context = null;
let filterModal = null;

// Storage key untuk menyimpan URL index sebelum navigasi ke form
const RETURN_URL_KEY = "anakAsuh_returnUrl";

/**
 * Simpan URL index saat ini ke sessionStorage sebelum navigasi ke edit/show.
 * URL sudah punya semua state (page, search, filter) karena search-pagination.js
 * selalu update URL via window.history.pushState.
 */
function saveReturnUrl() {
    sessionStorage.setItem(RETURN_URL_KEY, window.location.href);
}

// ==================== IMPORT MODAL SETUP ====================
function setupImportModalData() {
    const importBtn = document.getElementById("openImportModalBtn");
    if (!importBtn || !context) return;

    setupImportModal(
        {
            importUrl: `${window.userData.baseUrl}/anak-asuh/import`,
            csrfToken: window.userData.csrfToken,
        },
        showSuccessAlert,
        showErrorAlert,
        context,
        () => fetchData(context, 1),
    );
}

// ==================== ATTACH ACTION BUTTONS LISTENERS ====================
export function attachActionButtonsListeners(ctx = null) {
    const currentContext = ctx || context;
    if (!currentContext) {
        console.warn("attachActionButtonsListeners: context not available");
        return;
    }

    document.querySelectorAll(".btn-anak-asuh-delete").forEach((button) => {
        button.addEventListener("click", function () {
            const anakAsuhId = this.dataset.anakAsuhId;
            const anakAsuhName = this.dataset.anakAsuhName;

            const {
                alertModal,
                alertTitle,
                alertMessage,
                alertConfirmBtn,
                alertCancelBtn,
            } = currentContext;

            alertTitle.textContent = "Konfirmasi Hapus Permanen";
            alertMessage.textContent = `Hapus permanen data anak asuh ${anakAsuhName}? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
            alertConfirmBtn.textContent = "Hapus Permanen";
            alertConfirmBtn.className = "alert-btn alert-btn-confirm";
            alertConfirmBtn.onclick = () => {
                document.getElementById(`delete-form-${anakAsuhId}`).submit();
            };
            alertCancelBtn.style.display = "block";

            alertModal.style.display = "flex";
            document.body.style.overflow = "hidden";
        });
    });

    document.querySelectorAll(".btn-anak-asuh-view").forEach((button) => {
        button.addEventListener("click", function () {
            // Simpan URL index sebelum navigasi ke show
            saveReturnUrl();
            window.location.href = `${currentContext.baseUrl}/anak-asuh/${this.dataset.anakAsuhId}`;
        });
    });

    document.querySelectorAll(".btn-anak-asuh-edit").forEach((button) => {
        button.addEventListener("click", function () {
            // Simpan URL index sebelum navigasi ke edit
            saveReturnUrl();
            window.location.href = `${currentContext.baseUrl}/anak-asuh/${this.dataset.anakAsuhId}/edit`;
        });
    });
}

// ==================== FILTER MODAL ====================
function initFilterModal() {
    if (!document.querySelector(".anak-asuh-page")) return;

    filterModal = new FilterModal();

    window.anakAsuhApplyFilters = function () {
        updateResetButtonVisibility();
        fetchData(context, 1, false);
    };
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

// ==================== INITIALIZATION ====================
function init() {
    "use strict";

    if (
        !document.querySelector(".anak-asuh-page") &&
        !document.querySelector(".anak-asuh-form-page")
    ) {
        return;
    }

    // Form page (create / edit / show)
    if (document.querySelector(".anak-asuh-form-page")) {
        context = {
            alertModal: document.getElementById("alertModal"),
            alertTitle: document.getElementById("alertModalTitle"),
            alertMessage: document.getElementById("alertModalMessage"),
            alertCancelBtn: document.getElementById("alertCancelBtn"),
            alertConfirmBtn: document.getElementById("alertConfirmBtn"),
            alertContainer: document.querySelector(".alert-modal-container"),
            alertIcon: document.querySelector(".alert-icon"),
        };

        if (
            context.alertModal &&
            context.alertCancelBtn &&
            context.alertConfirmBtn
        ) {
            initAlertModal(context);
        }

        const berkasConfig = window.berkasConfig ?? null;

        setTimeout(() => {
            initAnakAsuhForm(
                showSuccessAlert,
                showErrorAlert,
                context,
                berkasConfig,
            );
        }, 100);

        checkQueryParamsForAlerts();
        attachActionButtonsListeners();

        // ── Restore page state dari sessionStorage ──────────────────────────
        // Berlaku untuk halaman edit dan show — keduanya menggunakan class
        // .anak-asuh-form-page dan me-load index.js yang sama.
        const returnUrl = sessionStorage.getItem(RETURN_URL_KEY);
        const btnBack = document.getElementById("btnBack");

        if (btnBack) {
            if (returnUrl) {
                btnBack.onclick = () => {
                    window.location.href = returnUrl;
                };
            } else {
                // Fallback jika tidak ada returnUrl (misal buka langsung via URL)
                btnBack.onclick = () => {
                    window.location.href = `${window.userData?.baseUrl ?? ""}/anak-asuh`;
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

        // Isi hidden input current_page di form delete pada show page (jika ada)
        const deleteCurrentPage = document.getElementById("deleteCurrentPage");
        if (deleteCurrentPage && returnUrl) {
            try {
                const page =
                    parseInt(new URL(returnUrl).searchParams.get("page")) || 1;
                deleteCurrentPage.value = page;
            } catch {
                deleteCurrentPage.value = 1;
            }
        }
        // ────────────────────────────────────────────────────────────────────

        return;
    }

    // List page
    context = {
        searchInput: document.getElementById("searchInput"),
        searchLoader: document.getElementById("searchLoader"),
        anakAsuhContainer: document.getElementById("anakAsuhContainer"),
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
        perPage: 8,
        csrfToken: window.userData.csrfToken,
        baseUrl: window.userData.baseUrl,
        onAfterRender: attachActionButtonsListeners,
    };

    initAlertModal(context);
    setupImportModalData();
    setupSearchHandler(context);
    initFilterModal();
    checkQueryParamsForAlerts();

    const resetBtn = document.getElementById("anakAsuhResetFilterBtn");
    if (resetBtn) {
        resetBtn.addEventListener("click", () => {
            resetFilters(context);

            if (filterModal && typeof filterModal.resetFilters === "function") {
                filterModal.filterValues = {
                    status: "",
                    is_active: "",
                    grade: "",
                    rh: "",
                };
                filterModal.updateApplyBtnState();
                if (filterModal.filterTrigger) {
                    filterModal.filterTrigger.classList.remove("active");
                }
                document
                    .querySelectorAll("#filterModal .modal-custom-dropdown")
                    .forEach((dropdown) => {
                        const textEl = dropdown.querySelector(
                            ".modal-dropdown-text",
                        );
                        const hiddenInput = dropdown.querySelector(
                            'input[type="hidden"]',
                        );
                        const filter = dropdown.getAttribute("data-filter");
                        if (textEl) {
                            const defaultTexts = {
                                status: "Semua Status",
                                is_active: "Semua Keaktifan",
                                grade: "Semua Grade",
                                rh: "Semua Asrama",
                            };
                            textEl.textContent =
                                defaultTexts[filter] || "Semua";
                        }
                        if (hiddenInput) hiddenInput.value = "";
                    });
            }
        });
    }

    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get("page")) || 1;
        const search = params.get("search") || "";
        if (context.searchInput) context.searchInput.value = search;
        fetchData(context, page, false);
    });

    // Initial load — baca dari URL params
    const params = new URLSearchParams(window.location.search);
    const initialPage = parseInt(params.get("page")) || 1;
    const initialSearch = params.get("search") || "";
    const initialStatus = params.get("status") || "";
    const initialIsActive = params.get("is_active") ?? "";
    const initialGrade = params.get("grade") || "";
    const initialRh = params.get("rh") || "";

    if (context.searchInput) context.searchInput.value = initialSearch;

    const statusFilterEl = document.getElementById("statusFilter");
    const isActiveFilterEl = document.getElementById("isActiveFilter");
    const gradeFilterEl = document.getElementById("gradeFilter");
    const rhFilterEl = document.getElementById("rhFilter");

    if (statusFilterEl) statusFilterEl.value = initialStatus;
    if (isActiveFilterEl) isActiveFilterEl.value = initialIsActive;
    if (gradeFilterEl) gradeFilterEl.value = initialGrade;
    if (rhFilterEl) rhFilterEl.value = initialRh;

    if (initialStatus || initialIsActive !== "" || initialGrade || initialRh) {
        if (filterModal) {
            filterModal.setValues({
                status: initialStatus,
                is_active: initialIsActive,
                grade: initialGrade,
                rh: initialRh,
            });
            filterModal.updateApplyBtnState();

            const dropdownTexts = {
                status: initialStatus
                    ? document
                        .querySelector(
                            `#filterModal [data-filter="status"] .modal-dropdown-item[data-value="${initialStatus}"]`,
                        )
                        ?.textContent?.trim() || initialStatus
                    : "Semua Status",
                is_active:
                    initialIsActive !== ""
                        ? initialIsActive === "1"
                            ? "Aktif"
                            : "Tidak Aktif"
                        : "Semua Keaktifan",
                grade: initialGrade ? `Grade ${initialGrade}` : "Semua Grade",
                rh: initialRh
                    ? document
                        .querySelector(
                            `#filterModal [data-filter="rh"] .modal-dropdown-item[data-value="${initialRh}"]`,
                        )
                        ?.textContent?.trim() || "Semua Asrama"
                    : "Semua Asrama",
            };

            document
                .querySelectorAll("#filterModal .modal-custom-dropdown")
                .forEach((dropdown) => {
                    const filter = dropdown.getAttribute("data-filter");
                    const textEl = dropdown.querySelector(
                        ".modal-dropdown-text",
                    );
                    const hiddenInput = dropdown.querySelector(
                        'input[type="hidden"]',
                    );
                    if (textEl && dropdownTexts[filter])
                        textEl.textContent = dropdownTexts[filter];
                    if (hiddenInput) {
                        const values = {
                            status: initialStatus,
                            is_active: initialIsActive,
                            grade: initialGrade,
                            rh: initialRh,
                        };
                        hiddenInput.value = values[filter] ?? "";
                    }
                });

            if (filterModal.filterTrigger) {
                filterModal.filterTrigger.classList.add("active");
            }
        }
    }

    updateResetButtonVisibility();

    // Setelah kembali dari edit/show, bersihkan sessionStorage
    // karena kita sudah kembali ke index
    sessionStorage.removeItem(RETURN_URL_KEY);

    fetchData(context, initialPage, false);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
