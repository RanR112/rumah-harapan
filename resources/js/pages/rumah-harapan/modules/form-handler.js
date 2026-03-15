/**
 * Rumah Harapan Form Handler Module
 */

export function initRumahHarapanForm() {
    "use strict";

    if (!document.querySelector(".rumah-harapan-form-page")) {
        return;
    }

    initCustomDropdowns();
    setupFormSubmission();
}

// ==================== CUSTOM DROPDOWNS ====================
function initCustomDropdowns() {
    const dropdowns = document.querySelectorAll("[data-dropdown]");

    if (dropdowns.length === 0) {
        console.warn("No dropdowns found with [data-dropdown]");
        return;
    }

    dropdowns.forEach((dropdown) => {
        if (dropdown.dataset.initialized === "true") return;

        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const menu = dropdown.querySelector("[data-dropdown-menu]");
        const options = dropdown.querySelectorAll("[data-dropdown-option]");
        const hiddenInput = dropdown.previousElementSibling;
        const valueDisplay = trigger.querySelector(".dropdown-value");

        if (!trigger || !menu || !hiddenInput) {
            console.warn("Missing dropdown elements", {
                trigger,
                menu,
                hiddenInput,
            });
            return;
        }

        const toggleDropdown = (show = null) => {
            const isExpanded = trigger.getAttribute("aria-expanded") === "true";
            const shouldShow = show !== null ? show : !isExpanded;

            trigger.setAttribute("aria-expanded", String(shouldShow));
            menu.style.display = shouldShow ? "block" : "none";

            const chevron = trigger.querySelector(".dropdown-chevron");
            if (chevron) {
                chevron.style.transform = shouldShow
                    ? "rotate(180deg)"
                    : "rotate(0deg)";
            }
        };

        const handleOptionClick = (option) => {
            const value = option.dataset.value;
            const text = option.textContent.trim();

            if (valueDisplay) {
                valueDisplay.textContent = text || "Pilih opsi";
            }

            if (hiddenInput && hiddenInput.type === "hidden") {
                hiddenInput.value = value;
                hiddenInput.dispatchEvent(
                    new Event("change", { bubbles: true }),
                );
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
    const form = document.querySelector(".rumah-harapan-form");
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    form.addEventListener("submit", function (e) {
        const requiredFields = ["kode", "nama", "alamat", "kota", "provinsi"];
        let isValid = true;

        requiredFields.forEach((fieldName) => {
            const field = document.getElementById(fieldName);
            if (field && !field.value.trim()) {
                isValid = false;
                if (field.classList.contains("form-input")) {
                    field.classList.add("is-invalid");
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            const firstError = document.querySelector(".is-invalid");
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                firstError.focus();
            }
        } else {
            const btnText = submitButton.querySelector(".btn-text");
            const btnLoader = submitButton.querySelector(".btn-loader");

            if (btnText && btnLoader) {
                btnText.style.display = "none";
                btnLoader.style.display = "flex";
                submitButton.disabled = true;
            }
        }
    });
}
