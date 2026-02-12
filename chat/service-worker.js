self.addEventListener('install', (e) => {
    // Skip waiting forces this new service worker to take over immediately
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    // Clear old caches
    e.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => caches.delete(key)));
        })
    );
});

self.addEventListener('fetch', (e) => {
    // Just fetch from network, don't cache (for now, to fix errors)
    e.respondWith(fetch(e.request));
});