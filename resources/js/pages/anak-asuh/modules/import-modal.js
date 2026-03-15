/**
 * Import Modal Module - OPTIMIZED
 * All DOM elements cached ONCE at module scope.
 * Zero redundant queries after initialization.
 */

// Config & Dependencies (injected from index.js)
let importUrl = null;
let csrfToken = null;
let showSuccessAlert = null;
let showErrorAlert = null;
let alertContext = null;
let reloadTable = null;

// CACHE DOM ELEMENTS (module scope - di-query sekali saja)
let modalElement = null;
let formElement = null;
let fileInputElement = null;
let fileNameSpanElement = null;
let fileErrorElement = null;
let submitButtonElement = null;
let closeModalBtnElement = null;
let cancelModalBtnElement = null;
let openImportBtnElement = null;

// Format Kolom Modal
let formatKolomBackdrop = null;
let openFormatKolomBtnElement = null;
let closeFormatKolomBtnElement = null;
let closeFormatKolomModalBtnElement = null;

/**
 * Initialize import modal with AJAX support.
 * All DOM elements cached ONCE here - no redundant queries later.
 */
export function setupImportModal(
    config,
    successAlertFn,
    errorAlertFn,
    context,
    reloadTableFn,
) {
    // Inject dependencies
    importUrl = config.importUrl ?? null;
    csrfToken = config.csrfToken ?? null;
    showSuccessAlert = successAlertFn;
    showErrorAlert = errorAlertFn;
    alertContext = context;
    reloadTable = reloadTableFn;

    // CACHE ALL DOM ELEMENTS ONCE
    openImportBtnElement = document.getElementById("openImportModalBtn");
    modalElement = document.getElementById("importModal");
    closeModalBtnElement = document.getElementById("closeImportModal");
    cancelModalBtnElement = document.getElementById("cancelImportModal");
    formElement = document.getElementById("importForm");
    submitButtonElement = document.getElementById("importSubmitBtn");
    fileInputElement = document.getElementById("importFile");
    fileNameSpanElement = document.getElementById("importFileName");
    fileErrorElement = document.getElementById("importFileError");

    // Format Kolom Modal elements
    formatKolomBackdrop = document.getElementById("formatKolomModal");
    openFormatKolomBtnElement = document.getElementById("openFormatKolomBtn");
    closeFormatKolomBtnElement = document.getElementById("closeFormatKolomBtn");
    closeFormatKolomModalBtnElement = document.getElementById(
        "closeFormatKolomModal",
    );

    if (!openImportBtnElement || !modalElement || !formElement) {
        console.warn("Import modal elements not found");
        return;
    }

    // File name display
    if (fileInputElement && fileNameSpanElement) {
        fileInputElement.addEventListener("change", (e) => {
            const file = e.target.files[0];
            fileNameSpanElement.textContent = file
                ? file.name
                : "Pilih file...";
            if (fileErrorElement) fileErrorElement.textContent = "";
        });
    }

    // ── Import Modal handlers ──
    openImportBtnElement.addEventListener("click", openImportModal);

    if (closeModalBtnElement)
        closeModalBtnElement.addEventListener("click", closeImportModal);
    if (cancelModalBtnElement)
        cancelModalBtnElement.addEventListener("click", closeImportModal);

    modalElement.addEventListener("click", (e) => {
        if (e.target === modalElement) closeImportModal();
    });

    formElement.addEventListener("submit", (e) => {
        e.preventDefault();
        handleImportSubmit();
    });

    // ── Format Kolom Modal handlers ──
    if (openFormatKolomBtnElement)
        openFormatKolomBtnElement.addEventListener(
            "click",
            openFormatKolomModal,
        );

    if (closeFormatKolomBtnElement)
        closeFormatKolomBtnElement.addEventListener(
            "click",
            closeFormatKolomModal,
        );

    if (closeFormatKolomModalBtnElement)
        closeFormatKolomModalBtnElement.addEventListener(
            "click",
            closeFormatKolomModal,
        );

    if (formatKolomBackdrop) {
        formatKolomBackdrop.addEventListener("click", (e) => {
            if (e.target === formatKolomBackdrop) closeFormatKolomModal();
        });
    }

    // ESC key — tutup format kolom modal dulu jika terbuka, baru import modal
    document.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        if (formatKolomBackdrop?.style.display === "flex") {
            closeFormatKolomModal();
        } else if (modalElement.style.display === "flex") {
            closeImportModal();
        }
    });
}

// ── Open/Close Import Modal ──────────────────────────────────
function openImportModal() {
    formElement.reset();
    if (fileNameSpanElement) fileNameSpanElement.textContent = "Pilih file...";
    clearImportErrors();
    modalElement.style.display = "flex";
    document.body.style.overflow = "hidden";
}

function closeImportModal() {
    modalElement.style.display = "none";
    document.body.style.overflow = "";
}

// ── Open/Close Format Kolom Modal ───────────────────────────
function openFormatKolomModal() {
    if (!formatKolomBackdrop) return;
    formatKolomBackdrop.style.display = "flex";
}

function closeFormatKolomModal() {
    if (!formatKolomBackdrop) return;
    formatKolomBackdrop.style.display = "none";
}

// ── Clear Errors ────────────────────────────────────────────
function clearImportErrors() {
    if (fileErrorElement) fileErrorElement.textContent = "";
}

// ── Client-side Validation ──────────────────────────────────
function validateImportForm() {
    let hasError = false;
    const file = fileInputElement.files[0];

    if (!file) {
        if (fileErrorElement)
            fileErrorElement.textContent = "File wajib dipilih";
        hasError = true;
    } else {
        if (file.size > 2 * 1024 * 1024) {
            if (fileErrorElement)
                fileErrorElement.textContent = "Ukuran file maksimal 2MB";
            hasError = true;
        }

        const mimeType = file.type.toLowerCase();
        const allowedTypes = [
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "text/csv",
            "application/csv",
            "application/octet-stream",
        ];

        const fileName = file.name.toLowerCase();
        const hasValidExtension =
            fileName.endsWith(".csv") ||
            fileName.endsWith(".xls") ||
            fileName.endsWith(".xlsx");

        if (!allowedTypes.includes(mimeType) && !hasValidExtension) {
            if (fileErrorElement)
                fileErrorElement.textContent =
                    "Format file harus CSV, XLS, atau XLSX";
            hasError = true;
        }
    }

    return !hasError;
}

// ── Handle Import Submit ────────────────────────────────────
function handleImportSubmit() {
    clearImportErrors();

    if (!validateImportForm()) return;

    const file = fileInputElement.files[0];
    const formData = new FormData();
    formData.append("file", file);
    formData.append("_token", csrfToken);

    setImportLoading(true, submitButtonElement);

    fetch(importUrl, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
    })
        .then(async (res) => {
            if (res.status === 422) {
                const data = await res.json();
                throw { validation: true, errors: data.errors || {} };
            }
            if (res.status >= 500) throw { serverError: true };
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then((data) => {
            // FULL SUCCESS
            if (
                data.success &&
                data.data?.success_count > 0 &&
                (data.data?.error_count === 0 || !data.data?.error_count)
            ) {
                closeImportModal();
                showSuccessAlert(
                    alertContext,
                    `Import berhasil (${data.data.success_count} data).`,
                );
                setTimeout(() => reloadTable(), 1500);
                return;
            }

            // PARTIAL SUCCESS
            if (
                data.data?.success_count > 0 &&
                data.data?.error_count > 0 &&
                data.data?.errors?.length > 0
            ) {
                closeImportModal();
                let errorSummary = `Import sebagian berhasil (${data.data.success_count} data).\n`;
                errorSummary += `${data.data.error_count} baris gagal:\n\n`;
                const maxErrors = Math.min(5, data.data.errors.length);
                for (let i = 0; i < maxErrors; i++) {
                    const err = data.data.errors[i];
                    errorSummary += `${i + 1}. ${err.row === 0 ? "Header" : `Baris ${err.row}`}: ${err.message}\n`;
                }
                if (data.data.errors.length > 5)
                    errorSummary += `\n... dan ${data.data.errors.length - 5} error lainnya`;
                showErrorAlert(alertContext, errorSummary);
                setTimeout(() => reloadTable(), 2000);
                return;
            }

            // NO DATA IMPORTED
            if (data.data?.success_count === 0 && data.data?.error_count > 0) {
                closeImportModal();
                let errorSummary = `Tidak ada data yang berhasil diimpor.\n`;
                errorSummary += `${data.data.error_count} baris gagal:\n\n`;
                const maxErrors = Math.min(5, data.data.errors.length);
                for (let i = 0; i < maxErrors; i++) {
                    const err = data.data.errors[i];
                    errorSummary += `${i + 1}. ${err.row === 0 ? "Header" : `Baris ${err.row}`}: ${err.message}\n`;
                }
                if (data.data.errors.length > 5)
                    errorSummary += `\n... dan ${data.data.errors.length - 5} error lainnya`;
                showErrorAlert(alertContext, errorSummary);
                setTimeout(() => reloadTable(), 2000);
                return;
            }

            // FALLBACK SUCCESS
            if (data.success) {
                closeImportModal();
                showSuccessAlert(alertContext, `Import berhasil.`);
                setTimeout(() => reloadTable(), 1500);
                return;
            }

            // FALLBACK ERROR
            closeImportModal();
            showErrorAlert(
                alertContext,
                "Terjadi kesalahan tak terduga saat mengimpor. Silakan coba lagi.",
            );
        })
        .catch((error) => {
            console.error("Import error:", error);
            closeImportModal();

            if (error.validation) {
                let validationErrors = "Validasi file gagal:\n\n";
                let hasErrors = false;
                Object.entries(error.errors).forEach(([, messages]) => {
                    hasErrors = true;
                    messages.forEach((msg) => {
                        validationErrors += `• ${msg}\n`;
                    });
                });
                showErrorAlert(
                    alertContext,
                    hasErrors
                        ? validationErrors
                        : "File tidak valid. Pastikan format kolom sesuai ketentuan.",
                );
                return;
            }

            if (error.serverError) {
                showErrorAlert(
                    alertContext,
                    "Server sedang mengalami gangguan. Silakan coba lagi beberapa saat lagi.",
                );
                return;
            }

            showErrorAlert(
                alertContext,
                error.message?.includes("Failed to fetch")
                    ? "Koneksi gagal. Periksa koneksi internet Anda."
                    : "Terjadi kesalahan saat mengimpor. Silakan coba lagi.",
            );
        })
        .finally(() => {
            setImportLoading(false, submitButtonElement);
        });
}

// ── Set Loading State ───────────────────────────────────────
function setImportLoading(isLoading, btn) {
    if (!btn) return;

    const btnText = btn.querySelector(".btn-text");
    const btnLoader = btn.querySelector(".btn-loader");

    if (!btnText || !btnLoader) {
        console.warn(
            "Button loader structure not found (.btn-text/.btn-loader)",
        );
        return;
    }

    if (isLoading) {
        btn.disabled = true;
        btnText.style.display = "none";
        btnLoader.style.display = "flex";
    } else {
        btn.disabled = false;
        btnText.style.display = "";
        btnLoader.style.display = "none";
    }
}
