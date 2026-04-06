class i{static get CONFIG(){return{TRANSITION_LOADING_TIME:800,MINIMUM_LOADING_TIME:500}}constructor(){this.init()}init(){this.handleSidebarNavigation()}showLoader(){const t=document.createElement("div");t.id="transition-loader",t.className="dashboard-transition-loader dashboard-transition-loader--active",t.innerHTML=`
            <div class="dashboard-transition-loader__content">
                <div class="loader-main">
                    <div class="loader-main__spinner"></div>
                </div>
            </div>
        `,document.body.appendChild(t)}hideLoader(){const t=document.getElementById("transition-loader");t&&(t.classList.remove("dashboard-transition-loader--active"),setTimeout(()=>{t.remove()},200))}handleSidebarNavigation(){document.addEventListener("click",t=>{var n;const e=t.target.closest(".sidebar .nav-link");if(!e)return;const a=e.getAttribute("href");!a||a.startsWith("#")||e.querySelector('[data-lucide="log-out"]')||(n=e.textContent)!=null&&n.includes("Logout")||(t.preventDefault(),this.showLoader(),setTimeout(()=>{window.location.href=a},100))})}static show(){new i().showLoader()}static hide(){new i().hideLoader()}static init(){return new i}}export{i as PageTransition};
