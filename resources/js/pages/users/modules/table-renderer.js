/**
 * Table Renderer Module untuk Users
 */

// ==================== HELPER FUNCTIONS ====================
export function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

export function showLoading(state, context) {
    if (state) {
        context.searchLoader.classList.add("active");
        context.tableBody.innerHTML = `
            <tr class="table-row">
                <td colspan="5" class="text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;
    } else {
        context.searchLoader.classList.remove("active");
    }
}

export function showError(message, context) {
    context.tableBody.innerHTML = `
        <tr class="table-row">
            <td colspan="5" class="text-center py-5">
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

// ==================== EMPTY STATE DENGAN 2 KONDISI ====================
export function showEmptyState(context, hasSearch = false) {
    let icon, title, text;

    if (hasSearch) {
        // Kondisi 2: Search aktif tapi tidak ada hasil
        icon = `
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
                <path d="m14 8-6 6"></path>
                <path d="m8 8 6 6"></path>
            </svg>
        `;
        title = "Data tidak ditemukan";
        text = "Coba gunakan kata kunci nama atau email yang berbeda.";
    } else {
        // Kondisi 1: Belum ada data sama sekali
        icon = `
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <path d="M20 8v6M23 11h-6"></path>
            </svg>
        `;
        title = "Belum ada pengguna";
        text = "Belum ada data pengguna yang terdaftar di sistem.";
    }

    context.tableBody.innerHTML = `
        <tr class="table-row">
            <td colspan="5" class="text-center py-5">
                <div class="empty-state">
                    ${icon}
                    <h4 class="empty-title">${title}</h4>
                    <p class="empty-text">${text}</p>
                    ${
                        !hasSearch
                            ? `
                    <div class="empty-actions">
                        <a href="${context.baseUrl}/users/create" class="empty-link">
                            Tambah Pengguna
                        </a>
                    </div>`
                            : ""
                    }
                </div>
            </td>
        </tr>
    `;
}

// ==================== TABLE RENDERING FUNCTIONS ====================
export function renderTable(context, data, firstItem) {
    const hasSearch = (context.searchInput?.value.trim() || "") !== "";

    if (data.length === 0) {
        showEmptyState(context, hasSearch);
        return;
    }

    context.tableBody.innerHTML = "";

    data.forEach((user, index) => {
        const rowNumber = firstItem + index;
        const roleClass = user.role === "admin" ? "admin" : "petugas";
        const roleName = user.role.charAt(0).toUpperCase() + user.role.slice(1);

        const row = document.createElement("tr");
        row.className = "table-row";

        row.innerHTML = `
            <td class="text-center">${rowNumber}</td>
            <td class="user-name-cell" title="${escapeHtml(user.name)}">${escapeHtml(user.name)}</td>
            <td class="user-email-cell" title="${escapeHtml(user.email)}">${escapeHtml(user.email)}</td>
            <td class="user-role-cell">
                <span class="badge badge-${roleClass}">
                    ${escapeHtml(roleName)}
                </span>
            </td>
            <td class="action-cell">
                ${renderActionButtons(context, user)}
            </td>
        `;

        context.tableBody.appendChild(row);
    });

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

export function renderActionButtons(context, user) {
    return `
        <a href="${context.baseUrl}/users/${user.id}/edit" class="action-btn action-btn-edit" title="Edit">
            <i data-lucide="edit" class="action-icon"></i>
        </a>
        <button type="button" class="action-btn action-btn-delete btn-hard-delete"
            data-user-id="${user.id}"
            data-user-name="${escapeHtml(user.name)}"
            title="Hapus Permanen">
            <i data-lucide="trash-2" class="action-icon"></i>
        </button>
        <form id="hard-delete-form-${user.id}" method="POST" action="${context.baseUrl}/users/${user.id}" style="display:none;">
            <input type="hidden" name="_token" value="${context.csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="current_page" value="${context.currentPage}">
        </form>
    `;
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
