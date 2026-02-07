export function initOtpTimer() {
    const countdownElement = document.getElementById('countdown');
    const otpForm = document.getElementById('otpForm');
    const otpInputs = document.querySelectorAll('.otp-digit');
    const submitBtn = document.getElementById('otpSubmitBtn');
    const otpHiddenInput = document.getElementById('otp_code');
    const otpTimerContainer = document.getElementById('otpTimer');

    if (!countdownElement || !otpForm) return;

    // Gunakan localStorage untuk menyimpan timer
    const STORAGE_KEY = 'otp_timer_expiry';
    const DURATION = 2 * 60; // 2 menit dalam detik
    
    // Hitung waktu yang benar
    let expiryTime = localStorage.getItem(STORAGE_KEY);
    const now = Date.now();
    
    // Jika tidak ada timer atau timer sudah expired, buat timer baru
    if (!expiryTime || parseInt(expiryTime) <= now) {
        expiryTime = now + (DURATION * 1000);
        localStorage.setItem(STORAGE_KEY, expiryTime.toString());
    }
    
    let waktuTersisa = Math.floor((parseInt(expiryTime) - now) / 1000);
    let timerExpired = waktuTersisa <= 0;

    // Fungsi untuk update tampilan saat waktu habis
    const handleTimerExpired = () => {
        timerExpired = true;
        
        // Ubah teks menjadi "Belum mendapatkan email?"
        otpTimerContainer.innerHTML = 'Belum mendapatkan email? <a href="#" id="resendLink" style="color: #8da434; text-decoration: underline;">Kirim permintaan ulang</a>';
        
        // Tambahkan event listener untuk resend
        const resendLink = document.getElementById('resendLink');
        if (resendLink) {
            resendLink.addEventListener('click', (e) => {
                e.preventDefault();
                // Hapus timer lama dan buat baru
                localStorage.removeItem(STORAGE_KEY);
                window.location.href = '/forgot-password';
            });
        }
    };

    // Cek status timer di awal
    if (timerExpired) {
        handleTimerExpired();
    } else {
        // Update tampilan awal
        const menitAwal = Math.floor(waktuTersisa / 60);
        const detikAwal = waktuTersisa % 60;
        countdownElement.textContent = `${menitAwal}:${detikAwal.toString().padStart(2, '0')}`;
        
        // Timer countdown dengan localStorage
        const timer = setInterval(() => {
            const now = Date.now();
            const currentExpiry = localStorage.getItem(STORAGE_KEY);
            
            if (!currentExpiry) {
                clearInterval(timer);
                return;
            }
            
            const timeLeft = Math.floor((parseInt(currentExpiry) - now) / 1000);
            waktuTersisa = timeLeft;
            
            if (waktuTersisa <= 0) {
                clearInterval(timer);
                handleTimerExpired();
                return;
            }

            const menit = Math.floor(waktuTersisa / 60);
            const detik = waktuTersisa % 60;
            countdownElement.textContent = `${menit}:${detik.toString().padStart(2, '0')}`;
        }, 1000);
    }

    // Inisialisasi input OTP
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            let value = e.target.value;
            if (value.length > 1) {
                value = value.slice(0, 1);
                e.target.value = value;
            }
            
            // Auto focus ke input berikutnya
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Gabungkan nilai untuk hidden input
            const otpValue = Array.from(otpInputs).map(inp => inp.value).join('');
            otpHiddenInput.value = otpValue;
            
            // Submit otomatis jika semua terisi
            if (otpValue.length === 6 && !timerExpired) {
                otpForm.submit();
            }
        });
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    // Handle form submit
    otpForm.addEventListener('submit', (e) => {
        const otpValue = otpHiddenInput.value;
        if (otpValue.length !== 6) {
            e.preventDefault();
            return;
        }
    });
}

export default initOtpTimer;