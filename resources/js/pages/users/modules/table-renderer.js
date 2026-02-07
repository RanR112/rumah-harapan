/**
 * Table Renderer Module
 * Menggabungkan:
 * - Table rendering logic
 * - Helper functions (escapeHtml, showLoading, dll)
 */

// ==================== HELPER FUNCTIONS (MERGED FROM helpers.js) ====================
export function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

export function showLoading(state, context) {
    if (state) {
        context.searchLoader.classList.add('active');
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
        context.searchLoader.classList.remove('active');
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
    
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

export function showEmptyState(context) {
    context.tableBody.innerHTML = `
        <tr class="table-row">
            <td colspan="5" class="text-center py-5">
                <div class="empty-state">
                    <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <path d="M20 8v6M23 11h-6"></path>
                    </svg>
                    <h4 class="empty-title">Tidak ada data ditemukan</h4>
                    <p class="empty-text">Pencarian tidak menghasilkan hasil.</p>
                </div>
            </td>
        </tr>
    `;
}

// ==================== TABLE RENDERING FUNCTIONS ====================
export function renderTable(context, data, firstItem) {
    if (data.length === 0) {
        showEmptyState(context);
        return;
    }
    
    context.tableBody.innerHTML = '';
    
    data.forEach((user, index) => {
        const rowNumber = firstItem + index;
        const roleClass = user.role === 'admin' ? 'admin' : 'petugas';
        const roleName = user.role.charAt(0).toUpperCase() + user.role.slice(1);
        
        const row = document.createElement('tr');
        row.className = 'table-row';
        
        row.innerHTML = `
            <td class="text-center">${rowNumber}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>
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
    
    // Re-init Lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

// Render action buttons (Edit + Hard Delete)
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
        </form>
    `;
}

// Render pagination UI
export function renderPagination(context, currentPage, lastPage) {
    if (lastPage <= 1) {
        context.paginationContainer.style.display = 'none';
        return;
    }
    context.paginationContainer.style.display = 'block';
    
    let pages = [];
    if (lastPage <= 7) {
        for (let i = 1; i <= lastPage; i++) pages.push(i);
    } else {
        pages.push(1);
        if (currentPage > 4) pages.push('...');
        const start = Math.max(2, currentPage - 2);
        const end = Math.min(lastPage - 1, currentPage + 2);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage < lastPage - 3) pages.push('...');
        pages.push(lastPage);
    }
    
    let html = '<ul class="pagination">';
    
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`;
    
    pages.forEach(page => {
        if (page === '...') {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        } else {
            html += `<li class="page-item ${currentPage === page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${page}">${page}</a>
            </li>`;
        }
    });
    
    html += `<li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage + 1}">
            <span>Next</span>
            <i data-lucide="chevron-right" class="pagination-icon"></i>
        </a>
    </li>`;
    
    html += '</ul>';
    
    context.paginationContainer.innerHTML = html;
    
    // Attach click handlers
    context.paginationContainer.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = parseInt(link.getAttribute('data-page'));
            if (page >= 1 && page <= lastPage && page !== currentPage) {
                import('./search-pagination.js').then(module => {
                    module.fetchData(context, page);
                });
            }
        });
    });
    
    // Re-init Lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}