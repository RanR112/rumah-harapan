/**
 * Profile Page - Alert Modal Handler Only
 *
 * File ini HANYA menangani:
 * - Alert modal initialization
 * - Query parameter detection (success/error/password_changed)
 * - Auto-logout setelah 5 detik jika password diubah
 *
 * Reuse fungsi dari users/modules/alert-modal.js
 */

import {
    initAlertModal,
    showSuccessAlert,
    showErrorAlert,
    waitForPageTransition,
} from "../../components/alert-modal";

// Setup minimal context (hanya untuk alert modal)
const context = {
    alertModal: document.getElementById("alertModal"),
    alertTitle: document.getElementById("alertModalTitle"),
    alertMessage: document.getElementById("alertModalMessage"),
    alertCancelBtn: document.getElementById("alertCancelBtn"),
    alertConfirmBtn: document.getElementById("alertConfirmBtn"),
    alertContainer: document.querySelector(".alert-modal-container"),
    alertIcon: document.querySelector(".alert-icon"),
    csrfToken: window.userData.csrfToken,
    baseUrl: window.userData.baseUrl,
};

// ==================== QUERY PARAMETER HANDLING ====================
function checkQueryParamsForAlerts() {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get("success");
    const errorMessage = urlParams.get("error");
    const passwordChanged = urlParams.get("password_changed");

    waitForPageTransition(() => {
        
        if (passwordChanged === "1") {
            // Tampilkan alert dengan pesan khusus
            showSuccessAlert(
                context,
                "Data dan password berhasil diubah. Silakan login ulang dengan password baru.",
                "Password Berhasil Diubah",
            );

            // PERBAIKAN: Logout dengan POST form (bukan GET redirect)
            setTimeout(() => {
                // Buat form logout secara dinamis
                const logoutForm = document.createElement('form');
                logoutForm.method = 'POST';
                logoutForm.action = `${context.baseUrl}/logout`;
                
                // Tambahkan CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = context.csrfToken;
                logoutForm.appendChild(csrfInput);
                
                // Tambahkan _method jika diperlukan
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'POST';
                logoutForm.appendChild(methodInput);
                
                // Append ke body dan submit
                document.body.appendChild(logoutForm);
                logoutForm.submit();
            }, 5000);

            // Bersihkan URL
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("password_changed");
            window.history.replaceState({}, "", newUrl.toString());

            return;
        }

        // Handle success message
        if (successMessage) {
            showSuccessAlert(context, successMessage);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.delete("success");
            window.history.replaceState({}, "", newUrl.toString());
        }

        // Handle error message
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
    initAlertModal(context);
    checkQueryParamsForAlerts();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}