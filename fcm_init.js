// fcm_init.js
// Include this on every resident page (resident_homepage.php, resident_profile.php, etc.)
// It registers the service worker and saves the FCM token to the server.

const firebaseConfig = {
    apiKey:            "AIzaSyDsAtSrF718UU4oz-_c_s3Wu59DrUKVe4s",
    authDomain:        "barangay-management-syst-3512b.firebaseapp.com",
    projectId:         "barangay-management-syst-3512b",
    storageBucket:     "barangay-management-syst-3512b.firebasestorage.app",
    messagingSenderId: "560258747749",
    appId:             "1:560258747749:web:67b0cf36663c11e74fa58c",
    measurementId:     "G-KZYSHB51M6"
};
// ── Still needed: VAPID public key ───────────────────────────────────────
// Firebase Console → Project Settings → Cloud Messaging → Web Push certificates → Generate key pair
const VAPID_KEY = "BNH01V-MOZec4-7PMWbVdwcveqUk8uV9FiVXf-d0sfhRx2KXwyjl-zM3QDqbcBnRX3j9J5SDGZ_SetrjGnZKNqQ";
// ─────────────────────────────────────────────────────────────────────────

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging.js";

const app       = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

async function initPushNotifications() {
    if (!('serviceWorker' in navigator)) return;

    try {
        const reg = await navigator.serviceWorker.register(
            '/firebase-messaging-sw.js',
            { scope: '/' }
        );

        await reg.update();
        const activeReg = await navigator.serviceWorker.ready;
        console.log('SW active:', activeReg.scope);

        const permission = await Notification.requestPermission();
        console.log('Permission:', permission);
        if (permission !== 'granted') return;

        const token = await getToken(messaging, {
            vapidKey: VAPID_KEY,
            serviceWorkerRegistration: activeReg
        });

        console.log('FCM Token:', token);

        if (token) {
            const res = await fetch('save_fcm_token.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'token=' + encodeURIComponent(token)
            });
            const data = await res.json();
            console.log('Token save result:', data);
        }
    } catch (err) {
        console.error('FCM error:', err.code, err.message);
    }
}

// Friendly in-page toast for foreground notifications
function showInPageToast(title, body) {
    const existing = document.getElementById('fcm-toast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.id = 'fcm-toast';
    t.innerHTML = `
        <div style="
            position:fixed; bottom:24px; right:24px; z-index:99999;
            background:#fff; border-left:4px solid #1D9E75;
            border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,.15);
            padding:14px 18px; max-width:340px; min-width:260px;
            display:flex; align-items:flex-start; gap:12px;
            animation: fcmSlideIn .4s cubic-bezier(.22,1,.36,1) both;
            font-family: 'DM Sans', sans-serif;
        ">
            <div style="width:34px;height:34px;border-radius:50%;background:#E1F5EE;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" fill="#1D9E75"/>
                    <path d="M7.5 12.5l3 3 6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div style="font-weight:700;color:#085041;font-size:14px;margin-bottom:2px;">${title}</div>
                <div style="color:#0F6E56;font-size:13px;">${body}</div>
            </div>
            <button onclick="this.closest('#fcm-toast').remove()"
                style="background:none;border:none;cursor:pointer;color:#9aadcc;font-size:20px;line-height:1;padding:0;flex-shrink:0;">×</button>
        </div>
        <style>
            @keyframes fcmSlideIn { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
        </style>
    `;
    document.body.appendChild(t);
    setTimeout(() => { if (t.parentNode) t.remove(); }, 6000);
}

window.initPushNotifications = initPushNotifications;