/**
 * Anak Asuh Card Renderer Module
 *
 * Menggantikan table-renderer.js untuk menampilkan data sebagai card
 * Setiap card berisi: foto, nama, status, dan action buttons
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
        context.anakAsuhContainer.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
    } else {
        context.searchLoader.classList.remove("active");
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
                <i data-lucide="refresh-ccw" class="btn-icon"></i>
                Muat Ulang
            </button>
        </div>
    `;

    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

// ==================== UPDATED: Two Conditions for Empty State with Green Link ====================
/**
 * Show empty state with two different conditions:
 * 
 * Condition 1: No data available at all (total count = 0)
 * - Shows: "Data belum tersedia"
 * - Encourages user to add new data with green link
 * 
 * Condition 2: Search performed but no results found (search term exists but total count = 0)
 * - Shows: "Pencarian tidak menghasilkan hasil"
 * - Suggests trying different search terms
 */
export function showEmptyState(context, hasSearchTerm = false, totalCount = 0) {
    // Condition 1: No data available at all (totalCount = 0 and no search)
    if (totalCount === 0 && !hasSearchTerm) {
        context.anakAsuhContainer.innerHTML = `
            <div class="empty-state">
                <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <path d="M20 8v6M23 11h-6"></path>
                </svg>
                <h4 class="empty-title">Data Belum Tersedia</h4>
                <p class="empty-text">Belum ada data anak asuh yang terdaftar di sistem.</p>
                <div class="empty-actions">
                    <a href="${context.baseUrl}/anak-asuh/create" class="empty-link">
                        Tambah Data Anak Asuh
                    </a>
                </div>
            </div>
        `;
    }
    // Condition 2: Search performed but no results found
    else {
        context.anakAsuhContainer.innerHTML = `
            <div class="empty-state">
                <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <path d="M20 8v6M23 11h-6"></path>
                </svg>
                <h4 class="empty-title">Tidak Ada Data Ditemukan</h4>
                <p class="empty-text">Pencarian tidak menghasilkan hasil. Coba gunakan kata kunci yang berbeda.</p>
            </div>
        `;
    }
}

// ==================== CARD RENDERING FUNCTIONS ====================
export function renderAnakAsuhCards(context, data, firstItem, totalCount = 0) {
    if (data.length === 0) {
        // Check if there's a search term to determine which empty state to show
        const searchInput = document.getElementById('searchInput');
        const hasSearchTerm = searchInput && searchInput.value.trim() !== '';
        
        showEmptyState(context, hasSearchTerm, totalCount);
        return;
    }

    let cardsHtml = "";

    data.forEach((anakAsuh, index) => {
        const itemNumber = firstItem + index;

        // Ambil foto default jika tidak ada
        const fotoUrl = anakAsuh.foto_url || "/images/default-anak-asuh.jpg";

        // Tentukan kelas status
        const statusClass =
            anakAsuh.status === "aktif" ? "status-aktif" : "status-nonaktif";
        const statusText = anakAsuh.status === "aktif" ? "Aktif" : "Non-Aktif";

        cardsHtml += `
            <div class="anak-asuh-card" data-id="${anakAsuh.id}">
                <div class="card-header">
                    <span class="item-number">#${itemNumber}</span>
                    <div class="status-badge ${statusClass}">${statusText}</div>
                </div>
                
                <div class="card-image">
                    <img src="${escapeHtml(fotoUrl)}" alt="Foto ${escapeHtml(anakAsuh.nama_anak)}" 
                        onerror="this.src='/images/default-anak-asuh.jpg'">
                </div>
                
                <div class="card-content">
                    <h3 class="card-title">${escapeHtml(anakAsuh.nama_anak)}</h3>
                    <div class="card-info">
                        <p><strong>NIK:</strong> ${escapeHtml(anakAsuh.nik)}</p>
                        <p><strong>Grade:</strong> ${escapeHtml(anakAsuh.grade)}</p>
                        <p><strong>Rumah Harapan:</strong> ${escapeHtml(anakAsuh.rh_kode)}</p>
                        <p><strong>Tanggal Lahir:</strong> ${escapeHtml(anakAsuh.tanggal_lahir)}</p>
                    </div>
                </div>
                
                <div class="card-actions">
                    <button type="button" class="btn-anak-asuh-view" 
                            data-anak-asuh-id="${anakAsuh.id}"
                            title="Lihat Detail">
                        <i data-lucide="eye" class="action-icon"></i>
                        Lihat
                    </button>
                    <button type="button" class="btn-anak-asuh-edit"
                            data-anak-asuh-id="${anakAsuh.id}"
                            title="Edit Data">
                        <i data-lucide="edit" class="action-icon"></i>
                        Edit
                    </button>
                    <button type="button" class="btn-anak-asuh-delete"
                            data-anak-asuh-id="${anakAsuh.id}"
                            data-anak-asuh-name="${escapeHtml(anakAsuh.nama_anak)}"
                            title="Hapus Permanen">
                        <i data-lucide="trash-2" class="action-icon"></i>
                        Hapus
                    </button>
                    
                    <!-- Form delete hidden -->
                    <form id="delete-form-${anakAsuh.id}" method="POST" 
                        action="${context.baseUrl}/anak-asuh/${anakAsuh.id}" style="display:none;">
                        <input type="hidden" name="_token" value="${context.csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                    </form>
                </div>
            </div>
        `;
    });

    context.anakAsuhContainer.innerHTML = cardsHtml;

    // Re-init Lucide icons
    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}

// Render pagination UI (reuse dari users)
export function renderPagination(context, currentPage, lastPage) {
    if (lastPage <= 1) {
        context.paginationContainer.style.display = "none";
        return;
    }
    context.paginationContainer.style.display = "block";

    let pages = [];
    if (lastPage <= 7) {
        for (let i = 1; i <= lastPage; i++) pages.push(i);
    } else {
        pages.push(1);
        if (currentPage > 4) pages.push("...");
        const start = Math.max(2, currentPage - 2);
        const end = Math.min(lastPage - 1, currentPage + 2);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage < lastPage - 3) pages.push("...");
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
            html +=
                '<li class="page-item disabled"><span class="page-link">...</span></li>';
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

    // Attach click handlers
    context.paginationContainer
        .querySelectorAll(".page-link")
        .forEach((link) => {
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

    // Re-init Lucide icons
    if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
    }
}