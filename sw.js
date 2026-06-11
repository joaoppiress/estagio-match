/* EstágioMatch service worker — minimal offline shell.
   Lives at the app root so its scope covers all routes. */
const CACHE = 'em-static-v1';
const ASSETS = [
    'public/assets/css/app.css',
    'public/assets/js/app.js',
    'public/assets/js/theme-init.js',
    'public/assets/icons/icon.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return; // let VLibras/CDN hit network

    // Cache-first for static assets.
    if (/\.(css|js|svg|png|webmanifest|woff2?)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(req).then((cached) => cached || fetch(req).then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
                return res;
            }).catch(() => cached))
        );
        return;
    }

    // Network-first for dynamic PHP pages.
    event.respondWith(fetch(req).catch(() => caches.match(req)));
});
