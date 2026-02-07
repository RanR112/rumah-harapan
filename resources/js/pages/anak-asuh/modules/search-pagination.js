/**
 * Search & Pagination Module for Anak Asuh
 * 
 * Reuse logic dari users, tapi panggil card renderer
 */

import { renderAnakAsuhCards, renderPagination } from './card-renderer.js';

// Fetch data dari server
export async function fetchData(context, page = 1) {
    context.currentPage = page;
    const searchValue = context.searchInput.value.trim();
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    const gradeFilter = document.getElementById('gradeFilter')?.value || '';
    const rhFilter = document.getElementById('rhFilter')?.value || '';

    // Show loading
    import('./card-renderer.js').then(renderer => {
        renderer.showLoading(true, context);
    });
    
    try {
        const url = new URL(`${context.baseUrl}/anak-asuh`);
        url.searchParams.append('search', searchValue);
        url.searchParams.append('status', statusFilter);
        url.searchParams.append('grade', gradeFilter);
        url.searchParams.append('rh', rhFilter);
        url.searchParams.append('page', page);
        url.searchParams.append('per_page', context.perPage);
        
        const response = await fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        
        // Render cards dengan total count
        renderAnakAsuhCards(context, data.data, data.first_item, data.total);
        renderPagination(context, data.current_page, data.last_page);
        
        // Update URL
        const newUrl = new URL(`${context.baseUrl}/anak-asuh`);
        if (searchValue) newUrl.searchParams.append('search', searchValue);
        if (statusFilter) newUrl.searchParams.append('status', statusFilter);
        if (gradeFilter) newUrl.searchParams.append('grade', gradeFilter);
        if (rhFilter) newUrl.searchParams.append('rh', rhFilter);
        if (page > 1) newUrl.searchParams.append('page', page);
        window.history.pushState({ path: newUrl.toString() }, '', newUrl.toString());
        
    } catch (error) {
        console.error('Fetch error:', error);
        import('./card-renderer.js').then(renderer => {
            renderer.showError('Gagal memuat data. Silakan coba lagi.', context);
        });
    } finally {
        import('./card-renderer.js').then(renderer => {
            renderer.showLoading(false, context);
        });
    }
}

// Setup search handler dengan debounce
export function setupSearchHandler(context) {
    if (!context.searchInput) return;
    
    context.searchInput.addEventListener('input', (e) => {
        clearTimeout(context.searchTimeout);
        context.searchTimeout = setTimeout(() => {
            fetchData(context, 1);
        }, 300);
    });
    
    // Setup filter handlers
    const statusFilter = document.getElementById('statusFilter');
    const gradeFilter = document.getElementById('gradeFilter');
    const rhFilter = document.getElementById('rhFilter');
    
    if (statusFilter) statusFilter.addEventListener('change', () => fetchData(context, 1));
    if (gradeFilter) gradeFilter.addEventListener('change', () => fetchData(context, 1));
    if (rhFilter) rhFilter.addEventListener('change', () => fetchData(context, 1));
}