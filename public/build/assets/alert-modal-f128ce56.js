function n(e){e.alertContainer.dataset.alertType="confirm",e.alertIcon.className="alert-icon alert-icon--confirm",e.alertIcon.innerHTML=`
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
            </path>
        </svg>
    `,e.alertCancelBtn.style.display="block",e.alertConfirmBtn.className="alert-btn alert-btn-confirm",e.alertConfirmBtn.textContent="Hapus"}function r(e){e.alertModal.style.display="none",document.body.style.overflow="",e.alertConfirmBtn._autoCloseTimer&&(clearTimeout(e.alertConfirmBtn._autoCloseTimer),delete e.alertConfirmBtn._autoCloseTimer),n(e)}function s(e,a,l="Berhasil!"){e.alertContainer.dataset.alertType="success",e.alertIcon.className="alert-icon alert-icon--success",e.alertIcon.innerHTML=`
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    `,e.alertTitle.textContent=l,e.alertMessage.textContent=a,e.alertCancelBtn.style.display="none",e.alertConfirmBtn.textContent="Tutup",e.alertConfirmBtn.className="alert-btn alert-btn-close",e.alertConfirmBtn.onclick=()=>r(e),e.alertModal.style.display="flex",document.body.style.overflow="hidden";const t=setTimeout(()=>{e.alertModal.style.display==="flex"&&r(e)},5e3);e.alertConfirmBtn._autoCloseTimer=t}function i(e,a,l="Terjadi Kesalahan"){e.alertContainer.dataset.alertType="error",e.alertIcon.className="alert-icon alert-icon--error",e.alertIcon.innerHTML=`
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    `,e.alertTitle.textContent=l,e.alertMessage.textContent=a,e.alertCancelBtn.style.display="none",e.alertConfirmBtn.textContent="Tutup",e.alertConfirmBtn.className="alert-btn alert-btn-error",e.alertConfirmBtn.onclick=()=>r(e),e.alertModal.style.display="flex",document.body.style.overflow="hidden"}function d(e){e.alertCancelBtn.addEventListener("click",()=>r(e)),e.alertModal.addEventListener("click",a=>{a.target===e.alertModal&&r(e)}),document.addEventListener("keydown",a=>{a.key==="Escape"&&e.alertModal.style.display==="flex"&&r(e)})}function m(e){const a=document.getElementById("dashboardTransitionLoader"),l=document.getElementById("transition-loader"),t=a||l;t&&(t.classList.contains("dashboard-transition-loader--active")||t.style.display!=="none")?setTimeout(e,1700):e()}export{i as a,d as i,n as r,s,m as w};
