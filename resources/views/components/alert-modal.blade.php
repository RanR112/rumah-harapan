@props([
    'type' => 'success',
    'title' => 'Pemberitahuan',
    'message' => '',
    'confirmText' => 'Hapus',
    'closeText' => 'Tutup',
])

{{-- Alert Modal Component - Global --}}
<div id="alertModal" class="alert-modal-overlay" style="display: none;">
    <div class="alert-modal-container">
        <div class="alert-modal-content">
            {{-- Alert Icon --}}
            <div class="alert-icon alert-icon--confirm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>

            {{-- Alert Title (dengan ID!) --}}
            <h3 class="alert-title" id="alertModalTitle">Pemberitahuan</h3>

            {{-- Alert Message (dengan ID!) --}}
            <p class="alert-message" id="alertModalMessage">Pesan akan ditampilkan di sini</p>

            {{-- Alert Actions --}}
            <div class="alert-actions">
                <button class="alert-btn alert-btn-cancel" id="alertCancelBtn">Batal</button>
                <button class="alert-btn alert-btn-confirm" id="alertConfirmBtn">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize alert modal elements
        const alertModal = document.getElementById('alertModal');
        if (!alertModal) return;

        const alertTitle = document.getElementById('alertModalTitle');
        const alertMessage = document.getElementById('alertModalMessage');
        const alertCancelBtn = document.getElementById('alertCancelBtn');
        const alertConfirmBtn = document.getElementById('alertConfirmBtn');
        const alertCloseBtn = document.getElementById('alertCloseBtn');
        const alertContainer = alertModal.querySelector('.alert-modal-container');
        const alertIcon = alertModal.querySelector('.alert-icon');

        // Close modal on close button click
        alertCloseBtn?.addEventListener('click', function() {
            alertModal.style.display = 'none';
            document.body.style.overflow = '';
        });

        // Close modal on cancel button click
        alertCancelBtn?.addEventListener('click', function() {
            alertModal.style.display = 'none';
            document.body.style.overflow = '';
        });

        // Close modal on confirm button click
        alertConfirmBtn?.addEventListener('click', function() {
            alertModal.style.display = 'none';
            document.body.style.overflow = '';
        });

        // Close modal on overlay click
        alertModal.addEventListener('click', function(e) {
            if (e.target === alertModal) {
                alertModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                alertModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Make showAlert function globally available
        window.showAlert = function(type, title, message, options = {}) {
            // Set type
            alertContainer.dataset.alertType = type;
            alertIcon.className = `alert-icon alert-icon--${type}`;

            // Set content
            alertTitle.textContent = title || 'Pemberitahuan';
            alertMessage.textContent = message || '';

            // Show modal
            alertModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Set button text
            if (type === 'confirm') {
                const confirmText = options.confirmText || 'Hapus';
                alertConfirmBtn.textContent = confirmText;
            } else {
                const closeText = options.closeText || 'Tutup';
                alertCloseBtn.textContent = closeText;
            }
        };
    });
</script>
