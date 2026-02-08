/**
 * Mobile Filter Modal Module
 *
 * Pure class that handles the filter modal for mobile devices (≤480px)
 * Uses custom dropdowns instead of radio buttons
 */

export class MobileFilterModal {
    constructor() {
        this.mobileFilterValues = {
            status: "",
            grade: "",
            rh: "",
        };

        // Check if we're on anak-asuh page and in mobile mode
        if (this.isAnakAsuhPage() && this.isMobileDevice()) {
            this.waitForModalElement();
        }
    }

    isAnakAsuhPage() {
        return document.querySelector('.anak-asuh-page') !== null;
    }

    isMobileDevice() {
        return window.innerWidth <= 480;
    }

    waitForModalElement() {
        const checkModal = () => {
            const modal = document.getElementById("filterModal");
            const filterTrigger = document.querySelector(".btn-filter-mobile");
            
            if (modal && filterTrigger) {
                this.modal = modal;
                this.applyBtn = document.getElementById("applyFilterBtn");
                this.closeButtons = document.querySelectorAll("[data-modal-close]");
                this.filterTrigger = filterTrigger;
                
                this.init();
                return true;
            }
            return false;
        };

        // Try immediately first
        if (checkModal()) {
            return;
        }

        // If not found, try again after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                if (!checkModal()) {
                    // Final fallback after a short delay
                    setTimeout(checkModal, 100);
                }
            });
        } else {
            // DOM already loaded, try with small delay
            setTimeout(checkModal, 100);
        }
    }

    init() {

        // Event listeners
        this.filterTrigger.addEventListener("click", (e) => {
            e.preventDefault();
            this.open();
        });
        
        this.closeButtons.forEach((btn) => {
            btn.addEventListener("click", () => this.close());
        });
        
        if (this.applyBtn) {
            this.applyBtn.addEventListener("click", () => this.applyFilters());
        }

        // Close on overlay click
        if (this.modal) {
            this.modal.addEventListener("click", (e) => {
                if (e.target === this.modal) {
                    this.close();
                }
            });
        }

        // Initialize modal dropdowns
        this.initModalDropdowns();
    }

    open() {
        
        if (this.modal) {
            this.modal.classList.add("show");
            document.body.style.overflow = "hidden";
        } else {
            console.error("Modal element not found when trying to open");
        }
    }

    close() {
        if (this.modal) {
            this.modal.classList.remove("show");
            document.body.style.overflow = "";
        }
    }

    applyFilters() {
        try {
            // Get values from modal dropdowns
            const statusValue = document.getElementById("modal-status-filter")?.value || "";
            const gradeValue = document.getElementById("modal-grade-filter")?.value || "";
            const rhValue = document.getElementById("modal-rh-filter")?.value || "";

            // Store in state
            this.mobileFilterValues = {
                status: statusValue,
                grade: gradeValue,
                rh: rhValue,
            };

            // Update main filter inputs for AJAX
            const statusInput = document.getElementById("statusFilter");
            const gradeInput = document.getElementById("gradeFilter");
            const rhInput = document.getElementById("rhFilter");
            
            if (statusInput) statusInput.value = statusValue;
            if (gradeInput) gradeInput.value = gradeValue;
            if (rhInput) rhInput.value = rhValue;

            // Update dropdown text for desktop (if visible)
            this.updateDropdownTexts(statusValue, gradeValue, rhValue);

            // Apply filters via AJAX
            if (typeof window.anakAsuhApplyMobileFilters === "function") {
                window.anakAsuhApplyMobileFilters();
            }

            this.close();

            // Set active state
            if (this.filterTrigger) {
                this.filterTrigger.classList.add("active");
            }
            
        } catch (error) {
            console.error("Error applying filters:", error);
        }
    }

    updateDropdownTexts(statusValue, gradeValue, rhValue) {
        const statusDropdown = document.querySelector(
            '.custom-dropdown[data-filter="status"] .dropdown-text',
        );
        const gradeDropdown = document.querySelector(
            '.custom-dropdown[data-filter="grade"] .dropdown-text',
        );
        const rhDropdown = document.querySelector(
            '.custom-dropdown[data-filter="rh"] .dropdown-text',
        );

        if (statusDropdown) {
            statusDropdown.textContent = statusValue
                ? statusValue === "aktif"
                    ? "Aktif"
                    : "Non-Aktif"
                : "Semua Status";
        }
        if (gradeDropdown) {
            gradeDropdown.textContent = gradeValue
                ? `Grade ${gradeValue}`
                : "Semua Grade";
        }
        if (rhDropdown) {
            const selectedItem = document.querySelector(
                `.custom-dropdown[data-filter="rh"] .dropdown-item[data-value="${rhValue}"]`
            );
            rhDropdown.textContent = rhValue
                ? (selectedItem?.textContent || "Semua Cabang")
                : "Semua Cabang";
        }
    }

    initModalDropdowns() {
        const modalDropdowns = document.querySelectorAll(
            "#filterModal .custom-dropdown",
        );

        modalDropdowns.forEach((dropdown) => {
            const trigger = dropdown.querySelector(".custom-dropdown-trigger");
            const menu = dropdown.querySelector(".custom-dropdown-menu");
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const dropdownText = dropdown.querySelector(".dropdown-text");

            if (!trigger || !menu || !hiddenInput || !dropdownText) return;

            trigger.addEventListener("click", (e) => {
                e.stopPropagation();
                const isMenuVisible = menu.classList.contains("show");

                // Close all modal dropdowns
                modalDropdowns.forEach((d) => {
                    const dMenu = d.querySelector(".custom-dropdown-menu");
                    const dTrigger = d.querySelector(".custom-dropdown-trigger");
                    if (dMenu && dTrigger) {
                        dMenu.classList.remove("show");
                        dTrigger.classList.remove("active");
                    }
                });

                if (!isMenuVisible) {
                    menu.classList.add("show");
                    trigger.classList.add("active");
                }
            });

            menu.querySelectorAll(".dropdown-item").forEach((item) => {
                item.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const value = item.dataset.value;
                    const text = item.textContent;

                    hiddenInput.value = value;
                    dropdownText.textContent = text;

                    menu.classList.remove("show");
                    trigger.classList.remove("active");
                });
            });

            document.addEventListener("click", (e) => {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove("show");
                    trigger.classList.remove("active");
                }
            });
        });
    }

    getValues() {
        return { ...this.mobileFilterValues };
    }

    setValues(values) {
        this.mobileFilterValues = { ...this.mobileFilterValues, ...values };
    }
}