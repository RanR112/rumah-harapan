/**
 * Anak Asuh Form Handler Module - Main Orchestrator
 *
 * CRITICAL INITIALIZATION ORDER:
 * 1. Flatpickr MUST init first (replaces DOM inputs)
 * 2. Custom dropdowns
 * 3. Foto preview
 * 4. Berkas upload
 * 5. Form validation listener (LAST - after all DOM manipulations done)
 */

import { setupFotoPreview, updateFotoPreview } from "./foto-handler.js";
import { setupFlatpickrDatePickers } from "./datepicker-handler.js";
import { setupBerkasUpload } from "./berkas-handler.js";

export function initAnakAsuhForm(
    showSuccessAlert,
    showErrorAlert,
    context,
    berkasConfig,
) {
    "use strict";

    const isReadOnly = window.pageConfig?.isReadOnly ?? false;
    const canManageBerkas = window.pageConfig?.canManageBerkas ?? false;

    if (!document.querySelector(".anak-asuh-form-page")) {
        return;
    }

    // Init flatpickr PERTAMA
    setupFlatpickrDatePickers();

    if (!isReadOnly) {
        initCustomDropdowns();
        setupFotoPreview();
        setupFormSubmission();
    }

    // Setup berkas upload (hanya di halaman edit)
    if (canManageBerkas && document.getElementById("addBerkasBtn")) {
        setupBerkasUpload(
            berkasConfig,
            showSuccessAlert,
            showErrorAlert,
            context,
        );
    }
}

// ==================== CUSTOM DROPDOWNS ====================
function initCustomDropdowns() {
    const dropdowns = document.querySelectorAll("[data-dropdown]");

    if (dropdowns.length === 0) {
        console.warn("No dropdowns found");
        return;
    }

    dropdowns.forEach((dropdown) => {
        if (dropdown.dataset.initialized === "true") return;

        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const menu = dropdown.querySelector("[data-dropdown-menu]");
        const options = dropdown.querySelectorAll("[data-dropdown-option]");
        const hiddenInput = dropdown.previousElementSibling;
        const valueDisplay = trigger.querySelector(".anak-asuh-dropdown-value");

        if (!trigger || !menu || !hiddenInput) {
            console.warn("Missing dropdown elements");
            return;
        }

        const toggleDropdown = (show = null) => {
            const isExpanded = trigger.getAttribute("aria-expanded") === "true";
            const shouldShow = show !== null ? show : !isExpanded;

            trigger.setAttribute("aria-expanded", String(shouldShow));
            menu.setAttribute("data-show", String(shouldShow));

            const chevron = trigger.querySelector(
                ".anak-asuh-dropdown-chevron",
            );
            if (chevron) {
                chevron.style.transform = shouldShow
                    ? "rotate(180deg)"
                    : "rotate(0deg)";
            }
        };

        const handleOptionClick = (option) => {
            const value = option.getAttribute("value");
            const text = option.textContent.trim();

            if (valueDisplay) {
                valueDisplay.textContent = text || "Pilih opsi";
            }

            if (hiddenInput && hiddenInput.type === "hidden") {
                hiddenInput.value = value;
                hiddenInput.dispatchEvent(
                    new Event("change", { bubbles: true }),
                );

                // Update foto preview on gender change
                if (hiddenInput.id === "jenis_kel") {
                    updateFotoPreview(value);
                }
            }

            toggleDropdown(false);

            options.forEach((opt) => opt.classList.remove("selected"));
            option.classList.add("selected");
        };

        options.forEach((option) => {
            option.addEventListener("click", (e) => {
                e.stopPropagation();
                handleOptionClick(option);
            });
        });

        trigger.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleDropdown();
        });

        document.addEventListener("click", (e) => {
            if (!dropdown.contains(e.target)) {
                toggleDropdown(false);
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                toggleDropdown(false);
            }
        });

        dropdown.dataset.initialized = "true";
    });
}

// ==================== FORM SUBMISSION ====================
function setupFormSubmission() {
    const form = document.querySelector(".anak-asuh-form");
    if (!form) return;

    form.addEventListener("submit", function (e) {

        const requiredFields = [
            "nama_anak",
            "nik",
            "no_kartu_keluarga",
            "jenis_kel",
            "tanggal_lahir",
            "status",
            "grade",
            "nama_orang_tua",
            "tanggal_masuk_rh",
            "rh",
        ];

        let isValid = true;
        const invalidFields = [];

        requiredFields.forEach((fieldName) => {

            // Tidak di-cache di luar loop, karena Flatpickr sudah replace DOM
            const field = document.getElementById(fieldName);

            if (field && !field.value.trim()) {
                isValid = false;
                invalidFields.push(fieldName);

                // Mark flatpickr altInput as invalid
                const wrapper = field.closest(".anak-asuh-datepicker-wrapper");
                if (wrapper) {
                    const altInput = wrapper.querySelector(
                        ".anak-asuh-flatpickr-input",
                    );
                    if (altInput) {
                        altInput.classList.add("anak-asuh-is-invalid");
                    }
                } else if (field.classList.contains("anak-asuh-form-input")) {
                    field.classList.add("anak-asuh-is-invalid");
                }
            }
        });

        if (!isValid) {
            e.preventDefault();

            const firstError = document.querySelector(".anak-asuh-is-invalid");
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                firstError.focus();
            }
        }
    });
}
