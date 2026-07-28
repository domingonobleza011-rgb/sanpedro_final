/**
 * Barangay San Pedro BMIS — PWA Registration & Install Prompt
 */

(function () {
  'use strict';

  // ── 1. Register Service Worker ──────────────────────────────────────────
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker
        .register('/sw.js', { scope: '/' })
        .then(reg => {
          console.log('[PWA] Service worker registered. Scope:', reg.scope);

          // Notify user when a new version is available
          reg.addEventListener('updatefound', () => {
            const worker = reg.installing;
            worker.addEventListener('statechange', () => {
              if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                showUpdateBanner();
              }
            });
          });
        })
        .catch(err => console.error('[PWA] Registration failed:', err));
    });

    // Reload page when SW controller changes (after update)
    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      if (!refreshing) {
        refreshing = true;
        window.location.reload();
      }
    });
  }

  // ── 2. Install Prompt (A2HS) ────────────────────────────────────────────
  let deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;

    const dismissed    = localStorage.getItem('pwa-install-dismissed');
    const dismissedAt  = dismissed ? parseInt(dismissed) : 0;
    const daysSince    = (Date.now() - dismissedAt) / (1000 * 60 * 60 * 24);

    if (!dismissed || daysSince > 7) {
      setTimeout(showInstallBanner, 3000);
    }
  });

  window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed.');
    deferredPrompt = null;
    removeBanner();
    showToast('BMIS has been added to your home screen!', 'success');
  });

  // ── 3. Install Banner UI ────────────────────────────────────────────────
  function showInstallBanner() {
    if (document.getElementById('pwa-install-banner')) return;

    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.innerHTML = `
      <div style="
        position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
        background:#fff; border-radius:14px;
        box-shadow:0 8px 32px rgba(15,45,90,0.18);
        padding:14px 18px; display:flex; align-items:center; gap:14px;
        z-index:9999; max-width:380px; width:calc(100% - 40px);
        border-left:4px solid #0f2d5a;
        animation:slideUp 0.35s ease;">
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
            background:linear-gradient(135deg,#0f2d5a,#2b5ea7);
            color:#fff;border:none;border-radius:8px;
            padding:6px 14px;font-size:0.78rem;font-weight:600;
            cursor:pointer;white-space:nowrap;">Install</button>
          <button id="pwa-dismiss-btn" style="
            background:transparent;color:#a0aec0;border:none;
            font-size:0.72rem;cursor:pointer;padding:2px;">Not now</button>
        </div>
      </div>
      <style>
        @keyframes slideUp {
          from { opacity:0; transform:translateX(-50%) translateY(20px); }
          to   { opacity:1; transform:translateX(-50%) translateY(0); }
        }
      </style>`;

    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(choice => {
          console.log('[PWA] User choice:', choice.outcome);
          deferredPrompt = null;
        });
      }
      removeBanner();
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
      localStorage.setItem('pwa-install-dismissed', Date.now().toString());
      removeBanner();
    });

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

  // ── 4. Update Banner ────────────────────────────────────────────────────
  function showUpdateBanner() {
    if (document.getElementById('pwa-update-banner')) return;

    const banner = document.createElement('div');
    banner.id = 'pwa-update-banner';
    banner.innerHTML = `
      <div style="
        position:fixed; bottom:1rem; left:50%; transform:translateX(-50%);
        background:#0f2d5a; color:#fff; border-radius:12px;
        padding:.85rem 1.4rem; display:flex; align-items:center; gap:.9rem;
        box-shadow:0 6px 24px rgba(0,0,0,.25); z-index:9999;
        font-family:inherit; max-width:92vw;">
        <span style="font-size:.93rem;">A new version of BMIS is available.</span>
        <button onclick="window.location.reload()" style="
          background:#c9943a; color:#fff; border:none; border-radius:8px;
          padding:.4rem .9rem; font-weight:700; cursor:pointer; white-space:nowrap;">
          Update
        </button>
        <button onclick="this.closest('#pwa-update-banner').remove()" style="
          background:transparent; color:#ccc; border:none; cursor:pointer;
          font-size:1.1rem; line-height:1;">✕</button>
      </div>`;
    document.body.appendChild(banner);
  }

  // ── 5. Online / Offline Toast ───────────────────────────────────────────
  window.addEventListener('online',  () => showToast('You\'re back online.', 'success'));
  window.addEventListener('offline', () => showToast('You\'re offline. Some features may be limited.', 'warning'));

  function showToast(message, type) {
    const colors     = { success: '#059669', warning: '#ffc107', error: '#dc3545' };
    const textColors = { success: '#fff',    warning: '#000',    error: '#fff'    };

    const toast = document.createElement('div');
    toast.style.cssText = `
      position:fixed; bottom:1.2rem; right:1.2rem;
      background:${colors[type] || '#333'}; color:${textColors[type] || '#fff'};
      padding:.75rem 1.2rem; border-radius:10px; font-size:.88rem;
      box-shadow:0 4px 16px rgba(0,0,0,.2); z-index:10000;
      max-width:280px; line-height:1.4; font-family:inherit;`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
  }

  // ── 6. Network quality indicator ───────────────────────────────────────
  if ('connection' in navigator) {
    const conn = navigator.connection;
    conn.addEventListener('change', () => {
      if (conn.effectiveType === 'slow-2g' || conn.effectiveType === '2g') {
        showToast('Slow connection detected.', 'warning');
      }
    });
  }

})();