/**
 * Anak Asuh Card Renderer Module
 */

export function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

export function showLoading(state, context, showSearchLoader = false) {
    if (state) {
        context.anakAsuhContainer.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
        if (showSearchLoader && context.searchLoader) {
            context.searchLoader.classList.add("active");
        }
    } else {
        if (context.searchLoader) {
            context.searchLoader.classList.remove("active");
        }
    }
}

export function showError(message, context) {
    context.anakAsuhContainer.innerHTML = `
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
    `;

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

export function showEmptyState(context, hasFilters = false, totalCount = 0) {
    if (!hasFilters && totalCount === 0) {
        context.anakAsuhContainer.innerHTML = `
            <div class="empty-state">
                <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <path d="M20 8v6M23 11h-6"></path>
                </svg>
                <h4 class="empty-title">Belum ada data anak asuh</h4>
                <p class="empty-text">Belum ada data anak asuh yang terdaftar di sistem.</p>
                <div class="empty-actions">
                    <a href="${context.baseUrl}/anak-asuh/create" class="empty-link">
                        Tambah Data Anak Asuh
                    </a>
                </div>
            </div>
        `;
    } else {
        context.anakAsuhContainer.innerHTML = `
            <div class="empty-state">
                <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                    <path d="m14 8-6 6"></path>
                    <path d="m8 8 6 6"></path>
                </svg>
                <h4 class="empty-title">Data tidak ditemukan</h4>
                <p class="empty-text">Coba gunakan kata kunci atau filter yang berbeda.</p>
            </div>
        `;
    }
}

function getIsActiveBadge(anakAsuh) {
    const isActive = anakAsuh.is_active === true || anakAsuh.is_active === 1;
    const activeClass = isActive ? "status-aktif" : "status-tidak-aktif";
    const activeText = isActive ? "Aktif" : "Tidak Aktif";
    return `<div class="status-badge ${activeClass}">${activeText}</div>`;
}

export function renderAnakAsuhCards(
    context,
    data,
    firstItem,
    totalCount = 0,
    hasFilters = false,
) {
    if (data.length === 0) {
        showEmptyState(context, hasFilters, totalCount);
        return;
    }

    let cardsHtml = "";

    data.forEach((anakAsuh) => {
        const fotoUrl = anakAsuh.foto_url || "/images/default-anak-asuh-L.png";
        const berkasCount = anakAsuh.berkas_count ?? 0;

        cardsHtml += `
            <div class="anak-asuh-card" data-id="${anakAsuh.id}">
                <div class="card-top-bar">
                    <div class="card-badges">
                        ${getIsActiveBadge(anakAsuh)}
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-anak-asuh-view"
                                data-anak-asuh-id="${anakAsuh.id}"
                                title="Lihat Detail">
                            <i data-lucide="eye" class="action-icon"></i>
                        </button>
                        <button type="button" class="btn-anak-asuh-edit"
                                data-anak-asuh-id="${anakAsuh.id}"
                                title="Edit Data">
                            <i data-lucide="edit" class="action-icon"></i>
                        </button>
                        <button type="button" class="btn-anak-asuh-delete"
                                data-anak-asuh-id="${anakAsuh.id}"
                                data-anak-asuh-name="${escapeHtml(anakAsuh.nama_anak)}"
                                title="Hapus Permanen">
                            <i data-lucide="trash-2" class="action-icon"></i>
                        </button>
                        <form id="delete-form-${anakAsuh.id}" method="POST"
                            action="${context.baseUrl}/anak-asuh/${anakAsuh.id}" style="display:none;">
                            <input type="hidden" name="_token" value="${context.csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="current_page" value="${context.currentPage}">
                        </form>
                    </div>
                </div>
                <div class="card-foto">
                    <img src="${escapeHtml(fotoUrl)}"
                        alt="Foto ${escapeHtml(anakAsuh.nama_anak)}"
                        onerror="this.src='/images/default-anak-asuh-L.png'">
                </div>
                <div class="card-nama">
                    <h3>${escapeHtml(anakAsuh.nama_anak)}</h3>
                </div>
                <div class="card-meta">
                    <span class="card-grade">
                        <i data-lucide="layers" class="meta-icon"></i>
                        Grade ${escapeHtml(String(anakAsuh.grade))}
                    </span>
                    <span class="card-berkas">
                        <i data-lucide="file-text" class="meta-icon"></i>
                        ${berkasCount} Berkas
                    </span>
                </div>
            </div>
        `;
    });

    context.anakAsuhContainer.innerHTML = cardsHtml;

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
    const pagesAround = isMobile ? 0 : 2;

    let pages = [];
    if (lastPage <= 7) {
        for (let i = 1; i <= lastPage; i++) pages.push(i);
    } else {
        pages.push(1);
        if (currentPage - pagesAround > 2) pages.push("...");
        const start = Math.max(2, currentPage - pagesAround);
        const end = Math.min(lastPage - 1, currentPage + pagesAround);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage + pagesAround < lastPage - 1) pages.push("...");
        pages.push(lastPage);
    }

    let html = '<ul class="pagination">';

    html += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`;

    pages.forEach((page) => {
        if (page === "...") {
            html += `<li class="page-item page-item-ellipsis disabled">
                <span class="page-link">...</span>
            </li>`;
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
            link.addEventListener("click", (e) => {
                e.preventDefault();
                const parentLi = link.closest(".page-item");
                if (parentLi?.classList.contains("page-item-ellipsis")) return;
                const page = parseInt(link.getAttribute("data-page"));
                if (page >= 1 && page <= lastPage && page !== currentPage) {
                    import("./search-pagination.js").then((module) => {
                        module.fetchData(context, page, false);
                    });
                }
            });
        });

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}
