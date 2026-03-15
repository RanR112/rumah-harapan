/**
 * Table Renderer Module untuk Audit Logs (Read-Only)
 */

// ==================== HELPER FUNCTIONS ====================
export function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function formatDateTime(timeString) {
    try {
        const [datePart, timePart] = timeString.split(" ");
        const [hours, minutes] = timePart.split(":");
        return `${datePart} ${hours}:${minutes}`;
    } catch (e) {
        return timeString;
    }
}

export function showLoading(state, context, showSearchLoader = false) {
    if (state) {
        if (showSearchLoader) context.searchLoader?.classList.add("active");
        context.tableBody.innerHTML = `
            <tr class="table-row">
                <td colspan="6" class="text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;
    } else {
        if (showSearchLoader) context.searchLoader?.classList.remove("active");
    }
}

export function showError(message, context) {
    context.tableBody.innerHTML = `
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="error-message">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="mt-2">${escapeHtml(message)}</p>
                    <button class="btn-reload mt-3" onclick="location.reload()">
                        Muat Ulang
                    </button>
                </div>
            </td>
        </tr>
    `;

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

// ==================== EMPTY STATE DENGAN 2 KONDISI ====================
export function showEmptyState(context, hasFilters = false) {
    let icon, title, text;

    if (hasFilters) {
        // Kondisi 1: Search/filter tidak menemukan hasil
        icon = `
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
                <path d="m14 8-6 6"></path>
                <path d="m8 8 6 6"></path>
            </svg>
        `;
        title = "Data tidak ditemukan";
        text = "Coba gunakan kata kunci atau filter yang berbeda.";
    } else {
        // Kondisi 2: Belum ada riwayat aktivitas sama sekali
        icon = `
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v3"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <circle cx="8" cy="16" r="6"></circle>
                <path d="M9.5 17.5 8 16.25V14"></path>
            </svg>
        `;
        title = "Belum ada riwayat aktivitas";
        text = "Aktivitas pengguna akan tercatat secara otomatis di sini.";
    }

    context.tableBody.innerHTML = `
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                    ${icon}
                    <h4 class="empty-title">${title}</h4>
                    <p class="empty-text">${text}</p>
                </div>
            </td>
        </tr>
    `;
}

// ==================== TABLE RENDERING FUNCTIONS ====================
export function renderTable(context, data, firstItem) {
    const hasFilters =
        (context.searchInput?.value.trim() || "") !== "" ||
        (context.modelTypeHiddenInput?.value.trim() || "") !== "" ||
        (context.actionHiddenInput?.value.trim() || "") !== "";

    if (data.length === 0) {
        showEmptyState(context, hasFilters);
        return;
    }

    context.tableBody.innerHTML = "";

    data.forEach((log, index) => {
        const rowNumber = firstItem + index;
        const actionClass =
            log.action === "Dibuat"
                ? "success"
            : log.action === "Diperbarui"
                ? "warning"
            : log.action === "Dihapus"
                ? "danger"
            : log.action === "Masuk"
                ? "primary"
            : log.action === "Keluar"
                ? "secondary"
            : "info";

        const row = document.createElement("tr");
        row.className = "table-row";
        row.innerHTML = `
            <td class="text-center">${rowNumber}</td>
            <td class="audit-user-cell">${escapeHtml(log.user_name)}</td>
            <td class="audit-model-cell">${escapeHtml(log.model_type)}</td>
            <td class="audit-action-cell">
                <span class="badge badge-${actionClass}">
                    ${escapeHtml(log.action)}
                </span>
            </td>
            <td class="audit-date-cell">${escapeHtml(formatDateTime(log.created_at))}</td>
            <td class="text-center audit-detail-cell">
                <a href="${escapeHtml(log.detail_url)}" class="action-btn action-btn-view" title="Lihat Detail">
                    <i data-lucide="eye" class="action-icon"></i>
                </a>
            </td>
        `;

        context.tableBody.appendChild(row);
    });

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

export function renderPagination(context, currentPage, lastPage) {
    if (lastPage <= 1) {
        context.paginationContainer.style.display = "none";
        return;
    }
    context.paginationContainer.style.display = "block";

    const isMobile = window.innerWidth <= 480;
    const pagesAroundCurrent = isMobile ? 0 : 1;

    let pages = [];
    pages.push(1);
    if (currentPage > 2 + pagesAroundCurrent) pages.push("...");

    const start = Math.max(2, currentPage - pagesAroundCurrent);
    const end = Math.min(lastPage - 1, currentPage + pagesAroundCurrent);
    for (let i = start; i <= end; i++) {
        if (i !== 1 && i !== lastPage) pages.push(i);
    }

    if (currentPage < lastPage - 1 - pagesAroundCurrent) pages.push("...");
    if (lastPage > 1 && lastPage !== 1) pages.push(lastPage);

    let html = '<ul class="pagination">';

    html += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`;

    pages.forEach((page) => {
        if (page === "...") {
            html +=
                '<li class="page-item page-item-ellipsis disabled"><span class="page-link page-link-ellipsis">...</span></li>';
        } else {
            html += `<li class="page-item ${currentPage === page ? "active" : ""}">
                <a class="page-link" href="#" data-page="${page}">${page}</a>
            </li>`;
        }
    });

    html += `<li class="page-item ${currentPage === lastPage ? "disabled" : ""}">
        <a class="page-link" href="#" data-page="${currentPage + 1}">
            <span>Next</span>
            <i data-lucide="chevron-right" class="pagination-icon"></i>
        </a>
    </li>`;

    html += "</ul>";
    context.paginationContainer.innerHTML = html;

    context.paginationContainer
        .querySelectorAll(".page-link")
        .forEach((link) => {
            if (link.closest(".page-item-ellipsis")) return;
            link.addEventListener("click", (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute("data-page"));
                if (page >= 1 && page <= lastPage && page !== currentPage) {
                    import("./search-pagination.js").then((module) => {
                        module.fetchData(context, page);
                    });
                }
            });
        });

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}
