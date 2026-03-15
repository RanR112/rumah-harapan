/**
 * User Form Handler Module
 *
 * Dipindahkan dari inline script di form.blade.php
 * Handles:
 * - Password toggle (show/hide)
 * - Custom dropdown untuk Role
 * - Form submission dengan button loader
 */

export function initUserForm() {
    "use strict";

    if (!document.querySelector(".users-form-page")) return;

    initPasswordToggle();
    initCustomDropdowns();
    setupFormSubmission();
}

// ==================== PASSWORD TOGGLE ====================
function initPasswordToggle() {
    document.querySelectorAll(".toggle-password").forEach((button) => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            const icon = this.querySelector(".eye-icon");

            if (!input || !icon) return;

            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML =
                    '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = "password";
                icon.innerHTML =
                    '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        });
    });
}

// ==================== CUSTOM DROPDOWNS ====================
function initCustomDropdowns() {
    const dropdowns = document.querySelectorAll("[data-dropdown]");

    dropdowns.forEach((dropdown) => {
        if (dropdown.dataset.initialized === "true") return;

        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const menu = dropdown.querySelector("[data-dropdown-menu]");
        const options = dropdown.querySelectorAll("[data-dropdown-option]");
        const valueDisplay = trigger?.querySelector(".dropdown-value");
        const hiddenInput = document.getElementById("role");
        const chevron = trigger?.querySelector(".dropdown-chevron");

        if (!trigger || !menu || !hiddenInput) return;

        const toggleDropdown = (show = null) => {
            const isExpanded = trigger.getAttribute("aria-expanded") === "true";
            const shouldShow = show !== null ? show : !isExpanded;

            trigger.setAttribute("aria-expanded", String(shouldShow));
            menu.classList.toggle("show", shouldShow);

            if (chevron) {
                chevron.style.transform = shouldShow
                    ? "rotate(180deg)"
                    : "rotate(0deg)";
                chevron.style.transition = "transform 0.2s ease";
            }
        };

        const handleOptionClick = (option) => {
            const value = option.getAttribute("value");
            const text = option.textContent.trim();

            if (valueDisplay) valueDisplay.textContent = text;
            if (hiddenInput) {
                hiddenInput.value = value;
                hiddenInput.dispatchEvent(
                    new Event("input", { bubbles: true }),
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
            if (!dropdown.contains(e.target)) toggleDropdown(false);
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") toggleDropdown(false);
        });

        dropdown.dataset.initialized = "true";
    });
}

// ==================== FORM SUBMISSION ====================
function setupFormSubmission() {
    const form = document.querySelector(".user-form");
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    form.addEventListener("submit", function () {
        const btnText = submitButton.querySelector(".btn-text");
        const btnLoader = submitButton.querySelector(".btn-loader");

        if (btnText && btnLoader) {
            btnText.style.display = "none";
            btnLoader.style.display = "flex";
            submitButton.disabled = true;
        }
    });
}
