import{_ as E}from"./preload-helper-f61836a9.js";function v(e){if(!e)return"";const a=document.createElement("div");return a.textContent=e,a.innerHTML}function S(e){try{const[a,i]=e.split(" "),[o,n]=i.split(":");return`${a} ${o}:${n}`}catch{return e}}function g(e,a,i=!1){var o,n;e?(i&&((o=a.searchLoader)==null||o.classList.add("active")),a.tableBody.innerHTML=`
            <tr class="table-row">
                <td colspan="6" class="text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `):i&&((n=a.searchLoader)==null||n.classList.remove("active"))}function b(e,a){a.tableBody.innerHTML=`
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="error-message">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="mt-2">${v(e)}</p>
                    <button class="btn-reload mt-3" onclick="location.reload()">
                        Muat Ulang
                    </button>
                </div>
            </td>
        </tr>
    `,window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function k(e,a=!1){let i,o,n;a?(i=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
                <path d="m14 8-6 6"></path>
                <path d="m8 8 6 6"></path>
            </svg>
        `,o="Data tidak ditemukan",n="Coba gunakan kata kunci atau filter yang berbeda."):(i=`
            <svg class="empty-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v3"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <circle cx="8" cy="16" r="6"></circle>
                <path d="M9.5 17.5 8 16.25V14"></path>
            </svg>
        `,o="Belum ada riwayat aktivitas",n="Aktivitas pengguna akan tercatat secara otomatis di sini."),e.tableBody.innerHTML=`
        <tr class="table-row">
            <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                    ${i}
                    <h4 class="empty-title">${o}</h4>
                    <p class="empty-text">${n}</p>
                </div>
            </td>
        </tr>
    `}function H(e,a,i){var n,r,d;const o=(((n=e.searchInput)==null?void 0:n.value.trim())||"")!==""||(((r=e.modelTypeHiddenInput)==null?void 0:r.value.trim())||"")!==""||(((d=e.actionHiddenInput)==null?void 0:d.value.trim())||"")!=="";if(a.length===0){k(e,o);return}e.tableBody.innerHTML="",a.forEach((c,l)=>{const s=i+l,m=c.action==="Dibuat"?"success":c.action==="Diperbarui"?"warning":c.action==="Dihapus"?"danger":c.action==="Masuk"?"primary":c.action==="Keluar"?"secondary":"info",p=document.createElement("tr");p.className="table-row",p.innerHTML=`
            <td class="text-center">${s}</td>
            <td class="audit-user-cell">${v(c.user_name)}</td>
            <td class="audit-model-cell">${v(c.model_type)}</td>
            <td class="audit-action-cell">
                <span class="badge badge-${m}">
                    ${v(c.action)}
                </span>
            </td>
            <td class="audit-date-cell">${v(S(c.created_at))}</td>
            <td class="text-center audit-detail-cell">
                <a href="${v(c.detail_url)}" class="action-btn action-btn-view" title="Lihat Detail">
                    <i data-lucide="eye" class="action-icon"></i>
                </a>
            </td>
        `,e.tableBody.appendChild(p)}),window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function D(e,a,i){if(i<=1){e.paginationContainer.style.display="none";return}e.paginationContainer.style.display="block";const n=window.innerWidth<=480?0:1;let r=[];r.push(1),a>2+n&&r.push("...");const d=Math.max(2,a-n),c=Math.min(i-1,a+n);for(let s=d;s<=c;s++)s!==1&&s!==i&&r.push(s);a<i-1-n&&r.push("..."),i>1&&i!==1&&r.push(i);let l='<ul class="pagination">';l+=`<li class="page-item ${a===1?"disabled":""}">
        <a class="page-link" href="#" data-page="${a-1}">
            <i data-lucide="chevron-left" class="pagination-icon"></i>
            <span>Previous</span>
        </a>
    </li>`,r.forEach(s=>{s==="..."?l+='<li class="page-item page-item-ellipsis disabled"><span class="page-link page-link-ellipsis">...</span></li>':l+=`<li class="page-item ${a===s?"active":""}">
                <a class="page-link" href="#" data-page="${s}">${s}</a>
            </li>`}),l+=`<li class="page-item ${a===i?"disabled":""}">
        <a class="page-link" href="#" data-page="${a+1}">
            <span>Next</span>
            <i data-lucide="chevron-right" class="pagination-icon"></i>
        </a>
    </li>`,l+="</ul>",e.paginationContainer.innerHTML=l,e.paginationContainer.querySelectorAll(".page-link").forEach(s=>{s.closest(".page-item-ellipsis")||s.addEventListener("click",m=>{m.preventDefault();const p=parseInt(s.getAttribute("data-page"));p>=1&&p<=i&&p!==a&&E(()=>Promise.resolve().then(()=>C),void 0).then(u=>{u.fetchData(e,p)})})}),window.lucide&&typeof window.lucide.createIcons=="function"&&window.lucide.createIcons()}function T(e){var o,n;const a=((o=e.modelTypeHiddenInput)==null?void 0:o.value.trim())||"",i=((n=e.actionHiddenInput)==null?void 0:n.value.trim())||"";return a!==""||i!==""}function h(e){const a=document.getElementById("resetFilterBtn");a&&(a.style.display=T(e)?"flex":"none")}async function y(e,a=1,i=!1){var d,c,l;e.currentPage=a;const o=((d=e.searchInput)==null?void 0:d.value.trim())||"",n=((c=e.modelTypeHiddenInput)==null?void 0:c.value.trim())||"",r=((l=e.actionHiddenInput)==null?void 0:l.value.trim())||"";g(!0,e,i);try{const s=new URL(`${e.baseUrl}/audit-log`);s.searchParams.append("search",o),n&&s.searchParams.append("model_type",n),r&&s.searchParams.append("action",r),s.searchParams.append("page",a),s.searchParams.append("per_page",e.perPage);const m=await fetch(s.toString(),{headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest"}});if(!m.ok)throw new Error(`HTTP error status: ${m.status}`);const p=await m.json();H(e,p.data,p.first_item),D(e,p.current_page,p.last_page),h(e),e.onAfterRender&&e.onAfterRender(e);const u=new URL(window.location.href);u.search="",o&&u.searchParams.append("search",o),n&&u.searchParams.append("model_type",n),r&&u.searchParams.append("action",r),a>1&&u.searchParams.append("page",a),window.history.pushState({path:u.toString()},"",u.toString())}catch(s){console.error("Fetch error:",s),b("Gagal memuat data audit logs. Silakan coba lagi.",e)}finally{g(!1,e,i)}}function L(e){var a,i,o,n;(a=e.modelTypeMenu)!=null&&a.classList.contains("show")&&(e.modelTypeMenu.classList.remove("show"),(i=e.modelTypeTrigger)==null||i.classList.remove("active")),(o=e.actionMenu)!=null&&o.classList.contains("show")&&(e.actionMenu.classList.remove("show"),(n=e.actionTrigger)==null||n.classList.remove("active")),e.modelTypeHiddenInput&&e.modelTypeSelectedText&&(e.modelTypeHiddenInput.value="",e.modelTypeSelectedText.textContent="Semua Model",e.modelTypeItems.forEach(r=>r.classList.remove("active"))),e.actionHiddenInput&&e.actionSelectedText&&(e.actionHiddenInput.value="",e.actionSelectedText.textContent="Semua Aksi",e.actionItems.forEach(r=>r.classList.remove("active"))),h(e),y(e,1,!1)}function I(e){e.searchInput&&e.searchInput.addEventListener("input",a=>{clearTimeout(e.searchTimeout),e.searchTimeout=setTimeout(()=>{y(e,1,!0)},300)})}const C=Object.freeze(Object.defineProperty({__proto__:null,fetchData:y,hasActiveFilters:T,resetFilters:L,setupSearchHandler:I,updateResetButtonVisibility:h},Symbol.toStringTag,{value:"Module"})),M="auditLog_returnUrl";function _(){sessionStorage.setItem(M,window.location.href)}let t=null;function f(e){var o;if(!t.actionMenu||!((o=t.actionItems)!=null&&o.length))return;const a=e?t.modelActionMap[e]||[]:Object.values(t.modelActionMap).flat(),i=new Set(a.map(n=>n.value));t.actionItems.forEach(n=>{const r=n.dataset.value,d=!r||i.has(r);n.style.display=d?"":"none",!d&&n.classList.contains("active")&&(n.classList.remove("active"),t.actionHiddenInput.value="",t.actionSelectedText.textContent="Semua Aksi")})}function B(){t.modelTypeTrigger&&t.modelTypeMenu&&(t.modelTypeTrigger.addEventListener("click",e=>{var a,i;e.stopPropagation(),(a=t.actionMenu)==null||a.classList.remove("show"),(i=t.actionTrigger)==null||i.classList.remove("active"),t.modelTypeMenu.classList.toggle("show"),t.modelTypeTrigger.classList.toggle("active")}),t.modelTypeItems.forEach(e=>{e.addEventListener("click",a=>{a.stopPropagation();const i=e.dataset.value,o=e.textContent.trim();t.modelTypeSelectedText.textContent=o,t.modelTypeItems.forEach(n=>n.classList.remove("active")),e.classList.add("active"),t.modelTypeHiddenInput.value=i,t.modelTypeMenu.classList.remove("show"),t.modelTypeTrigger.classList.remove("active"),f(i),h(t),y(t,1)})})),t.actionTrigger&&t.actionMenu&&(t.actionTrigger.addEventListener("click",e=>{var a,i;e.stopPropagation(),(a=t.modelTypeMenu)==null||a.classList.remove("show"),(i=t.modelTypeTrigger)==null||i.classList.remove("active"),t.actionMenu.classList.toggle("show"),t.actionTrigger.classList.toggle("active")}),t.actionItems.forEach(e=>{e.addEventListener("click",a=>{a.stopPropagation();const i=e.dataset.value,o=e.textContent.trim();t.actionSelectedText.textContent=o,t.actionItems.forEach(n=>n.classList.remove("active")),e.classList.add("active"),t.actionHiddenInput.value=i,t.actionMenu.classList.remove("show"),t.actionTrigger.classList.remove("active"),h(t),y(t,1)})})),document.addEventListener("click",e=>{var a,i,o,n;t.modelTypeDropdown&&!t.modelTypeDropdown.contains(e.target)&&((a=t.modelTypeMenu)==null||a.classList.remove("show"),(i=t.modelTypeTrigger)==null||i.classList.remove("active")),t.actionDropdown&&!t.actionDropdown.contains(e.target)&&((o=t.actionMenu)==null||o.classList.remove("show"),(n=t.actionTrigger)==null||n.classList.remove("active"))}),document.addEventListener("keydown",e=>{var a,i,o,n;e.key==="Escape"&&((a=t.modelTypeMenu)==null||a.classList.remove("show"),(i=t.modelTypeTrigger)==null||i.classList.remove("active"),(o=t.actionMenu)==null||o.classList.remove("show"),(n=t.actionTrigger)==null||n.classList.remove("active"))}),t.resetFilterBtn&&t.resetFilterBtn.addEventListener("click",e=>{e.stopPropagation(),L(t)})}function A(){const e=window.userData.initialFilters||{};if(t.searchInput&&e.search&&(t.searchInput.value=e.search),e.model_type&&t.modelTypeHiddenInput){t.modelTypeHiddenInput.value=e.model_type;const a=Array.from(t.modelTypeItems).find(i=>i.dataset.value===e.model_type);a&&(t.modelTypeSelectedText.textContent=a.textContent.trim(),a.classList.add("active"),f(e.model_type))}if(e.action&&t.actionHiddenInput){t.actionHiddenInput.value=e.action;const a=Array.from(t.actionItems).find(i=>i.dataset.value===e.action);a&&(t.actionSelectedText.textContent=a.textContent.trim(),a.classList.add("active"))}h(t)}function $(){document.querySelectorAll(".action-btn-view").forEach(e=>{e.addEventListener("click",()=>{_()})})}function w(){t={searchInput:document.getElementById("searchInput"),searchLoader:document.getElementById("searchLoader"),resetFilterBtn:document.getElementById("resetFilterBtn"),modelTypeDropdown:document.getElementById("modelTypeDropdown"),modelTypeTrigger:document.querySelector("#modelTypeDropdown .dropdown-trigger"),modelTypeMenu:document.querySelector("#modelTypeDropdown .dropdown-menu"),modelTypeItems:document.querySelectorAll("#modelTypeDropdown .dropdown-item"),modelTypeHiddenInput:document.getElementById("modelTypeFilterValue"),modelTypeSelectedText:document.querySelector("#modelTypeDropdown .selected-text"),actionDropdown:document.getElementById("actionDropdown"),actionTrigger:document.querySelector("#actionDropdown .dropdown-trigger"),actionMenu:document.querySelector("#actionDropdown .dropdown-menu"),actionItems:document.querySelectorAll("#actionDropdown .dropdown-item"),actionHiddenInput:document.getElementById("actionFilterValue"),actionSelectedText:document.querySelector("#actionDropdown .selected-text"),tableBody:document.getElementById("auditLogsTableBody"),paginationContainer:document.getElementById("paginationContainer"),searchTimeout:null,currentPage:1,perPage:7,csrfToken:window.userData.csrfToken,baseUrl:window.userData.baseUrl,modelActionMap:window.userData.modelActionMap||{},onAfterRender:$},A(),B(),I(t),window.addEventListener("popstate",i=>{const o=new URLSearchParams(window.location.search),n=parseInt(o.get("page"))||1,r=o.get("search")||"",d=o.get("model_type")||"",c=o.get("action")||"";if(t.searchInput&&(t.searchInput.value=r),t.modelTypeHiddenInput&&(t.modelTypeHiddenInput.value=d),t.actionHiddenInput&&(t.actionHiddenInput.value=c),d){const l=Array.from(t.modelTypeItems).find(s=>s.dataset.value===d);l&&(t.modelTypeSelectedText.textContent=l.textContent.trim()),f(d)}else t.modelTypeSelectedText.textContent="Semua Model";if(c){const l=Array.from(t.actionItems).find(s=>s.dataset.value===c);l&&(t.actionSelectedText.textContent=l.textContent.trim())}else t.actionSelectedText.textContent="Semua Aksi";h(t),y(t,n)}),sessionStorage.removeItem(M);const e=new URLSearchParams(window.location.search),a=parseInt(e.get("page"))||1;y(t,a)}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",w):w();
