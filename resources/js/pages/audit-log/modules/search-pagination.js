/**
 * Search & Pagination Module untuk Audit Logs
 */

import {
    renderTable,
    renderPagination,
    showLoading,
    showError,
} from "./table-renderer.js";

export function hasActiveFilters(context) {
    const modelTypeValue = context.modelTypeHiddenInput?.value.trim() || "";
    const actionValue = context.actionHiddenInput?.value.trim() || "";
    return modelTypeValue !== "" || actionValue !== "";
}

export function updateResetButtonVisibility(context) {
    const resetBtn = document.getElementById("resetFilterBtn");
    if (!resetBtn) return;
    resetBtn.style.display = hasActiveFilters(context) ? "flex" : "none";
}

export async function fetchData(context, page = 1, isSearch = false) {
    context.currentPage = page;
    const searchValue = context.searchInput?.value.trim() || "";
    const modelTypeValue = context.modelTypeHiddenInput?.value.trim() || "";
    const actionValue = context.actionHiddenInput?.value.trim() || "";

    showLoading(true, context, isSearch);

    try {
        const url = new URL(`${context.baseUrl}/audit-log`);
        url.searchParams.append("search", searchValue);
        if (modelTypeValue)
            url.searchParams.append("model_type", modelTypeValue);
        if (actionValue) url.searchParams.append("action", actionValue);
        url.searchParams.append("page", page);
        url.searchParams.append("per_page", context.perPage);

        const response = await fetch(url.toString(), {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok)
            throw new Error(`HTTP error status: ${response.status}`);

        const data = await response.json();

        renderTable(context, data.data, data.first_item);
        renderPagination(context, data.current_page, data.last_page);
        updateResetButtonVisibility(context);

        // Pasang kembali listeners setelah render (saveReturnUrl pada tombol detail)
        if (context.onAfterRender) {
            context.onAfterRender(context);
        }

        // Update URL state
        const newUrl = new URL(window.location.href);
        newUrl.search = "";
        if (searchValue) newUrl.searchParams.append("search", searchValue);
        if (modelTypeValue)
            newUrl.searchParams.append("model_type", modelTypeValue);
        if (actionValue) newUrl.searchParams.append("action", actionValue);
        if (page > 1) newUrl.searchParams.append("page", page);
        window.history.pushState(
            { path: newUrl.toString() },
            "",
            newUrl.toString(),
        );
    } catch (error) {
        console.error("Fetch error:", error);
        showError("Gagal memuat data audit logs. Silakan coba lagi.", context);
    } finally {
        showLoading(false, context, isSearch);
    }
}

export function resetFilters(context) {
    if (context.modelTypeMenu?.classList.contains("show")) {
        context.modelTypeMenu.classList.remove("show");
        context.modelTypeTrigger?.classList.remove("active");
    }
    if (context.actionMenu?.classList.contains("show")) {
        context.actionMenu.classList.remove("show");
        context.actionTrigger?.classList.remove("active");
    }

    if (context.modelTypeHiddenInput && context.modelTypeSelectedText) {
        context.modelTypeHiddenInput.value = "";
        context.modelTypeSelectedText.textContent = "Semua Model";
        context.modelTypeItems.forEach((item) =>
            item.classList.remove("active"),
        );
    }

    if (context.actionHiddenInput && context.actionSelectedText) {
        context.actionHiddenInput.value = "";
        context.actionSelectedText.textContent = "Semua Aksi";
        context.actionItems.forEach((item) => item.classList.remove("active"));
    }

    updateResetButtonVisibility(context);
    fetchData(context, 1, false);
}

export function setupSearchHandler(context) {
    if (!context.searchInput) return;

    context.searchInput.addEventListener("input", (e) => {
        clearTimeout(context.searchTimeout);
        context.searchTimeout = setTimeout(() => {
            fetchData(context, 1, true);
        }, 300);
    });
}
