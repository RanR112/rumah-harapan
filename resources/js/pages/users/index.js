/**
 * User Management Page - Main Entry Point
 * 
 * File ini berisi:
 * - Setup context (DOM elements, state, config)
 * - Initialization logic (DOM ready, event listeners)
 * - Import dan panggil fungsi dari modul
 * 
 * Semua logic lain dipisahkan ke modul di folder /modules
 */

import { 
    initAlertModal,
    resetAlertModal,
    showSuccessAlert,
    showErrorAlert,
    closeAlertModal,
    waitForPageTransition
} from '../../components/alert-modal.js';

import { setupSearchHandler, fetchData } from './modules/search-pagination.js';

// Setup context sekali di sini
const context = {
    // DOM Elements
    searchInput: document.getElementById('searchInput'),
    searchLoader: document.getElementById('searchLoader'),
    tableBody: document.getElementById('usersTableBody'),
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
    perPage: 7,
    csrfToken: window.userData.csrfToken,
    baseUrl: window.userData.baseUrl
};

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

// ==================== USERS-SPECIFIC: Attach Action Buttons Listeners ====================
export function attachActionButtonsListeners() {
    // Hard Delete button
    document.querySelectorAll(".btn-hard-delete").forEach((button) => {
        button.addEventListener("click", function () {
            resetAlertModal(context);
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            context.alertTitle.textContent = "Konfirmasi Hapus Permanen";
            context.alertMessage.textContent = `PERINGATAN: Hapus permanen pengguna "${userName}"? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
            context.alertConfirmBtn.textContent = "Hapus Permanen";
            context.alertConfirmBtn.className = "alert-btn alert-btn-confirm";
            context.alertConfirmBtn.onclick = () => {
                document.getElementById(`hard-delete-form-${userId}`).submit();
            };

            context.alertModal.style.display = "flex";
            document.body.style.overflow = "hidden";
        });
    });
}

// ==================== INITIALIZATION ====================
function init() {
    'use strict';

    // Setup alert modal
    initAlertModal(context);
    
    // Setup search handler
    setupSearchHandler(context);
    
    // Setup action buttons listeners (users-specific)
    attachActionButtonsListeners();
    
    // Check query params untuk alert (success/error)
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