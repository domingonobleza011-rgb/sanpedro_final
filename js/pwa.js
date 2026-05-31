// ============================================================
//  BMIS — Barangay San Pedro — PWA Registration & Install
// ============================================================

(function () {
    'use strict';

    // ── 1. Register Service Worker ──────────────────────────
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register("/sw.js", { scope: "/" })
                .then(function (reg) {
                    console.log('[PWA] Service Worker registered. Scope:', reg.scope);

                    // Check for updates every 60 seconds
                    setInterval(() => reg.update(), 60000);

                    // Notify user when a new version is available
                    reg.addEventListener('updatefound', function () {
                        const newWorker = reg.installing;
                        newWorker.addEventListener('statechange', function () {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                showUpdateBanner();
                            }
                        });
                    });
                })
                .catch(function (err) {
                    console.warn('[PWA] Service Worker registration failed:', err);
                });

            // Reload page when SW controller changes (after update)
            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', function () {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        });
    }

    // ── 2. Install Prompt (Add to Home Screen) ──────────────
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;

        // Only show banner if not already installed & not dismissed recently
        const dismissed = localStorage.getItem('pwa-install-dismissed');
        const dismissedAt = dismissed ? parseInt(dismissed) : 0;
        const daysSince = (Date.now() - dismissedAt) / (1000 * 60 * 60 * 24);

        if (!dismissed || daysSince > 7) {
            setTimeout(showInstallBanner, 3000); // slight delay feels more natural
        }
    });

    // ── 3. Install Banner UI ────────────────────────────────
    function showInstallBanner() {
        if (document.getElementById('pwa-install-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.innerHTML = `
            <div style="
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 8px 32px rgba(15,45,90,0.18);
                padding: 14px 18px;
                display: flex;
                align-items: center;
                gap: 14px;
                z-index: 9999;
                max-width: 380px;
                width: calc(100% - 40px);
                border-left: 4px solid #1a4480;
                animation: slideUp 0.35s ease;
            ">
                <img src="/icons/pwa/icon-72x72.png" width="44" height="44"
                     style="border-radius:10px;flex-shrink:0;"
                     onerror="this.style.display='none'">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:0.88rem;color:#0f2d5a;line-height:1.2;">
                        Install BMIS App
                    </div>
                    <div style="font-size:0.76rem;color:#718096;margin-top:2px;">
                        Add to home screen for quick access
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
                    <button id="pwa-install-btn" style="
                        background: linear-gradient(135deg,#0f2d5a,#2b5ea7);
                        color:#fff;border:none;border-radius:8px;
                        padding:6px 14px;font-size:0.78rem;font-weight:600;
                        cursor:pointer;white-space:nowrap;
                    ">Install</button>
                    <button id="pwa-dismiss-btn" style="
                        background:transparent;color:#a0aec0;border:none;
                        font-size:0.72rem;cursor:pointer;padding:2px;
                    ">Not now</button>
                </div>
            </div>
            <style>
                @keyframes slideUp {
                    from { opacity:0; transform: translateX(-50%) translateY(20px); }
                    to   { opacity:1; transform: translateX(-50%) translateY(0); }
                }
            </style>
        `;

        document.body.appendChild(banner);

        document.getElementById('pwa-install-btn').addEventListener('click', function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(choice => {
                    if (choice.outcome === 'accepted') {
                        console.log('[PWA] User accepted install prompt');
                    }
                    deferredPrompt = null;
                });
            }
            removeBanner();
        });

        document.getElementById('pwa-dismiss-btn').addEventListener('click', function () {
            localStorage.setItem('pwa-install-dismissed', Date.now().toString());
            removeBanner();
        });

        // Auto-dismiss after 12 seconds
        setTimeout(removeBanner, 12000);
    }

    function removeBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transition = 'opacity 0.3s';
            setTimeout(() => banner.remove(), 300);
        }
    }

    // ── 4. Update Available Banner ──────────────────────────
    function showUpdateBanner() {
        if (document.getElementById('pwa-update-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'pwa-update-banner';
        banner.innerHTML = `
            <div style="
                position: fixed;
                top: 16px;
                right: 16px;
                background: #0f2d5a;
                color: #fff;
                border-radius: 12px;
                box-shadow: 0 6px 24px rgba(15,45,90,0.3);
                padding: 12px 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                z-index: 9999;
                max-width: 320px;
                animation: slideIn 0.3s ease;
            ">
                <span style="font-size:1.2rem;">🔄</span>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:0.85rem;">Update Available</div>
                    <div style="font-size:0.75rem;opacity:0.8;">A new version of BMIS is ready.</div>
                </div>
                <button onclick="window.location.reload()" style="
                    background: #c9943a;color:#fff;border:none;border-radius:8px;
                    padding:6px 12px;font-size:0.78rem;font-weight:600;cursor:pointer;
                ">Reload</button>
            </div>
            <style>
                @keyframes slideIn {
                    from { opacity:0; transform: translateX(20px); }
                    to   { opacity:1; transform: translateX(0); }
                }
            </style>
        `;

        document.body.appendChild(banner);
        setTimeout(() => {
            if (document.getElementById('pwa-update-banner')) banner.remove();
        }, 15000);
    }

    // ── 5. Online/Offline Status Indicator ─────────────────
    function showConnectionToast(isOnline) {
        const existing = document.getElementById('pwa-connection-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'pwa-connection-toast';
        toast.innerHTML = `
            <div style="
                position: fixed;
                bottom: 24px;
                right: 20px;
                background: ${isOnline ? '#059669' : '#dc2626'};
                color: #fff;
                border-radius: 10px;
                padding: 10px 18px;
                font-size: 0.82rem;
                font-weight: 600;
                z-index: 9999;
                box-shadow: 0 4px 16px rgba(0,0,0,0.2);
                animation: fadeIn 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            ">
                <span>${isOnline ? '✅' : '📡'}</span>
                ${isOnline ? 'Back online' : 'You are offline'}
            </div>
            <style>
                @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
            </style>
        `;

        document.body.appendChild(toast);
        setTimeout(() => {
            if (document.getElementById('pwa-connection-toast')) toast.remove();
        }, 3000);
    }

    window.addEventListener('online',  () => showConnectionToast(true));
    window.addEventListener('offline', () => showConnectionToast(false));

    // ── 6. Track install state ──────────────────────────────
    window.addEventListener('appinstalled', function () {
        console.log('[PWA] App was installed successfully');
        deferredPrompt = null;
        removeBanner();
    });

})();