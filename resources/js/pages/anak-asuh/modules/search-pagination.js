/**
 * Search & Pagination Module for Anak Asuh
 * 
 * Handle AJAX search, pagination, and filter application
 * Supports both desktop (real-time filter) and mobile (modal filter) modes
 */

import { renderAnakAsuhCards, renderPagination } from './card-renderer.js';

// Global state untuk tracking filter mode
let isMobileMode = false;

// Check if we're in mobile mode based on screen size
function checkMobileMode() {
    isMobileMode = window.innerWidth <= 480;
    return isMobileMode;
}

// Fetch data dari server
export async function fetchData(context, page = 1) {
    context.currentPage = page;
    
    // Get current filter values from DOM inputs (works for both modes)
    const searchValue = context.searchInput?.value.trim() || '';
    const statusValue = document.getElementById('statusFilter')?.value || '';
    const gradeValue = document.getElementById('gradeFilter')?.value || '';
    const rhValue = document.getElementById('rhFilter')?.value || '';
    
    // Show loading
    import('./card-renderer.js').then(renderer => {
        renderer.showLoading(true, context);
    });
    
    try {
        const url = new URL(`${context.baseUrl}/anak-asuh`);
        url.searchParams.append('search', searchValue);
        url.searchParams.append('status', statusValue);
        url.searchParams.append('grade', gradeValue);
        url.searchParams.append('rh', rhValue);
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
        
        // Render cards dan pagination
        renderAnakAsuhCards(context, data.data, data.first_item, data.total);
        renderPagination(context, data.current_page, data.last_page);
        
        // Update URL
        const newUrl = new URL(`${context.baseUrl}/anak-asuh`);
        if (searchValue) newUrl.searchParams.append('search', searchValue);
        if (statusValue) newUrl.searchParams.append('status', statusValue);
        if (gradeValue) newUrl.searchParams.append('grade', gradeValue);
        if (rhValue) newUrl.searchParams.append('rh', rhValue);
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
}

// Setup filter handlers (Desktop mode only - real-time)
export function setupFilterHandlers(context) {
    // Only setup desktop filter handlers if not in mobile mode
    if (checkMobileMode()) return;
    
    const statusFilter = document.getElementById('statusFilter');
    const gradeFilter = document.getElementById('gradeFilter');
    const rhFilter = document.getElementById('rhFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            // In desktop mode, apply filter immediately
            fetchData(context, 1);
        });
    }
    
    if (gradeFilter) {
        gradeFilter.addEventListener('change', () => {
            // In desktop mode, apply filter immediately
            fetchData(context, 1);
        });
    }
    
    if (rhFilter) {
        rhFilter.addEventListener('change', () => {
            // In desktop mode, apply filter immediately
            fetchData(context, 1);
        });
    }
}

// Check and handle mobile mode
export function checkAndHandleMobileMode(context) {
    const wasMobileMode = isMobileMode;
    const currentIsMobile = checkMobileMode();
    
    // If switching from desktop to mobile or vice versa, we might need to adjust
    if (wasMobileMode !== currentIsMobile) {
        // Re-setup filter handlers based on new mode
        if (!currentIsMobile) {
            // Switching to desktop - setup real-time filters
            setupFilterHandlers(context);
        }
        // Switching to mobile - no real-time filters needed
    }
}