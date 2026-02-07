/**
 * Search & Pagination Module
 * Menangani:
 * - AJAX search dengan debounce
 * - Pagination navigation
 * - URL state management
 */

import { renderTable, renderPagination, showLoading, showError } from './table-renderer.js';
import { attachActionButtonsListeners } from '../index.js';

// Fetch data dari server
export async function fetchData(context, page = 1) {
    context.currentPage = page;
    const searchValue = context.searchInput.value.trim();
    
    // Show loading
    showLoading(true, context);
    
    try {
        const url = new URL(`${context.baseUrl}/users`);
        url.searchParams.append('search', searchValue);
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
        
        // Render table dan pagination
        renderTable(context, data.data, data.first_item);
        renderPagination(context, data.current_page, data.last_page);
        attachActionButtonsListeners();
        
        // Update URL
        const newUrl = new URL(`${context.baseUrl}/users`);
        if (searchValue) newUrl.searchParams.append('search', searchValue);
        if (page > 1) newUrl.searchParams.append('page', page);
        window.history.pushState({ path: newUrl.toString() }, '', newUrl.toString());
        
    } catch (error) {
        console.error('Fetch error:', error);
        showError('Gagal memuat data. Silakan coba lagi.', context);
    } finally {
        showLoading(false, context);
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