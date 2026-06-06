// firebase-messaging-sw.js
// Place this file in your PROJECT ROOT (same folder as index.php)
// This is the Service Worker that handles background push notifications.

importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey:            "AIzaSyDsAtSrF718UU4oz-_c_s3Wu59DrUKVe4s",
    authDomain:        "barangay-management-syst-3512b.firebaseapp.com",
    projectId:         "barangay-management-syst-3512b",
    storageBucket:     "barangay-management-syst-3512b.firebasestorage.app",
    messagingSenderId: "560258747749",
    appId:             "1:560258747749:web:67b0cf36663c11e74fa58c",
    measurementId:     "G-KZYSHB51M6"
});

const messaging = firebase.messaging();

// Handle background notifications (when the site is NOT open)
messaging.onBackgroundMessage(function(payload) {
    const { title, body, icon } = payload.notification;
    self.registration.showNotification(title, {
        body:  body  || '',
        icon:  icon  || '/icons/logo.png',
        badge: '/icons/logo.png',
        tag:   'bmis-notification',   // replaces previous instead of stacking
        data:  payload.data || {}
    });
});

// Clicking the notification opens the resident homepage
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url.includes('resident_homepage.php') && 'focus' in client)
                    return client.focus();
            }
            if (clients.openWindow) return clients.openWindow('resident_homepage.php');
        })
    );
});