/**
 * Anak Asuh Management Page - Main Entry Point
 * 
 * Struktur modular:
 * - Search & Pagination: modules/search-pagination.js  
 * - Card Rendering: modules/card-renderer.js
 * - Alert Modal: components/alert-modal.js (global)
 */

import { 
    initAlertModal,
    showSuccessAlert,
    showErrorAlert,
    waitForPageTransition
} from '../../components/alert-modal.js';

import { setupSearchHandler, fetchData } from './modules/search-pagination.js';

// Setup context untuk anak asuh
const context = {
    // DOM Elements
    searchInput: document.getElementById('searchInput'),
    searchLoader: document.getElementById('searchLoader'),
    anakAsuhContainer: document.getElementById('anakAsuhContainer'), // Container untuk cards
    paginationContainer: document.getElementById('paginationContainer'),
    alertModal: document.getElementById('alertModal'),
    alertTitle: document.getElementById('alertModalTitle'),
    alertMessage: document.getElementById('alertModalMessage'),
    alertCancelBtn: document.getElementById('alertCancelBtn'),
    alertConfirmBtn: document.getElementById('alertConfirmBtn'),
    alertContainer: document.querySelector('.alert-modal-container'),
    alertIcon: document.querySelector('.alert-icon'),
    
    // State & Config  
    searchTimeout: null,
    currentPage: 1,
    perPage: 12, // Lebih banyak karena card layout
    csrfToken: window.userData.csrfToken,
    baseUrl: window.userData.baseUrl
};

// ==================== ANAK ASUH-SPECIFIC: Attach Action Buttons Listeners ====================
function attachActionButtonsListeners() {
    // Delete button di card anak asuh
    document.querySelectorAll('.btn-anak-asuh-delete').forEach(button => {
        button.addEventListener('click', function() {
            const anakAsuhId = this.dataset.anakAsuhId;
            const anakAsuhName = this.dataset.anakAsuhName;
            
            // Tampilkan alert modal konfirmasi
            const alertModal = context.alertModal;
            const alertTitle = context.alertTitle;
            const alertMessage = context.alertMessage;
            const alertConfirmBtn = context.alertConfirmBtn;
            const alertCancelBtn = context.alertCancelBtn;
            
            alertTitle.textContent = 'Konfirmasi Hapus Permanen';
            alertMessage.textContent = `PERINGATAN: Hapus permanen data anak asuh "${anakAsuhName}"? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
            alertConfirmBtn.textContent = 'Hapus Permanen';
            alertConfirmBtn.className = 'alert-btn alert-btn-confirm';
            alertConfirmBtn.onclick = () => {
                document.getElementById(`delete-form-${anakAsuhId}`).submit();
            };
            alertCancelBtn.style.display = 'block';
            
            alertModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // View button di card anak asuh  
    document.querySelectorAll('.btn-anak-asuh-view').forEach(button => {
        button.addEventListener('click', function() {
            const anakAsuhId = this.dataset.anakAsuhId;
            window.location.href = `${context.baseUrl}/anak-asuh/${anakAsuhId}`;
        });
    });
    
    // Edit button di card anak asuh
    document.querySelectorAll('.btn-anak-asuh-edit').forEach(button => {
        button.addEventListener('click', function() {
            const anakAsuhId = this.dataset.anakAsuhId;
            window.location.href = `${context.baseUrl}/anak-asuh/${anakAsuhId}/edit`;
        });
    });
}

// ==================== GLOBAL: Check Query Params For Alerts ====================
function checkQueryParamsForAlerts() {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get("success");
    const errorMessage = urlParams.get("error");

    waitForPageTransition(() => {
        if (successMessage) {
            showSuccessAlert(context, successMessage);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("success");
            window.history.replaceState({}, "", newUrl.toString());
        }

        if (errorMessage) {
            showErrorAlert(context, errorMessage);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("error");
            window.history.replaceState({}, "", newUrl.toString());
        }
    });
}

// ==================== INITIALIZATION ====================
function init() {
    'use strict';

    // Setup alert modal
    initAlertModal(context);
    
    // Setup search handler
    setupSearchHandler(context);
    
    // Setup action buttons listeners
    attachActionButtonsListeners();
    
    // Check query params untuk alert
    checkQueryParamsForAlerts();
    
    // Setup browser back/forward button handler
    window.addEventListener('popstate', (e) => {
        const params = new URLSearchParams(window.location.search);
        const page = parseInt(params.get('page')) || 1;
        const search = params.get('search') || '';
        
        if (context.searchInput) context.searchInput.value = search;
        fetchData(context, page);
    });
    
    // Initial data load
    const params = new URLSearchParams(window.location.search);
    const initialPage = parseInt(params.get('page')) || 1;
    const initialSearch = params.get('search') || '';
    
    if (context.searchInput) context.searchInput.value = initialSearch;
    fetchData(context, initialPage);
}

// Start initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}