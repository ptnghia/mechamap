// Basic service worker for translations group & manifest caching (stale-while-revalidate)
const CACHE = 'translations-v1';
const MANIFEST_PATTERN = /\/api\/translations\/manifest/;
const GROUP_PATTERN = /\/api\/translations\/group/;

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k!==CACHE).map(k => caches.delete(k)) )));
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const url = event.request.url;
  if (MANIFEST_PATTERN.test(url) || GROUP_PATTERN.test(url)) {
    event.respondWith(staleWhileRevalidate(event.request));
  }
});

async function staleWhileRevalidate(request) {
  const cache = await caches.open(CACHE);
  const cached = await cache.match(request);
  const networkPromise = fetch(request).then(resp => {
    if (resp.ok) cache.put(request, resp.clone());
    return resp;
  }).catch(()=> cached);
  return cached || networkPromise;
}
