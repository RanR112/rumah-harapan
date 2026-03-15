/**
 * Audit Logs Page - Main Entry Point
 */

import {
    setupSearchHandler,
    fetchData,
    resetFilters,
    updateResetButtonVisibility,
    hasActiveFilters,
} from "./modules/search-pagination.js";

// Storage key untuk menyimpan URL index sebelum navigasi ke show
const RETURN_URL_KEY = "auditLog_returnUrl";

/**
 * Simpan URL index saat ini ke sessionStorage sebelum navigasi ke show.
 */
function saveReturnUrl() {
    sessionStorage.setItem(RETURN_URL_KEY, window.location.href);
}

// Context didefinisikan di dalam init() agar tidak error saat module load
let context = null;

// Update action dropdown berdasarkan model yang dipilih
function updateActionDropdown(modelType) {
    if (!context.actionMenu || !context.actionItems?.length) return;

    const validActions = modelType
        ? context.modelActionMap[modelType] || []
        : Object.values(context.modelActionMap).flat();

    const validActionSet = new Set(validActions.map((a) => a.value));

    context.actionItems.forEach((item) => {
        const itemValue = item.dataset.value;
        const isValid = !itemValue || validActionSet.has(itemValue);

        item.style.display = isValid ? "" : "none";

        if (!isValid && item.classList.contains("active")) {
            item.classList.remove("active");
            context.actionHiddenInput.value = "";
            context.actionSelectedText.textContent = "Semua Aksi";
        }
    });
}

function initCustomDropdowns() {
    // Model Type Dropdown
    if (context.modelTypeTrigger && context.modelTypeMenu) {
        context.modelTypeTrigger.addEventListener("click", (e) => {
            e.stopPropagation();
            context.actionMenu?.classList.remove("show");
            context.actionTrigger?.classList.remove("active");
            context.modelTypeMenu.classList.toggle("show");
            context.modelTypeTrigger.classList.toggle("active");
        });

        context.modelTypeItems.forEach((item) => {
            item.addEventListener("click", (e) => {
                e.stopPropagation();
                const value = item.dataset.value;
                const text = item.textContent.trim();

                context.modelTypeSelectedText.textContent = text;
                context.modelTypeItems.forEach((i) =>
                    i.classList.remove("active"),
                );
                item.classList.add("active");
                context.modelTypeHiddenInput.value = value;

                context.modelTypeMenu.classList.remove("show");
                context.modelTypeTrigger.classList.remove("active");

                updateActionDropdown(value);
                updateResetButtonVisibility(context);
                fetchData(context, 1);
            });
        });
    }

    // Action Dropdown
    if (context.actionTrigger && context.actionMenu) {
        context.actionTrigger.addEventListener("click", (e) => {
            e.stopPropagation();
            context.modelTypeMenu?.classList.remove("show");
            context.modelTypeTrigger?.classList.remove("active");
            context.actionMenu.classList.toggle("show");
            context.actionTrigger.classList.toggle("active");
        });

        context.actionItems.forEach((item) => {
            item.addEventListener("click", (e) => {
                e.stopPropagation();
                const value = item.dataset.value;
                const text = item.textContent.trim();

                context.actionSelectedText.textContent = text;
                context.actionItems.forEach((i) =>
                    i.classList.remove("active"),
                );
                item.classList.add("active");
                context.actionHiddenInput.value = value;

                context.actionMenu.classList.remove("show");
                context.actionTrigger.classList.remove("active");

                updateResetButtonVisibility(context);
                fetchData(context, 1);
            });
        });
    }

    // Close dropdowns on outside click
    document.addEventListener("click", (e) => {
        if (
            context.modelTypeDropdown &&
            !context.modelTypeDropdown.contains(e.target)
        ) {
            context.modelTypeMenu?.classList.remove("show");
            context.modelTypeTrigger?.classList.remove("active");
        }
        if (
            context.actionDropdown &&
            !context.actionDropdown.contains(e.target)
        ) {
            context.actionMenu?.classList.remove("show");
            context.actionTrigger?.classList.remove("active");
        }
    });

    // Close dropdowns on ESC key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            context.modelTypeMenu?.classList.remove("show");
            context.modelTypeTrigger?.classList.remove("active");
            context.actionMenu?.classList.remove("show");
            context.actionTrigger?.classList.remove("active");
        }
    });

    // Reset filter button handler
    if (context.resetFilterBtn) {
        context.resetFilterBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            resetFilters(context);
        });
    }
}

function setInitialValues() {
    const initial = window.userData.initialFilters || {};

    if (context.searchInput && initial.search) {
        context.searchInput.value = initial.search;
    }

    if (initial.model_type && context.modelTypeHiddenInput) {
        context.modelTypeHiddenInput.value = initial.model_type;
        const selectedItem = Array.from(context.modelTypeItems).find(
            (item) => item.dataset.value === initial.model_type,
        );
        if (selectedItem) {
            context.modelTypeSelectedText.textContent =
                selectedItem.textContent.trim();
            selectedItem.classList.add("active");
            updateActionDropdown(initial.model_type);
        }
    }

    if (initial.action && context.actionHiddenInput) {
        context.actionHiddenInput.value = initial.action;
        const selectedItem = Array.from(context.actionItems).find(
            (item) => item.dataset.value === initial.action,
        );
        if (selectedItem) {
            context.actionSelectedText.textContent =
                selectedItem.textContent.trim();
            selectedItem.classList.add("active");
        }
    }

    updateResetButtonVisibility(context);
}

// Tambah event listener saveReturnUrl pada semua tombol detail (action-btn-view)
// Dipanggil setiap setelah table di-render ulang via onAfterRender
function attachDetailLinkListeners() {
    document.querySelectorAll(".action-btn-view").forEach((link) => {
        link.addEventListener("click", () => {
            saveReturnUrl();
        });
    });
}

function init() {
    "use strict";

    context = {
        searchInput: document.getElementById("searchInput"),
        searchLoader: document.getElementById("searchLoader"),
        resetFilterBtn: document.getElementById("resetFilterBtn"),

        modelTypeDropdown: document.getElementById("modelTypeDropdown"),
        modelTypeTrigger: document.querySelector(
            "#modelTypeDropdown .dropdown-trigger",
        ),
        modelTypeMenu: document.querySelector(
            "#modelTypeDropdown .dropdown-menu",
        ),
        modelTypeItems: document.querySelectorAll(
            "#modelTypeDropdown .dropdown-item",
        ),
        modelTypeHiddenInput: document.getElementById("modelTypeFilterValue"),
        modelTypeSelectedText: document.querySelector(
            "#modelTypeDropdown .selected-text",
        ),

        actionDropdown: document.getElementById("actionDropdown"),
        actionTrigger: document.querySelector(
            "#actionDropdown .dropdown-trigger",
        ),
        actionMenu: document.querySelector("#actionDropdown .dropdown-menu"),
        actionItems: document.querySelectorAll(
            "#actionDropdown .dropdown-item",
        ),
        actionHiddenInput: document.getElementById("actionFilterValue"),
        actionSelectedText: document.querySelector(
            "#actionDropdown .selected-text",
        ),

        tableBody: document.getElementById("auditLogsTableBody"),
        paginationContainer: document.getElementById("paginationContainer"),

        searchTimeout: null,
        currentPage: 1,
        perPage: 7,
        csrfToken: window.userData.csrfToken,
        baseUrl: window.userData.baseUrl,
        modelActionMap: window.userData.modelActionMap || {},

        // Setelah setiap render, pasang kembali listener pada tombol detail
        onAfterRender: attachDetailLinkListeners,
    };

    setInitialValues();
    initCustomDropdowns();
    setupSearchHandler(context);

    window.addEventListener("popstate", (e) => {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get("page")) || 1;
        const search = params.get("search") || "";
        const modelType = params.get("model_type") || "";
        const action = params.get("action") || "";

        if (context.searchInput) context.searchInput.value = search;
        if (context.modelTypeHiddenInput)
            context.modelTypeHiddenInput.value = modelType;
        if (context.actionHiddenInput) context.actionHiddenInput.value = action;

        if (modelType) {
            const item = Array.from(context.modelTypeItems).find(
                (i) => i.dataset.value === modelType,
            );
            if (item)
                context.modelTypeSelectedText.textContent =
                    item.textContent.trim();
            updateActionDropdown(modelType);
        } else {
            context.modelTypeSelectedText.textContent = "Semua Model";
        }

        if (action) {
            const item = Array.from(context.actionItems).find(
                (i) => i.dataset.value === action,
            );
            if (item)
                context.actionSelectedText.textContent =
                    item.textContent.trim();
        } else {
            context.actionSelectedText.textContent = "Semua Aksi";
        }

        updateResetButtonVisibility(context);
        fetchData(context, page);
    });

    // Bersihkan sessionStorage saat kembali ke index
    sessionStorage.removeItem(RETURN_URL_KEY);

    const params = new URLSearchParams(window.location.search);
    const initialPage = parseInt(params.get("page")) || 1;
    fetchData(context, initialPage);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
