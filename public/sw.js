// ── Mi Negocio — Service Worker ───────────────────────────────
const CACHE_NAME = 'mi-negocio-v1';

// Recursos que se cachean al instalar (shell de la app)
const PRECACHE = [
    '/login',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap',
];

// ── Instalación: cachear shell ─────────────────────────────────
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

// ── Activación: limpiar caches viejos ─────────────────────────
self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch: Network First con fallback a cache ──────────────────
// Para un POS los datos siempre deben ser frescos (network first).
// Solo si no hay red se sirve desde cache.
self.addEventListener('fetch', (e) => {
    // Solo interceptar GET del mismo origen o CDNs conocidas
    const url = new URL(e.request.url);
    const mismOrigen = url.origin === self.location.origin;
    const esCDN = url.hostname.includes('jsdelivr') || url.hostname.includes('googleapis') || url.hostname.includes('gstatic');

    if (e.request.method !== 'GET') return;
    if (!mismOrigen && !esCDN) return;

    e.respondWith(
        fetch(e.request)
            .then(response => {
                // Guardar copia fresca en cache (solo respuestas OK)
                if (response && response.status === 200 && (mismOrigen || esCDN)) {
                    const clon = response.clone();
                    caches.open(CACHE_NAME).then(c => c.put(e.request, clon));
                }
                return response;
            })
            .catch(() => {
                // Sin red: servir desde cache
                return caches.match(e.request).then(cached => {
                    if (cached) return cached;

                    // Fallback offline para páginas HTML
                    if (e.request.headers.get('accept')?.includes('text/html')) {
                        return caches.match('/login');
                    }
                });
            })
    );
});
