// firebase-messaging-sw.js
// Must be at the ROOT of your site (same level as index.php).
// Handles background push notifications when the user is NOT on the site.

importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey:            "AIzaSyDsAtSrF718UU4oz-_c_s3Wu59DrUKVe4s",
    authDomain:        "barangay-management-syst-3512b.firebaseapp.com",
    projectId:         "barangay-management-syst-3512b",
    storageBucket:     "barangay-management-syst-3512b.firebasestorage.app",
    messagingSenderId: "560258747749",
    appId:             "1:560258747749:web:67b0cf36663c11e74fa58c"
});

const messaging = firebase.messaging();

// Background message handler — shows the notification when app is in background/closed
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Background message:', payload);

    const title = payload.notification?.title || 'Barangay San Pedro';
    const body  = payload.notification?.body  || 'You have a new notification.';
    const url   = payload.data?.url || '/';

    self.registration.showNotification(title, {
        body:    body,
        icon:    '/icons/pwa/icon-192x192.png',
        badge:   '/icons/pwa/icon-96x96.png',
        tag:     'bmis-notification',
        data:    { url: url },
        vibrate: [200, 100, 200],
        actions: [
            { action: 'open',    title: 'Open' },
            { action: 'dismiss', title: 'Dismiss' }
        ]
    });
});

// When user taps the notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.action === 'dismiss') return;

    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
            for (const client of list) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            return clients.openWindow(url);
        })
    );
});
