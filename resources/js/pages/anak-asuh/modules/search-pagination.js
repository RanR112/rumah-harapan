/**
 * Search & Pagination Module for Anak Asuh
 *
 * Handle AJAX search, pagination, dan filter application
 * Filter selalu via modal (semua ukuran layar)
 */

import {
    renderAnakAsuhCards,
    renderPagination,
    showLoading,
    showError,
} from "./card-renderer.js";

// Cek apakah ada filter yang aktif
// is_active: nilai "0" (Tidak Aktif) adalah valid — gunakan !== "" bukan !empty
function hasActiveFilters() {
    const statusValue = document.getElementById("statusFilter")?.value || "";
    const isActiveValue =
        document.getElementById("isActiveFilter")?.value ?? "";
    const gradeValue = document.getElementById("gradeFilter")?.value || "";
    const rhValue = document.getElementById("rhFilter")?.value || "";

    return (
        statusValue !== "" ||
        isActiveValue !== "" ||
        gradeValue !== "" ||
        rhValue !== ""
    );
}

// Update visibility reset button berdasarkan filter aktif
export function updateResetButtonVisibility() {
    const resetBtn = document.getElementById("anakAsuhResetFilterBtn");
    if (!resetBtn) return;
    resetBtn.style.display = hasActiveFilters() ? "flex" : "none";
}

// Reset semua filter (TIDAK mereset search input)
export function resetFilters(context) {
    const statusFilter = document.getElementById("statusFilter");
    const isActiveFilter = document.getElementById("isActiveFilter");
    const gradeFilter = document.getElementById("gradeFilter");
    const rhFilter = document.getElementById("rhFilter");

    if (statusFilter) statusFilter.value = "";
    if (isActiveFilter) isActiveFilter.value = "";
    if (gradeFilter) gradeFilter.value = "";
    if (rhFilter) rhFilter.value = "";

    updateResetButtonVisibility();

    // Fetch ulang — isSearch=false agar hanya table loader yang muncul
    fetchData(context, 1, false);
}

// Fetch data dari server
// isSearch=true  → tampilkan search loader (di dalam input)
// isSearch=false → tampilkan table loader (di dalam container)
export async function fetchData(context, page = 1, isSearch = false) {
    context.currentPage = page;

    const searchValue = context.searchInput?.value.trim() || "";
    const statusValue = document.getElementById("statusFilter")?.value || "";
    const isActiveValue =
        document.getElementById("isActiveFilter")?.value ?? "";
    const gradeValue = document.getElementById("gradeFilter")?.value || "";
    const rhValue = document.getElementById("rhFilter")?.value || "";

    showLoading(true, context, isSearch);

    try {
        const url = new URL(`${context.baseUrl}/anak-asuh`);
        url.searchParams.append("search", searchValue);
        url.searchParams.append("status", statusValue);
        // is_active hanya dikirim jika ada nilai ("0" atau "1") — bukan string kosong
        if (isActiveValue !== "")
            url.searchParams.append("is_active", isActiveValue);
        url.searchParams.append("grade", gradeValue);
        url.searchParams.append("rh", rhValue);
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

        const hasFilters = hasActiveFilters() || searchValue !== "";

        renderAnakAsuhCards(
            context,
            data.data,
            data.first_item,
            data.total,
            hasFilters,
        );
        renderPagination(context, data.current_page, data.last_page);

        if (context.onAfterRender) {
            context.onAfterRender(context);
        }

        // Update URL — is_active hanya append jika ada nilai
        const newUrl = new URL(`${context.baseUrl}/anak-asuh`);
        if (searchValue) newUrl.searchParams.append("search", searchValue);
        if (statusValue) newUrl.searchParams.append("status", statusValue);
        if (isActiveValue !== "")
            newUrl.searchParams.append("is_active", isActiveValue);
        if (gradeValue) newUrl.searchParams.append("grade", gradeValue);
        if (rhValue) newUrl.searchParams.append("rh", rhValue);
        if (page > 1) newUrl.searchParams.append("page", page);
        window.history.pushState(
            { path: newUrl.toString() },
            "",
            newUrl.toString(),
        );

        // Update reset button visibility setelah fetch
        updateResetButtonVisibility();
    } catch (error) {
        console.error("Fetch error:", error);
        showError("Gagal memuat data. Silakan coba lagi.", context);
    } finally {
        showLoading(false, context, isSearch);
    }
}

// Setup search handler dengan debounce
export function setupSearchHandler(context) {
    if (!context.searchInput) return;

    context.searchInput.addEventListener("input", () => {
        clearTimeout(context.searchTimeout);
        context.searchTimeout = setTimeout(() => {
            fetchData(context, 1, true);
        }, 300);
    });
}
