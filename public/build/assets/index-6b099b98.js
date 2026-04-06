import{i as I,w as L,r as S,s as k,a as B}from"./alert-modal-f128ce56.js";import{_ as T}from"./preload-helper-f61836a9.js";function h(e){if(!e)return"";const t=document.createElement("div");return t.textContent=e,t.innerHTML}function y(e,t){e?(t.searchLoader.classList.add("active"),t.tableBody.innerHTML=`
            <tr class="table-row">
                <td colspan="5" class="text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `):t.searchLoader.classList.remove("active")}function M(e,t){t.tableBody.innerHTML=`
        <tr class="table-row">
            <td colspan="5" class="text-center py-5">
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
    `,window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function $(e,t=!1){let a,n,s;t?(a=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
                <path d="m14 8-6 6"></path>
                <path d="m8 8 6 6"></path>
            </svg>
        `,n="Data tidak ditemukan",s="Coba gunakan kata kunci nama atau email yang berbeda."):(a=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <path d="M20 8v6M23 11h-6"></path>
            </svg>
        `,n="Belum ada pengguna",s="Belum ada data pengguna yang terdaftar di sistem."),e.tableBody.innerHTML=`
        <tr class="table-row">
            <td colspan="5" class="text-center py-5">
                <div class="empty-state">
                    ${a}
                    <h4 class="empty-title">${n}</h4>
                    <p class="empty-text">${s}</p>
                    ${t?"":`
                    <div class="empty-actions">
                        <a href="${e.baseUrl}/users/create" class="empty-link">
                            Tambah Pengguna
                        </a>
                    </div>`}
                </div>
            </td>
        </tr>
    `}function A(e,t,a){var s;const n=(((s=e.searchInput)==null?void 0:s.value.trim())||"")!=="";if(t.length===0){$(e,n);return}e.tableBody.innerHTML="",t.forEach((r,o)=>{const p=a+o,d=r.role==="admin"?"admin":"petugas",l=r.role.charAt(0).toUpperCase()+r.role.slice(1),i=document.createElement("tr");i.className="table-row",i.innerHTML=`
            <td class="text-center">${p}</td>
            <td class="user-name-cell" title="${h(r.name)}">${h(r.name)}</td>
            <td class="user-email-cell" title="${h(r.email)}">${h(r.email)}</td>
            <td class="user-role-cell">
                <span class="badge badge-${d}">
                    ${h(l)}
                </span>
            </td>
            <td class="action-cell">
                ${C(e,r)}
            </td>
        `,e.tableBody.appendChild(i)}),window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function C(e,t){return`
        <a href="${e.baseUrl}/users/${t.id}/edit" class="action-btn action-btn-edit" title="Edit">
            <i data-lucide="edit" class="action-icon"></i>
        </a>
        <button type="button" class="action-btn action-btn-delete btn-hard-delete"
            data-user-id="${t.id}"
            data-user-name="${h(t.name)}"
            title="Hapus Permanen">
            <i data-lucide="trash-2" class="action-icon"></i>
        </button>
        <form id="hard-delete-form-${t.id}" method="POST" action="${e.baseUrl}/users/${t.id}" style="display:none;">
            <input type="hidden" name="_token" value="${e.csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="current_page" value="${e.currentPage}">
        </form>
    `}function U(e,t,a){if(a<=1){e.paginationContainer.style.display="none";return}e.paginationContainer.style.display="block";const s=window.innerWidth<=480?0:1;let r=[];r.push(1),t>2+s&&r.push("...");const o=Math.max(2,t-s),p=Math.min(a-1,t+s);for(let l=o;l<=p;l++)l!==1&&l!==a&&r.push(l);t<a-1-s&&r.push("..."),a>1&&r.push(a);let d='<ul class="pagination">';d+=`<li class="page-item ${t===1?"disabled":""}">
        <a class="page-link" href="#" data-page="${t-1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`,r.forEach(l=>{l==="..."?d+='<li class="page-item page-item-ellipsis disabled"><span class="page-link page-link-ellipsis">...</span></li>':d+=`<li class="page-item ${t===l?"active":""}">
                <a class="page-link" href="#" data-page="${l}">${l}</a>
            </li>`}),d+=`<li class="page-item ${t===a?"disabled":""}">
        <a class="page-link" href="#" data-page="${t+1}">
            <span>Next</span>
            <i data-lucide="chevron-right" class="pagination-icon"></i>
        </a>
    </li>`,d+="</ul>",e.paginationContainer.innerHTML=d,e.paginationContainer.querySelectorAll(".page-link").forEach(l=>{l.closest(".page-item-ellipsis")||l.addEventListener("click",i=>{i.preventDefault();const u=parseInt(l.getAttribute("data-page"));u>=1&&u<=a&&u!==t&&T(()=>Promise.resolve().then(()=>P),void 0).then(m=>{m.fetchData(e,u)})})}),window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}async function f(e,t=1){e.currentPage=t;const a=e.searchInput.value.trim();y(!0,e);try{const n=new URL(`${e.baseUrl}/users`);n.searchParams.append("search",a),n.searchParams.append("page",t),n.searchParams.append("per_page",e.perPage);const s=await fetch(n.toString(),{headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest"}});if(!s.ok)throw new Error(`HTTP error! status: ${s.status}`);const r=await s.json();A(e,r.data,r.first_item),U(e,r.current_page,r.last_page),e.onAfterRender&&e.onAfterRender(e);const o=new URL(`${e.baseUrl}/users`);a&&o.searchParams.append("search",a),t>1&&o.searchParams.append("page",t),window.history.pushState({path:o.toString()},"",o.toString())}catch(n){console.error("Fetch error:",n),M("Gagal memuat data. Silakan coba lagi.",e)}finally{y(!1,e)}}function v(e){e.searchInput&&e.searchInput.addEventListener("input",t=>{clearTimeout(e.searchTimeout),e.searchTimeout=setTimeout(()=>{f(e,1)},300)})}const P=Object.freeze(Object.defineProperty({__proto__:null,fetchData:f,setupSearchHandler:v},Symbol.toStringTag,{value:"Module"}));function _(){document.querySelector(".users-form-page")&&(q(),H(),R())}function q(){document.querySelectorAll(".toggle-password").forEach(e=>{e.addEventListener("click",function(){const t=this.getAttribute("data-target"),a=document.getElementById(t),n=this.querySelector(".eye-icon");!a||!n||(a.type==="password"?(a.type="text",n.innerHTML='<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'):(a.type="password",n.innerHTML='<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>'))})})}function H(){document.querySelectorAll("[data-dropdown]").forEach(t=>{if(t.dataset.initialized==="true")return;const a=t.querySelector("[data-dropdown-trigger]"),n=t.querySelector("[data-dropdown-menu]"),s=t.querySelectorAll("[data-dropdown-option]"),r=a==null?void 0:a.querySelector(".dropdown-value"),o=document.getElementById("role"),p=a==null?void 0:a.querySelector(".dropdown-chevron");if(!a||!n||!o)return;const d=(i=null)=>{const u=a.getAttribute("aria-expanded")==="true",m=i!==null?i:!u;a.setAttribute("aria-expanded",String(m)),n.classList.toggle("show",m),p&&(p.style.transform=m?"rotate(180deg)":"rotate(0deg)",p.style.transition="transform 0.2s ease")},l=i=>{const u=i.getAttribute("value"),m=i.textContent.trim();r&&(r.textContent=m),o&&(o.value=u,o.dispatchEvent(new Event("input",{bubbles:!0}))),d(!1),s.forEach(E=>E.classList.remove("selected")),i.classList.add("selected")};s.forEach(i=>{i.addEventListener("click",u=>{u.stopPropagation(),l(i)})}),a.addEventListener("click",i=>{i.stopPropagation(),d()}),document.addEventListener("click",i=>{t.contains(i.target)||d(!1)}),document.addEventListener("keydown",i=>{i.key==="Escape"&&d(!1)}),t.dataset.initialized="true"})}function R(){const e=document.querySelector(".user-form");if(!e)return;const t=e.querySelector('button[type="submit"]');t&&e.addEventListener("submit",function(){const a=t.querySelector(".btn-text"),n=t.querySelector(".btn-loader");a&&n&&(a.style.display="none",n.style.display="flex",t.disabled=!0)})}const g="users_returnUrl";function D(){sessionStorage.setItem(g,window.location.href)}let c=null;function N(){const e=new URLSearchParams(window.location.search),t=e.get("success"),a=e.get("error");L(()=>{if(t){k(c,t);const n=new URL(window.location.href);n.searchParams.delete("success"),window.history.replaceState({},"",n.toString())}if(a){B(c,a);const n=new URL(window.location.href);n.searchParams.delete("error"),window.history.replaceState({},"",n.toString())}})}function w(){document.querySelectorAll(".btn-hard-delete").forEach(e=>{e.addEventListener("click",function(){S(c);const t=this.dataset.userId,a=this.dataset.userName;c.alertTitle.textContent="Konfirmasi Hapus Permanen",c.alertMessage.textContent=`Hapus permanen pengguna ${a}? Tindakan ini TIDAK DAPAT DIBATALKAN!`,c.alertConfirmBtn.textContent="Hapus Permanen",c.alertConfirmBtn.className="alert-btn alert-btn-confirm",c.alertConfirmBtn.onclick=()=>{document.getElementById(`hard-delete-form-${t}`).submit()},c.alertModal.style.display="flex",document.body.style.overflow="hidden"})}),document.querySelectorAll(".action-btn-edit").forEach(e=>{e.addEventListener("click",()=>{D()})})}function b(){if(document.querySelector(".users-form-page")){_();const n=sessionStorage.getItem(g),s=document.getElementById("btnBack");s&&(n?s.onclick=()=>{window.location.href=n}:s.onclick=()=>{var o;window.location.href=`${((o=window.userData)==null?void 0:o.baseUrl)??""}/users`});const r=document.getElementById("currentPageInput");if(r&&n)try{const o=parseInt(new URL(n).searchParams.get("page"))||1;r.value=o}catch{r.value=1}return}c={searchInput:document.getElementById("searchInput"),searchLoader:document.getElementById("searchLoader"),tableBody:document.getElementById("usersTableBody"),paginationContainer:document.getElementById("paginationContainer"),alertModal:document.getElementById("alertModal"),alertTitle:document.getElementById("alertModalTitle"),alertMessage:document.getElementById("alertModalMessage"),alertCancelBtn:document.getElementById("alertCancelBtn"),alertConfirmBtn:document.getElementById("alertConfirmBtn"),alertContainer:document.querySelector(".alert-modal-container"),alertIcon:document.querySelector(".alert-icon"),searchTimeout:null,currentPage:1,perPage:7,csrfToken:window.userData.csrfToken,baseUrl:window.userData.baseUrl,onAfterRender:w},I(c),v(c),w(),N(),window.addEventListener("popstate",()=>{const n=new URLSearchParams(window.location.search),s=parseInt(n.get("page"))||1,r=n.get("search")||"";c.searchInput&&(c.searchInput.value=r),f(c,s)});const e=new URLSearchParams(window.location.search),t=parseInt(e.get("page"))||1,a=e.get("search")||"";c.searchInput&&(c.searchInput.value=a),sessionStorage.removeItem(g),f(c,t)}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",b):b();
