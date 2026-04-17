const PSGC_CACHE   = 'ss-psgc-v2';
const SURVEY_CACHE = 'ss-survey-v2'; // v2: network-first to avoid stale CSRF tokens

// On install: pre-cache provinces + offline fallback page
self.addEventListener('install', event => {
  event.waitUntil(
    Promise.all([
      caches.open(PSGC_CACHE).then(c => c.add('/api/psgc/provinces')).catch(() => {}),
      caches.open(SURVEY_CACHE).then(c => c.add('/offline.html')).catch(() => {}),
    ])
  );
  self.skipWaiting();
});

// On activate: delete old PSGC and survey caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k =>
          (k.startsWith('ss-psgc-')   && k !== PSGC_CACHE) ||
          (k.startsWith('ss-survey')  && k !== SURVEY_CACHE)
        ).map(k => caches.delete(k))
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

  // Survey pages — network-first so CSRF tokens are always fresh.
  // Cache is only used as a fallback when the network is unreachable (offline).
  if (url.pathname.match(/^\/s\/[^/]+(\/done)?\/?(\?.*)?$/)) {
    event.respondWith(
      fetch(event.request).then(res => {
        // Update cache in the background so offline fallback stays fresh
        if (res.ok) {
          caches.open(SURVEY_CACHE).then(c => c.put(event.request, res.clone()));
        }
        return res;
      }).catch(() =>
        // Offline — serve cached version or generic offline page
        caches.open(SURVEY_CACHE).then(c =>
          c.match(event.request).then(cached => cached || caches.match('/offline.html'))
        )
      )
    );
    return;
  }
});
