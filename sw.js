const CACHE_VERSION   = 'bmis-v1';
const STATIC_CACHE    = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE   = `${CACHE_VERSION}-dynamic`;
const OFFLINE_PAGE    = '/offline.php';

const STATIC_ASSETS = [
    '/index.php',
    '/offline.php',
    '/css/sb-admin-2.min.css',
    '/js/sb-admin-2.min.js',
    '/icons/pwa/icon-192x192.png',
    '/icons/pwa/icon-512x512.png',
    '/icons/logo.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(STATIC_ASSETS.map(url => new Request(url, { cache: 'reload' }))))
            .then(() => self.skipWaiting())
            .catch(err => console.warn('[SW] Pre-cache failed:', err))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(k => k.startsWith('bmis-') && k !== STATIC_CACHE && k !== DYNAMIC_CACHE)
                    .map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;
    if (url.hostname.includes('firebase')) return;

    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    if (url.pathname.endsWith('.php') || url.pathname.endsWith('/')) {
        event.respondWith(networkFirstWithOfflineFallback(request));
        return;
    }

    event.respondWith(staleWhileRevalidate(request));
});

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('Asset unavailable offline.', { status: 503 });
    }
}

async function networkFirstWithOfflineFallback(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        const offlinePage = await caches.match(OFFLINE_PAGE);
        return offlinePage || new Response(
            '<h2>You are offline</h2><p>Please check your connection.</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function staleWhileRevalidate(request) {
    const cache  = await caches.open(DYNAMIC_CACHE);
    const cached = await cache.match(request);
    const fetchPromise = fetch(request).then(response => {
        if (response.ok) cache.put(request, response.clone());
        return response;
    }).catch(() => null);
    return cached || fetchPromise;
}

function isStaticAsset(url) {
    const ext = url.pathname.split('.').pop().toLowerCase();
    return ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf'].includes(ext);
}

self.addEventListener('push', event => {
    if (!event.data) return;
    let data = {};
    try { data = event.data.json(); } catch { data = { title: 'BMIS', body: event.data.text() }; }
    const title = data.notification?.title || 'Barangay San Pedro';
    const options = {
        body:  data.notification?.body || '',
        icon:  '/icons/pwa/icon-192x192.png',
        badge: '/icons/pwa/icon-72x72.png',
        tag:   'bmis-push',
        data:  data.data || {}
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/resident_homepage.php';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes(targetUrl) && 'focus' in client) return client.focus();
            }
            return clients.openWindow(targetUrl);
        })
    );
});