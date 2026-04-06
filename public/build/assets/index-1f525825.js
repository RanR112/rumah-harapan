import{i as C,w as $,r as A,s as M,a as T}from"./alert-modal-f128ce56.js";import{_ as P}from"./preload-helper-f61836a9.js";function h(e){if(!e)return"";const t=document.createElement("div");return t.textContent=e,t.innerHTML}function v(e,t,n=!1){var o,s;e?(n&&((o=t.searchLoader)==null||o.classList.add("active")),t.tableContainer.innerHTML=`
            <tr class="table-row">
                <td colspan="6" class="text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `):n&&((s=t.searchLoader)==null||s.classList.remove("active"))}function q(e,t){t.tableContainer.innerHTML=`
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="error-message">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="mt-2">${h(e)}</p>
                    <button class="btn-reload mt-3" onclick="location.reload()">
                        <i data-lucide="refresh-ccw" class="btn-icon"></i>
                        Muat Ulang
                    </button>
                </div>
            </td>
        </tr>
    `,window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function R(e,t=!1){let n,o,s;const r=e.userRole==="admin";t?(n=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
                <path d="m14 8-6 6"></path>
                <path d="m8 8 6 6"></path>
            </svg>
        `,o="Data tidak ditemukan",s="Coba gunakan kata kunci atau filter yang berbeda."):(n=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                <path d="M10 6h4"></path>
                <path d="M10 10h4"></path>
                <path d="M10 14h4"></path>
                <path d="M10 18h4"></path>
            </svg>
        `,o="Belum ada data asrama",s="Belum ada data asrama yang terdaftar di sistem."),e.tableContainer.innerHTML=`
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                    ${n}
                    <h4 class="empty-title">${o}</h4>
                    <p class="empty-text">${s}</p>
                    ${!t&&r?`
                    <div class="empty-actions">
                        <a href="${e.baseUrl}/rumah-harapan/create" class="empty-link">
                            Tambah Data Asrama
                        </a>
                    </div>`:""}
                </div>
            </td>
        </tr>
    `}function _(e,t){return e.userRole==="admin"?`
            <button type="button" class="btn-rumah-harapan-edit"
                    data-rumah-harapan-id="${t.id}"
                    title="Edit Data">
                <i data-lucide="edit" class="action-icon"></i>
            </button>
            <button type="button" class="btn-rumah-harapan-delete"
                    data-rumah-harapan-id="${t.id}"
                    data-rumah-harapan-name="${h(t.nama)}"
                    title="Hapus Permanen">
                <i data-lucide="trash-2" class="action-icon"></i>
            </button>
            <form id="delete-form-${t.id}" method="POST"
                action="${e.baseUrl}/rumah-harapan/${t.id}" style="display:none;">
                <input type="hidden" name="_token" value="${e.csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="current_page" value="${e.currentPage}">
            </form>
        `:`
        <a href="${e.baseUrl}/rumah-harapan/${t.id}"
            class="btn-rumah-harapan-show"
            title="Lihat Detail">
            <i data-lucide="eye" class="action-icon"></i>
        </a>
    `}function U(e,t,n,o=0){var r,d;if(t.length===0){const i=((r=e.searchInput)==null?void 0:r.value.trim())||"",l=((d=document.getElementById("statusFilterValue"))==null?void 0:d.value.trim())||"";R(e,i!==""||l!=="");return}let s="";t.forEach((i,l)=>{const a=n+l,c=i.is_active?"status-active":"status-inactive",u=i.is_active?"Aktif":"Non-Aktif";s+=`
            <tr class="rumah-harapan-row" data-id="${i.id}">
                <td class="rumah-harapan-cell">
                    <span class="item-number">${a}</span>
                </td>
                <td class="rumah-harapan-cell rh-kode-cell" title="${h(i.kode)}">
                    <span class="rumah-harapan-kode">${h(i.kode)}</span>
                </td>
                <td class="rumah-harapan-cell rh-nama-cell" title="${h(i.nama)}">
                    <span class="rumah-harapan-nama">${h(i.nama)}</span>
                </td>
                <td class="rumah-harapan-cell rh-status-cell">
                    <span class="status-badge ${c}">${u}</span>
                </td>
                <td class="rumah-harapan-cell rh-alamat-cell" title="${h(i.alamat)}">
                    ${h(i.alamat)}
                </td>
                <td class="rumah-harapan-cell rumah-harapan-actions">
                    ${_(e,i)}
                </td>
            </tr>
        `}),e.tableContainer.innerHTML=s,window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function F(e,t,n){if(n<=1){e.paginationContainer.style.display="none";return}e.paginationContainer.style.display="block";const s=window.innerWidth<=480?0:1;let r=[];r.push(1),t>2+s&&r.push("...");const d=Math.max(2,t-s),i=Math.min(n-1,t+s);for(let a=d;a<=i;a++)a!==1&&a!==n&&r.push(a);t<n-1-s&&r.push("..."),n>1&&r.push(n);let l='<ul class="pagination">';l+=`<li class="page-item ${t===1?"disabled":""}">
        <a class="page-link" href="#" data-page="${t-1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`,r.forEach(a=>{a==="..."?l+='<li class="page-item page-item-ellipsis disabled"><span class="page-link page-link-ellipsis">...</span></li>':l+=`<li class="page-item ${t===a?"active":""}">
                <a class="page-link" href="#" data-page="${a}">${a}</a>
            </li>`}),l+=`<li class="page-item ${t===n?"disabled":""}">
        <a class="page-link" href="#" data-page="${t+1}">
            <span>Next</span>
            <i data-lucide="chevron-right" class="pagination-icon"></i>
        </a>
    </li>`,l+="</ul>",e.paginationContainer.innerHTML=l,e.paginationContainer.querySelectorAll(".page-link").forEach(a=>{a.closest(".page-item-ellipsis")||a.addEventListener("click",c=>{c.preventDefault();const u=parseInt(a.getAttribute("data-page"));u>=1&&u<=n&&u!==t&&P(()=>Promise.resolve().then(()=>V),void 0).then(m=>{m.fetchData(e,u)})})}),window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function S(e){var n;return(((n=document.getElementById("statusFilterValue"))==null?void 0:n.value.trim())||"")!==""}function g(e){const t=document.getElementById("resetFilterBtnRh");t&&(t.style.display=S()?"flex":"none")}async function f(e,t=1,n=!1){var r,d;e.currentPage=t;const o=((r=e.searchInput)==null?void 0:r.value.trim())||"",s=((d=document.getElementById("statusFilterValue"))==null?void 0:d.value.trim())||"";v(!0,e,n);try{const i=new URL(`${e.baseUrl}/rumah-harapan`);i.searchParams.append("search",o),s!==""&&i.searchParams.append("is_active",s),i.searchParams.append("page",t),i.searchParams.append("per_page",e.perPage);const l=await fetch(i.toString(),{headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest"}});if(!l.ok)throw new Error(`HTTP error! status: ${l.status}`);const a=await l.json();U(e,a.data,a.first_item,a.total),F(e,a.current_page,a.last_page),g(e),e.onAfterRender&&e.onAfterRender(e);const c=new URL(`${e.baseUrl}/rumah-harapan`);o&&c.searchParams.append("search",o),s!==""&&c.searchParams.append("is_active",s),t>1&&c.searchParams.append("page",t),window.history.pushState({path:c.toString()},"",c.toString())}catch(i){console.error("Fetch error:",i),q("Gagal memuat data asrama. Silakan coba lagi.",e)}finally{v(!1,e,n)}}function k(e){const t=document.getElementById("statusFilterValue"),n=document.getElementById("statusFilterTrigger"),o=document.getElementById("statusFilterMenu");if((n==null?void 0:n.getAttribute("aria-expanded"))==="true"){n.setAttribute("aria-expanded","false"),o&&(o.style.display="none");const s=n.querySelector(".dropdown-chevron");s&&(s.style.transform="rotate(0deg)")}if(t){t.value="";const s=n==null?void 0:n.querySelector(".dropdown-value");s&&(s.textContent="Semua Status")}g(),f(e,1,!1)}function B(e){e.searchInput&&e.searchInput.addEventListener("input",()=>{clearTimeout(e.searchTimeout),e.searchTimeout=setTimeout(()=>{f(e,1,!0)},300)})}function L(e){const t=document.getElementById("filterActive");t&&t.addEventListener("change",()=>{f(e,1)})}const V=Object.freeze(Object.defineProperty({__proto__:null,fetchData:f,hasActiveFilters:S,resetFilters:k,setupFilterHandlers:L,setupSearchHandler:B,updateResetButtonVisibility:g},Symbol.toStringTag,{value:"Module"}));function D(){document.querySelector(".rumah-harapan-form-page")&&(x(),H())}function x(){const e=document.querySelectorAll("[data-dropdown]");if(e.length===0){console.warn("No dropdowns found with [data-dropdown]");return}e.forEach(t=>{if(t.dataset.initialized==="true")return;const n=t.querySelector("[data-dropdown-trigger]"),o=t.querySelector("[data-dropdown-menu]"),s=t.querySelectorAll("[data-dropdown-option]"),r=t.previousElementSibling,d=n.querySelector(".dropdown-value");if(!n||!o||!r){console.warn("Missing dropdown elements",{trigger:n,menu:o,hiddenInput:r});return}const i=(a=null)=>{const c=n.getAttribute("aria-expanded")==="true",u=a!==null?a:!c;n.setAttribute("aria-expanded",String(u)),o.style.display=u?"block":"none";const m=n.querySelector(".dropdown-chevron");m&&(m.style.transform=u?"rotate(180deg)":"rotate(0deg)")},l=a=>{const c=a.dataset.value,u=a.textContent.trim();d&&(d.textContent=u||"Pilih opsi"),r&&r.type==="hidden"&&(r.value=c,r.dispatchEvent(new Event("change",{bubbles:!0}))),i(!1),s.forEach(m=>m.classList.remove("selected")),a.classList.add("selected")};s.forEach(a=>{a.addEventListener("click",c=>{c.stopPropagation(),l(a)})}),n.addEventListener("click",a=>{a.stopPropagation(),i()}),document.addEventListener("click",a=>{t.contains(a.target)||i(!1)}),document.addEventListener("keydown",a=>{a.key==="Escape"&&i(!1)}),t.dataset.initialized="true"})}function H(){const e=document.querySelector(".rumah-harapan-form");if(!e)return;const t=e.querySelector('button[type="submit"]');t&&e.addEventListener("submit",function(n){const o=["kode","nama","alamat","kota","provinsi"];let s=!0;if(o.forEach(r=>{const d=document.getElementById(r);d&&!d.value.trim()&&(s=!1,d.classList.contains("form-input")&&d.classList.add("is-invalid"))}),s){const r=t.querySelector(".btn-text"),d=t.querySelector(".btn-loader");r&&d&&(r.style.display="none",d.style.display="flex",t.disabled=!0)}else{n.preventDefault();const r=document.querySelector(".is-invalid");r&&(r.scrollIntoView({behavior:"smooth",block:"center"}),r.focus())}})}const y="rumahHarapan_returnUrl";function b(){sessionStorage.setItem(y,window.location.href)}let p=null;function N(){const e=document.getElementById("statusFilterTrigger"),t=document.getElementById("statusFilterMenu"),n=document.getElementById("statusFilterValue"),o=document.getElementById("resetFilterBtnRh");if(!e||!t||!n){console.warn("Status filter dropdown elements not found");return}const s=e.querySelector(".dropdown-value");function r(a=null){const c=e.getAttribute("aria-expanded")==="true",u=a!==null?a:!c;e.setAttribute("aria-expanded",String(u)),t.style.display=u?"block":"none";const m=e.querySelector(".dropdown-chevron");m&&(m.style.transform=u?"rotate(180deg)":"rotate(0deg)")}function d(a){const c=a.dataset.value,u=a.textContent.trim();s&&(s.textContent=u),n.value=c,r(!1),g(),f(p,1,!1)}e.addEventListener("click",a=>{a.stopPropagation(),r()}),t.querySelectorAll(".dropdown-option").forEach(a=>{a.addEventListener("click",c=>{c.stopPropagation(),d(a)})}),o&&o.addEventListener("click",a=>{a.stopPropagation(),k(p)}),document.addEventListener("click",a=>{const c=e.closest(".rumah-harapan-status-filter");c&&!c.contains(a.target)&&r(!1)}),document.addEventListener("keydown",a=>{a.key==="Escape"&&r(!1)});const l=new URLSearchParams(window.location.search).get("is_active");if(l!==null){n.value=l;const a=t.querySelector(`[data-value="${l}"]`);a&&s&&(s.textContent=a.textContent.trim())}}function O(){const e=new URLSearchParams(window.location.search),t=e.get("success"),n=e.get("error");$(()=>{if(t){M(p,t);const o=new URL(window.location.href);o.searchParams.delete("success"),window.history.replaceState({},"",o.toString())}if(n){T(p,n);const o=new URL(window.location.href);o.searchParams.delete("error"),window.history.replaceState({},"",o.toString())}})}function E(){document.querySelectorAll(".btn-rumah-harapan-delete").forEach(e=>{e.addEventListener("click",function(){A(p);const t=this.dataset.rumahHarapanId,n=this.dataset.rumahHarapanName;p.alertTitle.textContent="Konfirmasi Hapus Permanen",p.alertMessage.textContent=`Hapus permanen data asrama ${n}? Tindakan ini TIDAK DAPAT DIBATALKAN!`,p.alertConfirmBtn.textContent="Hapus Permanen",p.alertConfirmBtn.className="alert-btn alert-btn-confirm",p.alertConfirmBtn.onclick=()=>{document.getElementById(`delete-form-${t}`).submit()},p.alertModal.style.display="flex",document.body.style.overflow="hidden"})}),document.querySelectorAll(".btn-rumah-harapan-edit").forEach(e=>{e.addEventListener("click",function(){b();const t=this.dataset.rumahHarapanId;window.location.href=`${p.baseUrl}/rumah-harapan/${t}/edit`})}),document.querySelectorAll(".btn-rumah-harapan-show").forEach(e=>{e.addEventListener("click",()=>{b()})})}function I(){if(document.querySelector(".rumah-harapan-form-page")){D();const r=sessionStorage.getItem(y),d=document.getElementById("btnBack");d&&(r?d.onclick=()=>{window.location.href=r}:d.onclick=()=>{var l;window.location.href=`${((l=window.userData)==null?void 0:l.baseUrl)??""}/rumah-harapan`});const i=document.getElementById("currentPageInput");if(i&&r)try{const l=parseInt(new URL(r).searchParams.get("page"))||1;i.value=l}catch{i.value=1}return}p={searchInput:document.getElementById("searchInput"),searchLoader:document.getElementById("searchLoader"),tableContainer:document.getElementById("rumahHarapanTableBody"),paginationContainer:document.getElementById("paginationContainer"),alertModal:document.getElementById("alertModal"),alertTitle:document.getElementById("alertModalTitle"),alertMessage:document.getElementById("alertModalMessage"),alertCancelBtn:document.getElementById("alertCancelBtn"),alertConfirmBtn:document.getElementById("alertConfirmBtn"),alertContainer:document.querySelector(".alert-modal-container"),alertIcon:document.querySelector(".alert-icon"),searchTimeout:null,currentPage:1,perPage:7,csrfToken:window.userData.csrfToken,baseUrl:window.userData.baseUrl,userRole:window.userData.userRole,onAfterRender:E},C(p),B(p),L(p),N(),g(),E(),O(),window.addEventListener("popstate",()=>{const r=new URLSearchParams(window.location.search),d=parseInt(r.get("page"))||1,i=r.get("search")||"",l=r.get("is_active")||"";p.searchInput&&(p.searchInput.value=i);const a=document.getElementById("statusFilterValue"),c=document.getElementById("statusFilterTrigger"),u=document.getElementById("statusFilterMenu");if(a){a.value=l;const m=c==null?void 0:c.querySelector(".dropdown-value");if(m){const w=u==null?void 0:u.querySelector(`[data-value="${l}"]`);m.textContent=w?w.textContent.trim():"Semua Status"}}g(),f(p,d)});const e=new URLSearchParams(window.location.search),t=parseInt(e.get("page"))||1,n=e.get("search")||"",o=e.get("is_active")||"";p.searchInput&&(p.searchInput.value=n);const s=document.getElementById("statusFilterValue");s&&(s.value=o),sessionStorage.removeItem(y),f(p,t)}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",I):I();
