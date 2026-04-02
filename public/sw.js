const PSGC_CACHE   = 'ss-psgc-v2';
const SURVEY_CACHE = 'ss-survey';   // no version — stable, never deleted

// On install: pre-cache provinces
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(PSGC_CACHE)
      .then(c => c.add('/api/psgc/provinces'))
      .catch(() => {})
  );
  self.skipWaiting();
});

// On activate: delete old PSGC caches only (never delete survey cache)
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k => k.startsWith('ss-psgc-') && k !== PSGC_CACHE)
            .map(k => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  if (event.request.method !== 'GET') return;
  if (url.origin !== self.location.origin) return;
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/login')) return;

  // PSGC API — cache-first, update in background
  if (url.pathname.startsWith('/api/psgc/')) {
    event.respondWith(
      caches.open(PSGC_CACHE).then(cache =>
        cache.match(event.request).then(cached => {
          const fresh = fetch(event.request).then(res => {
            if (res.ok) cache.put(event.request, res.clone());
            return res;
          }).catch(() => null);
          return cached || fresh;
        })
      )
    );
    return;
  }

  // Survey pages — cache-first, update in background (stale-while-revalidate)
  // This ensures the page always loads offline after first online visit
  if (url.pathname.match(/^\/s\/[^/]+(\/done)?\/?(\?.*)?$/)) {
    event.respondWith(
      caches.open(SURVEY_CACHE).then(cache =>
        cache.match(event.request).then(cached => {
          const fresh = fetch(event.request).then(res => {
            if (res.ok) cache.put(event.request, res.clone());
            return res;
          }).catch(() => null);
          return cached || fresh;
        })
      )
    );
    return;
  }
});
