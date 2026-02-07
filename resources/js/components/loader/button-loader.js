export function initButtonLoader() {
    document.querySelectorAll('form').forEach(form => {
        if (form.dataset.loaderInitialized) return;
        
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('[type="submit"]');
            if (!submitButton) return;

            const btnText = submitButton.querySelector('.btn-text');
            const btnLoader = submitButton.querySelector('.btn-loader');
            
            if (btnText && btnLoader) {
                try {
                    btnText.style.display = 'none';
                    btnLoader.style.display = 'flex';
                    submitButton.disabled = true;
                } catch (error) {
                    console.warn('Button loader failed:', error);
                }
            }
        });

        form.dataset.loaderInitialized = 'true';
    });
}