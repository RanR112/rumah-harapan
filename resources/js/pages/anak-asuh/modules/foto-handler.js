/**
 * Foto Handler Module
 * Handles photo preview, upload, and deletion
 */

// Flag untuk track apakah user sudah upload foto custom
let hasCustomPhoto = false;

export function setupFotoPreview() {
    const fotoUpload = document.getElementById("foto_upload");
    const fotoPreview = document.getElementById("fotoPreview");
    const fotoContainer = document.querySelector(
        ".anak-asuh-foto-preview-container",
    );
    const deleteFotoBtn = document.getElementById("deleteFotoBtn");

    if (!fotoUpload || !fotoPreview || !fotoContainer) {
        console.warn("Foto elements not found");
        return;
    }

    // Cek apakah sudah ada foto existing dari database (saat edit)
    const existingFotoPath = document.querySelector(
        'input[name="existing_foto_path"]',
    );
    if (existingFotoPath && existingFotoPath.value) {
        hasCustomPhoto = true;
        showDeleteButton();
    }

    // Event: Upload file → Update preview
    fotoUpload.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi ukuran file (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert("Ukuran file maksimal 2MB!");
                fotoUpload.value = "";
                return;
            }

            // Validasi tipe file
            const validTypes = ["image/jpeg", "image/jpg", "image/png"];
            if (!validTypes.includes(file.type)) {
                alert("Format file harus JPG, JPEG, atau PNG!");
                fotoUpload.value = "";
                return;
            }

            // Preview gambar
            const reader = new FileReader();
            reader.onload = function (e) {
                fotoPreview.src = e.target.result;
                hasCustomPhoto = true;
                showDeleteButton();

                // Reset signal hapus jika user upload foto baru
                setDeleteFotoSignal(false);
            };
            reader.readAsDataURL(file);
        }
    });

    // Event: Klik container → Trigger file input
    fotoContainer.addEventListener("click", function (e) {
        if (e.target.closest("#deleteFotoBtn")) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        fotoUpload.click();
    });

    // Event: Click Delete Button
    if (deleteFotoBtn) {
        deleteFotoBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const jenisKelInput = document.getElementById("jenis_kel");
            const jenisKel = jenisKelInput ? jenisKelInput.value : "L";
            const defaultImage = jenisKel === "L" ? "L.png" : "P.png";

            // Tampilkan default image di preview
            fotoPreview.src = `/images/default-anak-asuh-${defaultImage}`;
            fotoUpload.value = "";
            hasCustomPhoto = false;
            hideDeleteButton();

            // Kirim signal ke server: hapus foto
            setDeleteFotoSignal(true);

            // Hapus hidden input existing_foto_path dari DOM
            // (tidak dikirim ke server = server tahu foto lama tidak dipertahankan)
            const existingFotoPath = document.querySelector(
                'input[name="existing_foto_path"]',
            );
            if (existingFotoPath) {
                existingFotoPath.remove();
            }
        });
    }

    // Drag & drop support
    fotoContainer.addEventListener("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
        fotoContainer.style.borderColor = "rgba(141, 164, 52, 0.6)";
    });

    fotoContainer.addEventListener("dragleave", function (e) {
        e.preventDefault();
        e.stopPropagation();
        fotoContainer.style.borderColor = "#e2e8f0";
    });

    fotoContainer.addEventListener("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        fotoContainer.style.borderColor = "#e2e8f0";

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fotoUpload.files = files;
            const changeEvent = new Event("change", { bubbles: true });
            fotoUpload.dispatchEvent(changeEvent);
        }
    });
}

export function updateFotoPreview(jenisKel) {
    const fotoPreview = document.getElementById("fotoPreview");

    if (fotoPreview) {
        if (hasCustomPhoto) {
            return;
        }

        const defaultImage = jenisKel === "L" ? "L.png" : "P.png";
        fotoPreview.src = `/images/default-anak-asuh-${defaultImage}`;
    }
}

// ── Helper: set/reset signal delete_foto ──────────────────────
function setDeleteFotoSignal(shouldDelete) {
    const signal = document.getElementById("deleteFotoSignal");
    if (signal) {
        signal.value = shouldDelete ? "1" : "0";
    }
}

function showDeleteButton() {
    const deleteBtn = document.getElementById("deleteFotoBtn");
    if (deleteBtn) {
        deleteBtn.style.display = "flex";
    }
}

function hideDeleteButton() {
    const deleteBtn = document.getElementById("deleteFotoBtn");
    if (deleteBtn) {
        deleteBtn.style.display = "none";
    }
}
