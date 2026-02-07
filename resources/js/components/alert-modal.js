/**
 * Alert Modal
 * Menggabungkan:
 * - Alert modal logic (success/error/confirm)
 * - Event handlers untuk tombol delete dan modal
 * - Page transition integration
 */

// ==================== ALERT MODAL FUNCTIONS ====================
export function resetAlertModal(context) {
    context.alertContainer.dataset.alertType = 'confirm';
    context.alertIcon.className = 'alert-icon alert-icon--confirm';
    context.alertIcon.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
            </path>
        </svg>
    `;
    context.alertCancelBtn.style.display = 'block';
    context.alertConfirmBtn.className = 'alert-btn alert-btn-confirm';
    context.alertConfirmBtn.textContent = 'Hapus';
}

export function closeAlertModal(context) {
    context.alertModal.style.display = 'none';
    document.body.style.overflow = '';
    
    if (context.alertConfirmBtn._autoCloseTimer) {
        clearTimeout(context.alertConfirmBtn._autoCloseTimer);
        delete context.alertConfirmBtn._autoCloseTimer;
    }
    
    resetAlertModal(context);
}

export function showSuccessAlert(context, message, title = 'Berhasil!') {
    context.alertContainer.dataset.alertType = 'success';
    context.alertIcon.className = 'alert-icon alert-icon--success';
    context.alertIcon.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    `;
    
    context.alertTitle.textContent = title;
    context.alertMessage.textContent = message;
    
    context.alertCancelBtn.style.display = 'none';
    context.alertConfirmBtn.textContent = 'Tutup';
    context.alertConfirmBtn.className = 'alert-btn alert-btn-close';
    context.alertConfirmBtn.onclick = () => closeAlertModal(context);
    
    context.alertModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    const autoCloseTimer = setTimeout(() => {
        if (context.alertModal.style.display === 'flex') {
            closeAlertModal(context);
        }
    }, 5000);
    
    context.alertConfirmBtn._autoCloseTimer = autoCloseTimer;
}

export function showErrorAlert(context, message, title = 'Terjadi Kesalahan') {
    context.alertContainer.dataset.alertType = 'error';
    context.alertIcon.className = 'alert-icon alert-icon--error';
    context.alertIcon.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    `;
    
    context.alertTitle.textContent = title;
    context.alertMessage.textContent = message;
    
    context.alertCancelBtn.style.display = 'none';
    context.alertConfirmBtn.textContent = 'Tutup';
    context.alertConfirmBtn.className = 'alert-btn alert-btn-error';
    context.alertConfirmBtn.onclick = () => closeAlertModal(context);
    
    context.alertModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Init alert modal event listeners
export function initAlertModal(context) {
    context.alertCancelBtn.addEventListener('click', () => closeAlertModal(context));
    context.alertModal.addEventListener('click', (e) => {
        if (e.target === context.alertModal) closeAlertModal(context);
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && context.alertModal.style.display === 'flex') {
            closeAlertModal(context);
        }
    });
}

// Wait for page transition selesai
export function waitForPageTransition(callback) {
    const loader1 = document.getElementById('dashboardTransitionLoader');
    const loader2 = document.getElementById('transition-loader');
    const loader = loader1 || loader2;
    
    const isActive = loader && (
        loader.classList.contains('dashboard-transition-loader--active') || 
        loader.style.display !== 'none'
    );
    
    if (isActive) {
        setTimeout(callback, 1700);
    } else {
        callback();
    }
}