/**
 * Rumah Harapan Table Renderer Module
 */

export function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

export function showLoading(state, context, showSearchLoader = false) {
    if (state) {
        if (showSearchLoader) context.searchLoader?.classList.add("active");
        context.tableContainer.innerHTML = `
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
    context.tableContainer.innerHTML = `
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
                        <i data-lucide="refresh-ccw" class="btn-icon"></i>
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

export function showEmptyState(context, hasFilters = false) {
    let icon, title, text;
    const isAdmin = context.userRole === "admin";

    if (!hasFilters) {
        // Kondisi 1: Belum ada data sama sekali
        icon = `
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                <path d="M10 6h4"></path>
                <path d="M10 10h4"></path>
                <path d="M10 14h4"></path>
                <path d="M10 18h4"></path>
            </svg>
        `;
        title = "Belum ada data asrama";
        text = "Belum ada data asrama yang terdaftar di sistem.";
    } else {
        // Kondisi 2: Search/filter tidak menemukan hasil
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
    }

    context.tableContainer.innerHTML = `
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                    ${icon}
                    <h4 class="empty-title">${title}</h4>
                    <p class="empty-text">${text}</p>
                    ${
                        !hasFilters && isAdmin
                            ? `
                    <div class="empty-actions">
                        <a href="${context.baseUrl}/rumah-harapan/create" class="empty-link">
                            Tambah Data Asrama
                        </a>
                    </div>`
                            : ""
                    }
                </div>
            </td>
        </tr>
    `;
}

/**
 * Render tombol aksi berdasarkan role:
 * - admin  : tombol edit + delete
 * - petugas: tombol show (detail)
 */
function renderActionButtons(context, rumahHarapan) {
    if (context.userRole === "admin") {
        return `
            <button type="button" class="btn-rumah-harapan-edit"
                    data-rumah-harapan-id="${rumahHarapan.id}"
                    title="Edit Data">
                <i data-lucide="edit" class="action-icon"></i>
            </button>
            <button type="button" class="btn-rumah-harapan-delete"
                    data-rumah-harapan-id="${rumahHarapan.id}"
                    data-rumah-harapan-name="${escapeHtml(rumahHarapan.nama)}"
                    title="Hapus Permanen">
                <i data-lucide="trash-2" class="action-icon"></i>
            </button>
            <form id="delete-form-${rumahHarapan.id}" method="POST"
                action="${context.baseUrl}/rumah-harapan/${rumahHarapan.id}" style="display:none;">
                <input type="hidden" name="_token" value="${context.csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="current_page" value="${context.currentPage}">
            </form>
        `;
    }

    // Petugas: hanya tombol show/detail
    return `
        <a href="${context.baseUrl}/rumah-harapan/${rumahHarapan.id}"
            class="btn-rumah-harapan-show"
            title="Lihat Detail">
            <i data-lucide="eye" class="action-icon"></i>
        </a>
    `;
}

export function renderRumahHarapanTable(
    context,
    data,
    firstItem,
    totalCount = 0,
) {
    if (data.length === 0) {
        const searchValue = context.searchInput?.value.trim() || "";
        const statusValue =
            document.getElementById("statusFilterValue")?.value.trim() || "";
        const hasFilters = searchValue !== "" || statusValue !== "";
        showEmptyState(context, hasFilters);
        return;
    }

    let tableRows = "";

    data.forEach((rumahHarapan, index) => {
        const itemNumber = firstItem + index;
        const statusClass = rumahHarapan.is_active
            ? "status-active"
            : "status-inactive";
        const statusText = rumahHarapan.is_active ? "Aktif" : "Non-Aktif";

        tableRows += `
            <tr class="rumah-harapan-row" data-id="${rumahHarapan.id}">
                <td class="rumah-harapan-cell">
                    <span class="item-number">${itemNumber}</span>
                </td>
                <td class="rumah-harapan-cell rh-kode-cell" title="${escapeHtml(rumahHarapan.kode)}">
                    <span class="rumah-harapan-kode">${escapeHtml(rumahHarapan.kode)}</span>
                </td>
                <td class="rumah-harapan-cell rh-nama-cell" title="${escapeHtml(rumahHarapan.nama)}">
                    <span class="rumah-harapan-nama">${escapeHtml(rumahHarapan.nama)}</span>
                </td>
                <td class="rumah-harapan-cell rh-status-cell">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td class="rumah-harapan-cell rh-alamat-cell" title="${escapeHtml(rumahHarapan.alamat)}">
                    ${escapeHtml(rumahHarapan.alamat)}
                </td>
                <td class="rumah-harapan-cell rumah-harapan-actions">
                    ${renderActionButtons(context, rumahHarapan)}
                </td>
            </tr>
        `;
    });

    context.tableContainer.innerHTML = tableRows;

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
    if (lastPage > 1) pages.push(lastPage);

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
