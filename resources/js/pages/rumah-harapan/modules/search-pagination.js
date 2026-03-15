/**
 * Rumah Harapan Search & Pagination Module
 *
 * Menangani:
 * - AJAX search dengan debounce
 * - Pagination navigation
 * - URL state management
 * - Reset filter visibility
 */

import {
    renderRumahHarapanTable,
    renderPagination,
    showLoading,
    showError,
} from "./table-renderer.js";

// Cek apakah ada filter aktif (bukan nilai default)
export function hasActiveFilters(context) {
    const statusValue =
        document.getElementById("statusFilterValue")?.value.trim() || "";

    // Filter aktif jika status tidak kosong (kosong = "Semua Status")
    return statusValue !== "";
}

// Update visibility reset button berdasarkan status filter
export function updateResetButtonVisibility(context) {
    const resetBtn = document.getElementById("resetFilterBtnRh");
    if (!resetBtn) return;

    resetBtn.style.display = hasActiveFilters(context) ? "flex" : "none";
}

// Fetch data dari server
export async function fetchData(context, page = 1, isSearch = false) {
    context.currentPage = page;
    const searchValue = context.searchInput?.value.trim() || "";
    const statusValue =
        document.getElementById("statusFilterValue")?.value.trim() || "";

    // Tampilkan loader sesuai jenis operasi
    showLoading(true, context, isSearch);

    try {
        const url = new URL(`${context.baseUrl}/rumah-harapan`);
        url.searchParams.append("search", searchValue);
        if (statusValue !== "")
            url.searchParams.append("is_active", statusValue);
        url.searchParams.append("page", page);
        url.searchParams.append("per_page", context.perPage);

        const response = await fetch(url.toString(), {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();

        // Render table dan pagination
        renderRumahHarapanTable(
            context,
            data.data,
            data.first_item,
            data.total,
        );
        renderPagination(context, data.current_page, data.last_page);
        updateResetButtonVisibility(context);

        if (context.onAfterRender) {
            context.onAfterRender(context);
        }

        // Update URL state
        const newUrl = new URL(`${context.baseUrl}/rumah-harapan`);
        if (searchValue) newUrl.searchParams.append("search", searchValue);
        if (statusValue !== "")
            newUrl.searchParams.append("is_active", statusValue);
        if (page > 1) newUrl.searchParams.append("page", page);
        window.history.pushState(
            { path: newUrl.toString() },
            "",
            newUrl.toString(),
        );
    } catch (error) {
        console.error("Fetch error:", error);
        showError("Gagal memuat data asrama. Silakan coba lagi.", context);
    } finally {
        // Sembunyikan loader sesuai jenis operasi
        showLoading(false, context, isSearch);
    }
}

// Reset hanya status filter (TIDAK mereset search input)
export function resetFilters(context) {
    const statusFilterValue = document.getElementById("statusFilterValue");
    const statusFilterTrigger = document.getElementById("statusFilterTrigger");
    const statusFilterMenu = document.getElementById("statusFilterMenu");

    // Tutup dropdown jika terbuka
    if (statusFilterTrigger?.getAttribute("aria-expanded") === "true") {
        statusFilterTrigger.setAttribute("aria-expanded", "false");
        if (statusFilterMenu) statusFilterMenu.style.display = "none";
        const chevron = statusFilterTrigger.querySelector(".dropdown-chevron");
        if (chevron) chevron.style.transform = "rotate(0deg)";
    }

    // Reset nilai filter
    if (statusFilterValue) {
        statusFilterValue.value = "";
        const dropdownValue =
            statusFilterTrigger?.querySelector(".dropdown-value");
        if (dropdownValue) dropdownValue.textContent = "Semua Status";
    }

    updateResetButtonVisibility(context);

    // Fetch dengan isSearch = false (hanya table loader)
    fetchData(context, 1, false);
}

// Setup search handler dengan debounce
export function setupSearchHandler(context) {
    if (!context.searchInput) return;

    context.searchInput.addEventListener("input", () => {
        clearTimeout(context.searchTimeout);
        context.searchTimeout = setTimeout(() => {
            // isSearch = true agar search loader + table loader muncul
            fetchData(context, 1, true);
        }, 300);
    });
}

// Setup filter handlers
export function setupFilterHandlers(context) {
    const activeFilter = document.getElementById("filterActive");
    if (activeFilter) {
        activeFilter.addEventListener("change", () => {
            fetchData(context, 1);
        });
    }
}
