const CACHE_VER    = 'v2';
const PSGC_CACHE   = 'ss-psgc-'   + CACHE_VER;
const SURVEY_CACHE = 'ss-survey-' + CACHE_VER;

// On install: pre-cache provinces (small, always needed)
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(PSGC_CACHE)
      .then(c => c.add('/api/psgc/provinces'))
      .catch(() => {})   // ignore if offline at install time
  );
  self.skipWaiting();
});

// On activate: delete old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k => k !== PSGC_CACHE && k !== SURVEY_CACHE)
            .map(k => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // Skip non-GET, cross-origin, and admin/auth routes
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

  // Survey take pages /s/{token} and /s/{token}/done — network-first, fall back to cache
  if (url.pathname.match(/^\/s\/[^/]+(\/done)?\/?(\?.*)?$/)) {
    event.respondWith(
      fetch(event.request)
        .then(res => {
          if (res.ok) {
            caches.open(SURVEY_CACHE)
              .then(c => c.put(event.request, res.clone()));
          }
          return res;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }
});
