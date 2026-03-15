export function initButtonLoader() {
    document.querySelectorAll("form").forEach((form) => {
        if (form.dataset.loaderInitialized) return;

        form.addEventListener("submit", function () {
            const submitButton = this.querySelector('[type="submit"]');
            if (!submitButton) return;

            const btnText = submitButton.querySelector(".btn-text");
            const btnLoader = submitButton.querySelector(".btn-loader");

            // Cek apakah button memiliki struktur loader
            const hasLoaderStructure = btnText && btnLoader;

            if (hasLoaderStructure) {
                try {
                    // Sembunyikan semua child element kecuali .btn-loader
                    Array.from(submitButton.children).forEach((child) => {
                        if (!child.classList.contains("btn-loader")) {
                            child.style.display = "none";
                        }
                    });

                    btnLoader.style.display = "flex";
                    submitButton.disabled = true;
                } catch (error) {
                    console.warn("Button loader failed:", error);
                }
            } else {
                // Fallback: disable saja tanpa struktur loader
                submitButton.disabled = true;
                submitButton.textContent = "Memproses...";
            }
        });

        form.dataset.loaderInitialized = "true";
    });
}
