var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/dashboard.css',
    '/css/app.css',
    '/js/app.js',
    '/assets/js/core.js',
    '/assets/js/dashboard.js',
    '/assets/js/require.min.js',
    '/assets/js/vendors/axios.min.js',
    '/assets/js/vendors/chart.bundle.min.js',
    '/assets/js/vendors/circle-progress.min.js',
    '/assets/js/vendors/jquery-3.2.1.min.js',
    '/assets/js/vendors/bootstrap.bundle.min.js',
    '/assets/js/vendors/datepicker.js',
    '/assets/js/vendors/daterangepicker.min.js',
    '/assets/js/vendors/moment.min.js',
    '/assets/js/vendors/select2.min.js',
    '/assets/js/vendors/selectize.min.js',
    '/assets/js/vendors/sweetalert.min.js',
    '/assets/plugins/charts-c3/plugin.js',
    '/assets/plugins/charts-c3/plugin.css',
    '/assets/plugins/datepicker/datepicker.css',
    '/assets/plugins/select2/select2.min.css',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});