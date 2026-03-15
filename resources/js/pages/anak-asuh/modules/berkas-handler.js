/**
 * Berkas Handler Module - OPTIMIZED
 * Handles document upload & deletion via AJAX with:
 * - Module-level DOM caching (zero redundant queries)
 * - Frontend-generated messages (no backend message dependency)
 * - Structured error handling (consistent with import-modal.js)
 */

// Config (cached at module scope)
let anakAsuhId = null;
let uploadUrl = null;
let deleteUrl = null;
let csrfToken = null;

// Alert functions (injected from index.js)
let showSuccessAlert = null;
let showErrorAlert = null;
let alertContext = null;

// DOM ELEMENTS CACHED AT MODULE SCOPE (ZERO REDUNDANT QUERIES)
let addBerkasBtnElement = null;
let modalElement = null;
let closeModalBtnElement = null;
let cancelModalBtnElement = null;
let formElement = null;
let uploadBtnElement = null;
let namaInputElement = null;
let fileInputElement = null;
let fileNameSpanElement = null;
let namaErrorElement = null;
let fileErrorElement = null;
let berkasListElement = null;

/**
 * Initialize berkas upload with AJAX support.
 * All DOM elements cached ONCE at module scope.
 */
export function setupBerkasUpload(
    config,
    successAlertFn,
    errorAlertFn,
    context,
) {
    // Inject dependencies
    showSuccessAlert = successAlertFn;
    showErrorAlert = errorAlertFn;
    alertContext = context;
    anakAsuhId = config.anakAsuhId ?? null;
    uploadUrl = config.uploadUrl ?? null;
    deleteUrl = config.deleteUrl ?? null;
    csrfToken = config.csrfToken ?? null;

    // CACHE ALL DOM ELEMENTS ONCE
    addBerkasBtnElement = document.getElementById("addBerkasBtn");
    modalElement = document.getElementById("modalUploadBerkas");
    closeModalBtnElement = document.getElementById("closeModalBerkas");
    cancelModalBtnElement = document.getElementById("cancelModalBerkas");
    formElement = document.getElementById("formUploadBerkas");
    uploadBtnElement = document.getElementById("uploadBerkasBtn");
    namaInputElement = document.getElementById("berkasNama");
    fileInputElement = document.getElementById("berkasFile");
    fileNameSpanElement = document.getElementById("berkasFileName");
    namaErrorElement = document.getElementById("berkasNamaError");
    fileErrorElement = document.getElementById("berkasFileError");
    berkasListElement = document.getElementById("berkasList");

    if (!addBerkasBtnElement || !modalElement) {
        console.warn("Berkas elements not found");
        return;
    }

    // Open modal
    addBerkasBtnElement.addEventListener("click", openBerkasModal);

    // Close modal handlers
    if (closeModalBtnElement)
        closeModalBtnElement.addEventListener("click", closeBerkasModal);
    if (cancelModalBtnElement)
        cancelModalBtnElement.addEventListener("click", closeBerkasModal);

    modalElement.addEventListener("click", (e) => {
        if (e.target === modalElement) closeBerkasModal();
    });

    // Update filename display
    if (fileInputElement && fileNameSpanElement) {
        fileInputElement.addEventListener("change", (e) => {
            const file = e.target.files[0];
            fileNameSpanElement.textContent = file
                ? file.name
                : "Pilih file...";
        });
    }

    // Upload berkas via AJAX
    if (uploadBtnElement) {
        uploadBtnElement.addEventListener("click", uploadBerkas);
    }

    // ESC key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && modalElement.style.display === "flex") {
            closeBerkasModal();
        }
    });
}

// ── Open Modal ─────────────────────────────────────────────────
function openBerkasModal() {
    formElement.reset();
    fileNameSpanElement.textContent = "Pilih file...";
    clearBerkasErrors();
    modalElement.style.display = "flex";
    document.body.style.overflow = "hidden";
}

// ── Close Modal ─────────────────────────────────────────────────
function closeBerkasModal() {
    modalElement.style.display = "none";
    document.body.style.overflow = "";
}

// ── Clear Errors ────────────────────────────────────────────────
function clearBerkasErrors() {
    if (namaErrorElement) namaErrorElement.textContent = "";
    if (fileErrorElement) fileErrorElement.textContent = "";
}

// ── Client-side Validation ──────────────────────────────────────
function validateBerkasForm() {
    let hasError = false;
    const nama = namaInputElement.value.trim();
    const file = fileInputElement.files[0];

    if (!nama) {
        if (namaErrorElement)
            namaErrorElement.textContent = "Nama berkas wajib diisi";
        hasError = true;
    }

    if (!file) {
        if (fileErrorElement)
            fileErrorElement.textContent = "File wajib dipilih";
        hasError = true;
    } else {
        if (file.size > 5 * 1024 * 1024) {
            if (fileErrorElement)
                fileErrorElement.textContent = "Ukuran file maksimal 5MB";
            hasError = true;
        }

        const allowedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "application/pdf",
        ];
        if (!allowedTypes.includes(file.type)) {
            if (fileErrorElement)
                fileErrorElement.textContent =
                    "Format file harus JPG, PNG, atau PDF";
            hasError = true;
        }
    }

    return !hasError;
}

// ── Upload Berkas via AJAX ──────────────────────────────────────
function uploadBerkas() {
    clearBerkasErrors();

    if (!validateBerkasForm()) {
        return;
    }

    const nama = namaInputElement.value.trim();
    const file = fileInputElement.files[0];
    const formData = new FormData();
    formData.append("file", file);
    formData.append("original_name", nama);
    formData.append("_token", csrfToken);

    setUploadLoading(true, uploadBtnElement);

    fetch(uploadUrl, {
        method: "POST",
        body: formData,
    })
        .then(async (res) => {
            if (res.status === 422) {
                const data = await res.json();
                throw { validation: true, errors: data.errors || {} };
            }
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then((data) => {
            if (data.success && data.berkas) {
                addBerkasToList(
                    data.berkas.id,
                    data.berkas.original_name,
                    data.berkas.file_url,
                );
                closeBerkasModal();
                showSuccessAlert(alertContext, "Berkas berhasil ditambahkan!");
            } else {
                closeBerkasModal();
                showSuccessAlert(alertContext, "Berkas berhasil ditambahkan!");
            }
        })
        .catch((error) => {
            console.error("Upload berkas error:", error);
            closeBerkasModal();

            if (error.validation) {
                let validationErrors = "Validasi gagal:\n\n";
                let hasErrors = false;
                Object.entries(error.errors).forEach(([field, messages]) => {
                    hasErrors = true;
                    messages.forEach((msg) => {
                        validationErrors += `• ${msg}\n`;
                    });
                });
                showErrorAlert(
                    alertContext,
                    hasErrors
                        ? validationErrors
                        : "File tidak valid. Pastikan format JPG, PNG, atau PDF.",
                );
                return;
            }

            showErrorAlert(
                alertContext,
                error.message?.includes("Failed to fetch")
                    ? "Koneksi gagal. Periksa koneksi internet Anda."
                    : "Gagal mengupload berkas. Pastikan format file JPG, PNG, atau PDF.",
            );
        })
        .finally(() => {
            setUploadLoading(false, uploadBtnElement);
        });
}

// ── Set Loading State ────────────────────────────────────────────
function setUploadLoading(isLoading, btn) {
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

// ── Add Berkas to DOM List ───────────────────────────────────────
function addBerkasToList(id, nama, fileUrl) {
    if (!berkasListElement) return;

    // Hapus teks "Belum ada berkas" jika masih ada
    const emptyText = berkasListElement.querySelector(
        ".anak-asuh-berkas-empty",
    );
    if (emptyText) emptyText.remove();

    const berkasHtml = `
        <div class="anak-asuh-berkas-item" data-berkas-type="existing" data-berkas-id="${id}">
            <div class="anak-asuh-berkas-info">
                <span class="anak-asuh-berkas-name">${escapeHtml(nama)}</span>
            </div>
            <div class="anak-asuh-berkas-actions">
                <a href="${escapeHtml(fileUrl)}" target="_blank"
                    class="anak-asuh-btn-berkas-view" title="Lihat Berkas">
                    <i data-lucide="eye"></i>
                </a>
                <button type="button" class="anak-asuh-btn-berkas-delete"
                    onclick="deleteBerkas(${id})" title="Hapus Berkas">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>
    `;

    berkasListElement.insertAdjacentHTML("beforeend", berkasHtml);

    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// ── Helper: Escape HTML ─────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

// ── Delete Berkas via AJAX ──────────────────────────────────────
window.deleteBerkas = function (berkasId) {
    if (!alertContext || !alertContext.alertModal) {
        if (confirm("Apakah Anda yakin ingin menghapus berkas ini?")) {
            performDeleteBerkas(berkasId);
        }
        return;
    }

    alertContext.alertTitle.textContent = "Konfirmasi Hapus";
    alertContext.alertMessage.textContent =
        "Apakah Anda yakin ingin menghapus berkas ini?";
    alertContext.alertConfirmBtn.textContent = "Hapus";
    alertContext.alertConfirmBtn.className = "alert-btn alert-btn-confirm";
    alertContext.alertCancelBtn.style.display = "block";

    alertContext.alertConfirmBtn.onclick = () => {
        alertContext.alertModal.style.display = "none";
        document.body.style.overflow = "";
        performDeleteBerkas(berkasId);
    };

    alertContext.alertModal.style.display = "flex";
    document.body.style.overflow = "hidden";
};

// ── Perform Delete AJAX ─────────────────────────────────────────
function performDeleteBerkas(berkasId) {
    const url = `${deleteUrl}/${berkasId}`;

    fetch(url, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then(async (res) => {
            if (res.status === 404) throw { notFound: true };
            if (res.status >= 500) throw { serverError: true };
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then((data) => {
            if (data.success) {
                const berkasItem = document.querySelector(
                    `[data-berkas-id="${berkasId}"]`,
                );
                if (berkasItem) berkasItem.remove();

                // Tampilkan teks kosong jika tidak ada berkas tersisa
                if (
                    berkasListElement &&
                    berkasListElement.querySelectorAll(".anak-asuh-berkas-item")
                        .length === 0
                ) {
                    berkasListElement.innerHTML =
                        '<p class="anak-asuh-berkas-empty">Belum ada berkas yang diunggah.</p>';
                }

                showSuccessAlert(alertContext, "Berkas berhasil dihapus!");
            } else {
                showSuccessAlert(alertContext, "Berkas berhasil dihapus!");
            }
        })
        .catch((error) => {
            console.error("Delete berkas error:", error);

            if (error.notFound) {
                showErrorAlert(
                    alertContext,
                    "Berkas tidak ditemukan. Mungkin sudah dihapus sebelumnya.",
                );
                return;
            }

            if (error.serverError) {
                showErrorAlert(
                    alertContext,
                    "Server sedang mengalami gangguan. Silakan coba lagi nanti.",
                );
                return;
            }

            showErrorAlert(
                alertContext,
                error.message?.includes("Failed to fetch")
                    ? "Koneksi gagal. Periksa koneksi internet Anda."
                    : "Gagal menghapus berkas. Silakan coba lagi.",
            );
        });
}
