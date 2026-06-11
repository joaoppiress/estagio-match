/* EstágioMatch service worker — offline shell, network-first.
   Lives at the app root so its scope covers all routes.

   Network-first em vez de cache-first: online sempre busca a versão mais
   recente (corrige CSS/JS/tema desatualizado ao navegar); o cache só é usado
   como fallback offline. A versão do cache é trocada para limpar o lixo antigo. */
const CACHE = 'em-static-v2';
const OFFLINE_ASSETS = [
    'public/assets/css/app.css',
    'public/assets/js/app.js',
    'public/assets/js/theme-init.js',
    'public/assets/icons/icon.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(OFFLINE_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return; // VLibras/CDN vão direto à rede

    const isAsset = /\.(css|js|svg|png|webmanifest|woff2?)$/.test(url.pathname);

    // Network-first: tenta a rede; em sucesso atualiza o cache; offline cai no cache.
    event.respondWith(
        fetch(req)
            .then((res) => {
                if (isAsset && res && res.ok) {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
                }
                return res;
            })
            .catch(() => caches.match(req))
    );
});
