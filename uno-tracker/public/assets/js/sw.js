// sw.js - Service Worker برای UNO Tracker PWA

const CACHE_NAME = 'uno-tracker-v1';
const STATIC_ASSETS = [
  '/',
  '/assets/css/mobile.css',
  '/assets/css/sweetalert2.min.css',
  '/assets/js/htmx.min.js',
  '/assets/js/alpine.min.js',
  '/assets/js/sweetalert2.min.js',
  '/assets/js/tailwind.js',
  '/assets/js/sse-client.js',
  '/assets/js/sound-manager.js',
  '/assets/images/logo.svg',
  '/assets/fonts/Vazir.woff2',
];

// نصب Service Worker و کش کردن فایل‌های استاتیک
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('📦 Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => self.skipWaiting())
  );
});

// فعال‌سازی و پاکسازی کش قدیمی
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
  return self.clients.claim();
});

// مدیریت درخواست‌ها (Network First with Cache Fallback)
self.addEventListener('fetch', (event) => {
  // فقط درخواست‌های GET را مدیریت کن
  if (event.request.method !== 'GET') return;

  // درخواست‌های مربوط به SSE را نادیده بگیر
  if (event.request.url.includes('/sse/')) return;

  // درخواست‌های API را نادیده بگیر
  if (event.request.url.includes('/api/')) return;

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // پاسخ معتبر را در کش ذخیره کن
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(() => {
        // اگر آفلاین هستیم، از کش پاسخ بده
        return caches.match(event.request)
          .then((cachedResponse) => {
            if (cachedResponse) {
              return cachedResponse;
            }
            // اگر در کش نبود، صفحه‌ی آفلاین را نشان بده
            return caches.match('/offline');
          });
      })
  );
});

// پیام‌های دریافتی از کلاینت (برای Push Notification)
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});