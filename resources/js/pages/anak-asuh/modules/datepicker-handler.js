/**
 * Datepicker Handler Module
 * Handles Flatpickr date picker initialization
 */

import flatpickr from "flatpickr";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

/**
 * Setup scroll listener untuk menutup calendar saat input
 * tidak lagi terlihat di viewport (ter-scroll keluar layar).
 * Dipasang per instance agar bisa di-remove saat calendar ditutup.
 */
function setupScrollClose(instance) {
    let scrollHandler = null;

    instance.config.onOpen.push(function () {
        scrollHandler = () => {
            const input = instance.altInput || instance.element;
            const rect = input.getBoundingClientRect();
            const inViewport =
                rect.top >= 0 &&
                rect.bottom <=
                    (window.innerHeight ||
                        document.documentElement.clientHeight);

            if (!inViewport) {
                instance.close();
            }
        };

        // Pasang listener pada semua scrollable parent dan window
        window.addEventListener("scroll", scrollHandler, { passive: true });
        document
            .querySelectorAll(".page-content, .content-wrapper, .card-body")
            .forEach((el) => {
                el.addEventListener("scroll", scrollHandler, { passive: true });
            });
    });

    instance.config.onClose.push(function () {
        if (scrollHandler) {
            window.removeEventListener("scroll", scrollHandler);
            document
                .querySelectorAll(".page-content, .content-wrapper, .card-body")
                .forEach((el) => {
                    el.removeEventListener("scroll", scrollHandler);
                });
            scrollHandler = null;
        }
    });
}

export function setupFlatpickrDatePickers() {
    const sharedConfig = {
        locale: Indonesian,
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: false,
        disableMobile: true,
        clickOpens: true,
        // Auto posisi — muncul di atas jika ruang bawah tidak cukup,
        // muncul di bawah jika ruang bawah cukup
        position: "auto",
        onReady(selectedDates, dateStr, instance) {
            instance.altInput.classList.add("anak-asuh-form-input");
            instance.altInput.classList.add("anak-asuh-flatpickr-input");
            instance.altInput.setAttribute("readonly", "readonly");

            instance.altInput.addEventListener("focus", function (e) {
                e.target.blur();
                instance.open();
            });

            instance.altInput.addEventListener("touchstart", function (e) {
                e.preventDefault();
                instance.open();
            });

            if (instance.element.classList.contains("anak-asuh-is-invalid")) {
                instance.altInput.classList.add("anak-asuh-is-invalid");
            }

            // Pasang scroll close per instance
            setupScrollClose(instance);
        },
        onChange(selectedDates, dateStr, instance) {
            instance.altInput.classList.remove("anak-asuh-is-invalid");
            instance.element.classList.remove("anak-asuh-is-invalid");

            if (dateStr) {
                instance.element.value = dateStr;
            }
        },
    };

    // Tanggal Lahir
    const tanggalLahirEl = document.getElementById("tanggal_lahir");
    if (tanggalLahirEl) {
        const defaultValue = tanggalLahirEl.value.trim();

        flatpickr(tanggalLahirEl, {
            ...sharedConfig,
            defaultDate: defaultValue || null,
            maxDate: "today",
            onReady(selectedDates, dateStr, instance) {
                sharedConfig.onReady.call(
                    this,
                    selectedDates,
                    dateStr,
                    instance,
                );
                instance.altInput.placeholder = "Pilih tanggal lahir";
            },
        });
    }

    // Tanggal Masuk RH
    const tanggalMasukEl = document.getElementById("tanggal_masuk_rh");
    if (tanggalMasukEl) {
        const defaultValue = tanggalMasukEl.value.trim();

        flatpickr(tanggalMasukEl, {
            ...sharedConfig,
            defaultDate: defaultValue || null,
            maxDate: "today",
            onReady(selectedDates, dateStr, instance) {
                sharedConfig.onReady.call(
                    this,
                    selectedDates,
                    dateStr,
                    instance,
                );
                instance.altInput.placeholder = "Pilih tanggal masuk RH";
            },
        });
    }

    // Generic datepickers
    document
        .querySelectorAll(
            ".anak-asuh-datepicker:not(#tanggal_lahir):not(#tanggal_masuk_rh)",
        )
        .forEach((el) => {
            const defaultValue = el.value.trim();

            flatpickr(el, {
                ...sharedConfig,
                defaultDate: defaultValue || null,
                onReady(selectedDates, dateStr, instance) {
                    sharedConfig.onReady.call(
                        this,
                        selectedDates,
                        dateStr,
                        instance,
                    );
                    instance.altInput.placeholder = "Pilih tanggal";
                },
            });
        });
}
