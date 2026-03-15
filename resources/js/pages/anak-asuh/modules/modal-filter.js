/**
 * Filter Modal Module
 *
 * Button footer behavior:
 * - Pilihan di dropdown berbeda dari filter aktif → "Terapkan Filter"
 * - Pilihan di dropdown sama dengan filter aktif  → "Reset Filter"
 * - Belum ada filter aktif sama sekali            → "Terapkan Filter"
 */

export class FilterModal {
    constructor() {
        // filterValues = filter yang SUDAH diterapkan (dari klik Terapkan)
        this.filterValues = {
            status: "",
            is_active: "",
            grade: "",
            rh: "",
        };

        if (this.isAnakAsuhPage()) {
            this.waitForModalElement();
        }
    }

    isAnakAsuhPage() {
        return document.querySelector(".anak-asuh-page") !== null;
    }

    waitForModalElement() {
        const checkModal = () => {
            const modal = document.getElementById("filterModal");
            const filterTrigger = document.querySelector(".btn-filter-modal");

            if (modal && filterTrigger) {
                this.modal = modal;
                this.applyBtn = document.getElementById("applyFilterBtn");
                this.closeButtons =
                    document.querySelectorAll("[data-modal-close]");
                this.filterTrigger = filterTrigger;
                this.init();
                return true;
            }
            return false;
        };

        if (checkModal()) return;

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => {
                if (!checkModal()) setTimeout(checkModal, 100);
            });
        } else {
            setTimeout(checkModal, 100);
        }
    }

    init() {
        this.filterTrigger.addEventListener("click", (e) => {
            e.preventDefault();
            this.open();
        });

        this.closeButtons.forEach((btn) => {
            btn.addEventListener("click", () => this.close());
        });

        if (this.applyBtn) {
            this.applyBtn.addEventListener("click", () => {
                const action = this.applyBtn.getAttribute("data-action");
                if (action === "reset") {
                    this.resetFilters();
                } else {
                    this.applyFilters();
                }
            });
        }

        if (this.modal) {
            this.modal.addEventListener("click", (e) => {
                if (e.target === this.modal) this.close();
            });
        }

        this.initModalDropdowns();
        this.updateApplyBtnState();
    }

    open() {
        if (this.modal) {
            this.modal.classList.add("show");
            document.body.style.overflow = "hidden";
            if (
                window.lucide &&
                typeof window.lucide.createIcons === "function"
            ) {
                window.lucide.createIcons();
            }
        }
    }

    close() {
        if (this.modal) {
            this.modal.classList.remove("show");
            document.body.style.overflow = "";
        }
    }

    // Cek apakah filter yang SUDAH DITERAPKAN ada nilainya
    hasActiveFilters() {
        return (
            this.filterValues.status !== "" ||
            this.filterValues.is_active !== "" ||
            this.filterValues.grade !== "" ||
            this.filterValues.rh !== ""
        );
    }

    // Baca pilihan dropdown yang SEDANG DIPILIH user di modal (belum diterapkan)
    getPendingValues() {
        return {
            status: document.getElementById("modal-status-filter")?.value || "",
            is_active:
                document.getElementById("modal-is-active-filter")?.value ?? "",
            grade: document.getElementById("modal-grade-filter")?.value || "",
            rh: document.getElementById("modal-rh-filter")?.value || "",
        };
    }

    // Cek apakah pilihan pending berbeda dari filter yang sudah diterapkan
    isPendingDifferentFromApplied() {
        const pending = this.getPendingValues();
        return (
            pending.status !== this.filterValues.status ||
            pending.is_active !== this.filterValues.is_active ||
            pending.grade !== this.filterValues.grade ||
            pending.rh !== this.filterValues.rh
        );
    }

    // Update teks dan data-action button:
    // - Jika pilihan pending berbeda dari applied → "Terapkan Filter"
    // - Jika sama dan ada filter aktif            → "Reset Filter"
    // - Jika sama dan tidak ada filter aktif      → "Terapkan Filter" (disabled feel)
    updateApplyBtnState() {
        if (!this.applyBtn) return;

        const pendingDifferent = this.isPendingDifferentFromApplied();
        const hasActive = this.hasActiveFilters();

        if (pendingDifferent) {
            // User mengubah pilihan — selalu tampilkan Terapkan Filter
            this.applyBtn.textContent = "Terapkan Filter";
            this.applyBtn.setAttribute("data-action", "apply");
            this.applyBtn.classList.remove("anak-asuh-btn-filter-active");
        } else if (hasActive) {
            // Pilihan sama dengan yang diterapkan, dan ada filter aktif → Reset
            this.applyBtn.textContent = "Reset Filter";
            this.applyBtn.setAttribute("data-action", "reset");
            this.applyBtn.classList.add("anak-asuh-btn-filter-active");
        } else {
            // Tidak ada filter sama sekali → Terapkan
            this.applyBtn.textContent = "Terapkan Filter";
            this.applyBtn.setAttribute("data-action", "apply");
            this.applyBtn.classList.remove("anak-asuh-btn-filter-active");
        }
    }

    applyFilters() {
        try {
            const pending = this.getPendingValues();

            this.filterValues = { ...pending };

            this._syncToHiddenInputs();
            this.updateApplyBtnState();

            if (this.filterTrigger) {
                if (this.hasActiveFilters()) {
                    this.filterTrigger.classList.add("active");
                } else {
                    this.filterTrigger.classList.remove("active");
                }
            }

            if (typeof window.anakAsuhApplyFilters === "function") {
                window.anakAsuhApplyFilters();
            }

            this.close();
        } catch (error) {
            console.error("Error applying filters:", error);
        }
    }

    resetFilters() {
        this.filterValues = { status: "", is_active: "", grade: "", rh: "" };

        // Reset hidden input modal
        const modalStatus = document.getElementById("modal-status-filter");
        const modalIsActive = document.getElementById("modal-is-active-filter");
        const modalGrade = document.getElementById("modal-grade-filter");
        const modalRh = document.getElementById("modal-rh-filter");

        if (modalStatus) modalStatus.value = "";
        if (modalIsActive) modalIsActive.value = "";
        if (modalGrade) modalGrade.value = "";
        if (modalRh) modalRh.value = "";

        // Reset teks semua dropdown modal
        const modalDropdowns = document.querySelectorAll(
            "#filterModal .modal-custom-dropdown",
        );
        modalDropdowns.forEach((dropdown) => {
            const textEl = dropdown.querySelector(".modal-dropdown-text");
            const filter = dropdown.getAttribute("data-filter");
            const trigger = dropdown.querySelector(".modal-dropdown-trigger");
            const menu = dropdown.querySelector(".modal-dropdown-menu");

            if (textEl) {
                const defaultTexts = {
                    status: "Semua Status",
                    is_active: "Semua Keaktifan",
                    grade: "Semua Grade",
                    rh: "Semua Asrama",
                };
                textEl.textContent = defaultTexts[filter] || "Semua";
            }
            if (trigger) trigger.classList.remove("active");
            if (menu) menu.classList.remove("show");
        });

        this._syncToHiddenInputs();
        this.updateApplyBtnState();

        if (this.filterTrigger) {
            this.filterTrigger.classList.remove("active");
        }

        this.close();

        if (typeof window.anakAsuhApplyFilters === "function") {
            window.anakAsuhApplyFilters();
        }
    }

    _syncToHiddenInputs() {
        const statusInput = document.getElementById("statusFilter");
        const isActiveInput = document.getElementById("isActiveFilter");
        const gradeInput = document.getElementById("gradeFilter");
        const rhInput = document.getElementById("rhFilter");

        if (statusInput) statusInput.value = this.filterValues.status;
        if (isActiveInput) isActiveInput.value = this.filterValues.is_active;
        if (gradeInput) gradeInput.value = this.filterValues.grade;
        if (rhInput) rhInput.value = this.filterValues.rh;
    }

    initModalDropdowns() {
        const modalDropdowns = document.querySelectorAll(
            "#filterModal .modal-custom-dropdown",
        );

        modalDropdowns.forEach((dropdown, index) => {
            const trigger = dropdown.querySelector(".modal-dropdown-trigger");
            const menu = dropdown.querySelector(".modal-dropdown-menu");
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const dropdownText = dropdown.querySelector(".modal-dropdown-text");

            if (!trigger || !menu || !hiddenInput || !dropdownText) {
                console.warn(`Dropdown ${index} missing elements`);
                return;
            }

            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();

                const isMenuVisible = menu.classList.contains("show");

                // Tutup semua dropdown lain
                modalDropdowns.forEach((d) => {
                    d.querySelector(".modal-dropdown-menu")?.classList.remove(
                        "show",
                    );
                    d.querySelector(
                        ".modal-dropdown-trigger",
                    )?.classList.remove("active");
                });

                if (!isMenuVisible) {
                    menu.classList.add("show");
                    trigger.classList.add("active");
                }
            });

            menu.querySelectorAll(".modal-dropdown-item").forEach((item) => {
                item.addEventListener("click", (e) => {
                    e.stopPropagation();
                    hiddenInput.value = item.dataset.value;
                    dropdownText.textContent = item.textContent.trim();
                    menu.classList.remove("show");
                    trigger.classList.remove("active");

                    // Setiap kali user memilih item, update tombol
                    this.updateApplyBtnState();
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
        return { ...this.filterValues };
    }

    setValues(values) {
        this.filterValues = { ...this.filterValues, ...values };
    }
}
