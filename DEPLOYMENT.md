## 📘 راهنمای انتشار پروژه UNO Tracker روی هاست اشتراکی

این راهنما به شما کمک می‌کند تا پروژه را از حالت توسعه (با پوشه‌ی `public`) به یک هاست اشتراکی (با قرار دادن محتویات `public` در روت) منتقل کنید. تمام نکات، کدهای اصلاح‌شده و مسائل امنیتی در این فایل گردآوری شده است.

---

### ✅ تأیید تغییرات پیشنهادی

تغییراتی که شما در نظر گرفته‌اید **کاملاً صحیح** هستند و با بهترین روش‌های استقرار در هاست‌های اشتراکی تطابق دارند. اما چند نکته‌ی تکمیلی وجود دارد که برای عملکرد بهتر و امنیت بیشتر باید رعایت شوند.

---

## 🚀 مراحل انتقال

### ۱️⃣ تغییر ساختار فایل‌ها

۱. تمام محتویات پوشه‌ی `public/` را **به ریشه‌ی پروژه** (همان سطح `src/`، `config/`، `views/` و غیره) منتقل کنید.
۲. پوشه‌ی خالی `public/` را حذف کنید.

**نتیجه:** فایل `index.php` و `.htaccess` در کنار پوشه‌های `src/`، `config/`، `views/` و غیره قرار می‌گیرند.

---

### ۲️⃣ اصلاح فایل `index.php`

فایل `index.php` که اکنون در ریشه قرار دارد، باید ثابت `ROOT_PATH` را اصلاح کنید.

**کد اصلاح‌شده:**

```php
<?php
// ============================================
// UNO Tracker - Application Entry Point
// ============================================

// نمایش خطاها در حالت Development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// تنظیم Timezone
date_default_timezone_set('Asia/Tehran');

// 🆕 تعریف ثابت‌های مسیر (با __DIR__ چون index.php در روت است)
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH); // چون public حذف شده، روت همان public است

// Autoloader
require_once SRC_PATH . '/Core/Autoloader.php';
Core\Autoloader::register();

// 🆕 لود توابع کمکی Debug (dd, dump, d, sql, trace)
require_once SRC_PATH . '/Core/Helpers.php';

// ... باقی کدها بدون تغییر ...
```

**نکته:** دقت کنید که `PUBLIC_PATH` نیز باید به `__DIR__` تغییر کند، چون دیگر پوشه‌ی `public` وجود ندارد.

---

### ۳️⃣ اصلاح فایل `.htaccess`

فایل `.htaccess` جدید در ریشه‌ی پروژه باید به‌گونه‌ای باشد که:

- تمام درخواست‌ها را به `index.php` هدایت کند (مگر فایل‌های واقعی).
- از پوشه‌های حساس (src, config, views, database, storage) محافظت کند.
- هدایت HTTP به HTTPS را اعمال کند.
- هدرهای امنیتی را تنظیم کند.

**.htaccess کامل و نهایی:**

```apache
# ============================================
# UNO Tracker - Apache 2.4 Configuration
# ============================================

RewriteEngine On

# ============================================
# 🔒 Redirect to HTTPS (فعال برای پروداکشن)
# ============================================
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ============================================
# 🎯 Redirect all requests to index.php
# ============================================
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# ============================================
# 🛡️ Security Headers
# ============================================
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>

# ============================================
# 🚫 Block access to sensitive files
# ============================================
<FilesMatch "\.(env|log|sql|md|json|lock|ini|yml|yaml|xml|dist|example)$">
    Require all denied
</FilesMatch>

# ============================================
# 🚫 Block access to sensitive directories
# ============================================
RedirectMatch 403 ^/config/.*$
RedirectMatch 403 ^/src/.*$
RedirectMatch 403 ^/database/.*$
RedirectMatch 403 ^/views/.*$
RedirectMatch 403 ^/storage/.*$
RedirectMatch 403 ^/vendor/.*$
RedirectMatch 403 ^/tests/.*$
RedirectMatch 403 ^/\.git/.*$

# ============================================
# 🚫 Disable directory listing
# ============================================
Options -Indexes

# ============================================
# 🚫 Protect .htaccess itself
# ============================================
<Files ".htaccess">
    Require all denied
</Files>

# ============================================
# 📂 Allow access to assets folder only
# ============================================
<Directory "assets">
    Options -Indexes
    Require all granted
</Directory>

# ============================================
# ⏱️ Cache static assets (optional)
# ============================================
<FilesMatch "\.(css|js|jpg|jpeg|png|gif|webp|svg|ico|mp3|woff2|ttf)$">
    Header set Cache-Control "max-age=2592000, public, immutable"
</FilesMatch>
```

---

### ۴️⃣ تنظیم مسیرهای assets در قالب‌ها

تمام مسیرهای فایل‌های استاتیک (CSS, JS, images, sounds) در قالب‌ها با `/assets/...` شروع می‌شوند. با انتقال محتویات `public` به روت، این مسیرها همچنان به درستی کار می‌کنند چون فایل‌های `assets` در روت در دسترس هستند.

**مثال از `views/layouts/main.php`:**

```html
<link rel="stylesheet" href="/assets/css/sweetalert2.min.css" />
<script src="/assets/js/htmx.min.js"></script>
<img src="/assets/images/logo.svg" alt="UNO Tracker" />
```

**هیچ تغییری در این مسیرها لازم نیست.**

---

### ۵️⃣ تنظیم مجوزهای پوشه‌ها (Permission)

برای عملکرد صحیح سیستم، مجوزهای پوشه‌های زیر را تنظیم کنید:

```bash
chmod -R 755 storage/
chmod -R 755 uploads/        # اگر در روت وجود دارد
chmod -R 755 assets/uploads/ # اگر آپلودها در assets هستند
```

اگر هاست شما از `suPHP` یا `FastCGI` استفاده می‌کند، مجوزها را روی `755` و مالکیت فایل‌ها را روی `user:group` مناسب تنظیم کنید.

---

### ۶️⃣ تنظیم متغیرهای محیطی (Environment Variables)

اگر از `.env` استفاده می‌کنید (پروژه از `config/database.php` با `getenv()` استفاده می‌کند)، حتماً فایل `.env` را در روت قرار دهید و آن را از دسترس خارج کنید (با `.htaccess` بلاک شده است).

**ساختار `.env` پیشنهادی:**

```env
APP_ENV=production
APP_DEBUG=false

DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database
DB_USER=your_username
DB_PASS=your_password

SMS_USERNAME=your_melipayamak_username
SMS_PASSWORD=your_melipayamak_password
```

---

### ۷️⃣ غیرفعال کردن نمایش خطاها در پروداکشن

در `index.php`، این دو خط را برای محیط پروداکشن غیرفعال کنید:

```php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
```

و به‌جای آن:

```php
error_reporting(0);
ini_set('display_errors', '0');
```

---

### ۸️⃣ تنظیم Session در هاست اشتراکی

پروژه از `storage/sessions` برای ذخیره‌سازی Session استفاده می‌کند. اگر هاست شما اجازه‌ی نوشتن در این پوشه را نمی‌دهد، مسیر Session را به یک پوشه‌ی معتبر دیگر تغییر دهید (مثلاً `/tmp`).

در `index.php`، خط زیر را اصلاح کنید:

```php
// تنظیم مسیر ذخیره session به پوشه‌ای که قابل نوشتن باشد
$sessionPath = ROOT_PATH . '/storage/sessions';
```

اگر این پوشه وجود ندارد، ایجاد کنید:

```bash
mkdir -p storage/sessions
chmod 755 storage/sessions
```

---

### ۹️⃣ نکات امنیتی تکمیلی

| مورد                              | توضیح                                             |
| --------------------------------- | ------------------------------------------------- |
| **غیرفعال کردن `display_errors`** | از افشای اطلاعات حساس جلوگیری می‌کند.             |
| **فعال کردن HTTPS**               | با `.htaccess` به‌صورت اجباری هدایت کنید.         |
| **بلاک کردن `src/` و `config/`**  | از دسترسی مستقیم به فایل‌های PHP جلوگیری می‌کند.  |
| **استفاده از `getenv()`**         | اطلاعات حساس را در `.env` نگه دارید.              |
| **به‌روزرسانی منظم**              | آخرین نسخه‌ی پروژه را همیشه روی هاست داشته باشید. |

---

## 🧪 عیب‌یابی پس از انتقال

| مشکل                                 | راه‌حل                                                                                |
| ------------------------------------ | ------------------------------------------------------------------------------------- |
| **خطای ۵۰۰ (Internal Server Error)** | بررسی `.htaccess` و مطمئن شدن از صحت syntax. چک کردن لاگ‌های سرور.                    |
| **صفحات سفید (White Screen)**        | فعال کردن موقت `display_errors` برای دیدن خطاها.                                      |
| **مسیرهای assets 404**               | بررسی کنید که فایل‌های `assets` در روت هستند و مسیرها با `/assets` شروع می‌شوند.      |
| **اتصال به دیتابیس**                 | تنظیمات `config/database.php` را با اطلاعات هاست به‌روز کنید.                         |
| **Session کار نمی‌کند**              | مجوز پوشه‌ی `storage/sessions` را به `755` تغییر دهید یا از مسیر `/tmp` استفاده کنید. |
| **RewriteRule کار نمی‌کند**          | اطمینان از فعال بودن `mod_rewrite` در هاست.                                           |

---

## 📂 ساختار نهایی پروژه در هاست

```
your-domain.com/
├── .htaccess
├── index.php
├── assets/                      # (CSS, JS, images, sounds)
│   ├── css/
│   ├── js/
│   ├── images/
│   └── sounds/
├── src/
│   ├── Core/
│   ├── Application/
│   ├── Domain/
│   ├── Infrastructure/
│   └── Presentation/
├── views/
│   ├── layouts/
│   └── pages/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── events.php
│   └── melipayamak.php
├── database/
│   └── uno_tracker.sql
├── storage/
│   ├── sessions/
│   ├── logs/
│   └── uploads/
├── .env                         # (اختیاری)
└── .htaccess
```

---

## ✅ جمع‌بندی نهایی

تغییراتی که شما در نظر گرفته‌اید (**تغییر `ROOT_PATH` به `__DIR__`** و **انتقال `.htaccess` به روت**) کاملاً درست و استاندارد است. با اعمال `.htaccess` پیشنهادی و رعایت نکات امنیتی، پروژه با خیال راحت روی هاست اشتراکی اجرا می‌شود.

**مهم‌ترین نکات قابل ذکر در فایل `.htaccess` جدید:**

1. **هدایت HTTPS** (برای امنیت)
2. **بلاک کردن پوشه‌های حساس** (`src`, `config`, `views`, `database`, `storage`)
3. **محافظت از فایل‌های `.env` و `.htaccess`**
4. **غیرفعال کردن `Indexes`** برای جلوگیری از نمایش لیست فایل‌ها

در صورت بروز هرگونه خطا، با فعال کردن موقت `display_errors` و بررسی لاگ‌های سرور، به‌سرعت مشکل را پیدا کنید.

---

> **تذکر نهایی:** اگر هاست شما از `nginx` به جای Apache استفاده می‌کند، باید تنظیمات مشابه را در فایل `nginx.conf` پیاده‌سازی کنید. اما در هاست‌های اشتراکی معمولاً Apache در دسترس است.

---

## ❓ سوال مهم

```
بعد از تغییرات فوق ایا میتوانم کل پروژه را درون یک ساب فولدر به صورت زیر قرار بدم
https://hamidionline.ir/uno/
ایا باید تغییری در ساختار و کدها ایجاد کنم

```

## 🎯 پاسخ به سوال شما

**بله، کاملاً امکان‌پذیر است** که پروژه را در یک ساب‌فولدر مانند `https://hamidionline.ir/uno/` قرار دهید. اما برای اینکه همه چیز به‌درستی کار کند، **باید تغییرات مشخصی** در کدها و تنظیمات اعمال کنید. در غیر این صورت، لینک‌ها و مسیرهای assets به‌خاطر وجود ساب‌فولدر `uno/` خراب می‌شوند.

دلیل اصلی مشکل این است که در کدهای فعلی، همه‌ی مسیرها با **اسلش ابتدایی (`/`)** شروع می‌شوند (مثل `/dashboard`، `/assets/css/style.css`). وقتی پروژه در روت هاست است، این مسیرها به درستی کار می‌کنند. اما وقتی در ساب‌فولدر `uno/` قرار می‌گیرد، مرورگر به‌اشتباه به دنبال `https://hamidionline.ir/dashboard` می‌گردد، در حالی که باید به `https://hamidionline.ir/uno/dashboard` برود.

---

## ✅ راه‌حل کامل و گام‌به‌گام برای قرار گرفتن در ساب‌فولدر

### ۱️⃣ تنظیم ثابت `BASE_PATH` در فایل `index.php`

ابتدا در فایل `index.php` (که الان در ریشه‌ی پروژه قرار دارد)، یک ثابت جدید برای مسیر پایه (ساب‌فولدر) تعریف می‌کنیم. این ثابت در سراسر برنامه برای ساخت لینک‌ها و مسیرها استفاده می‌شود.

**فایل:** `index.php` (در ریشه)

```php
<?php
// ============================================
// UNO Tracker - Application Entry Point
// ============================================

// نمایش خطاها در حالت Development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// تنظیم Timezone
date_default_timezone_set('Asia/Tehran');

// تعریف ثابت‌های مسیر فیزیکی (سیستم فایل)
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH);

// 🆕 تعریف مسیر پایه برای URLها (برای ساب‌فولدر)
// اگر پروژه در روت است، مقدار را خالی بگذارید: ''
// اگر در ساب‌فولدر است، مثلاً '/uno'
define('BASE_PATH', '/uno');

// Autoloader
require_once SRC_PATH . '/Core/Autoloader.php';
Core\Autoloader::register();

// ... ادامه کدهای قبلی ...
```

---

### ۲️⃣ اصلاح تابع `base_url()` در `Helpers.php`

تابع `base_url()` که در سراسر برنامه برای ساخت لینک‌ها استفاده می‌شود، باید از ثابت `BASE_PATH` استفاده کند.

**فایل:** `src/Core/Helpers.php`

```php
<?php

/**
 * ساخت URL کامل با در نظر گرفتن ساب‌فولدر
 * مثال: base_url('dashboard') → '/uno/dashboard'
 */
function base_url($path = '')
{
    $base = defined('BASE_PATH') ? BASE_PATH : '';
    // حذف اسلش‌های اضافی
    $path = ltrim($path, '/');
    if ($base === '' || $base === '/') {
        return '/' . $path;
    }
    return rtrim($base, '/') . '/' . $path;
}
```

---

### ۳️⃣ اصلاح متد `uri()` در `Request.php` (برای مسیریابی)

وقتی کاربر به آدرس `/uno/dashboard` مراجعه می‌کند، سرور این مسیر کامل را به PHP می‌دهد. اما مسیریاب (Router) فقط باید بخش `dashboard` را ببیند. بنابراین باید در کلاس `Request`، مسیر ساب‌فولدر را از ابتدای URI حذف کنیم.

**فایل:** `src/Core/Request.php`

```php
public function uri(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // 🆕 حذف مسیر پایه (ساب‌فولدر) از ابتدای URI
    if (defined('BASE_PATH') && BASE_PATH !== '' && BASE_PATH !== '/') {
        $pattern = '#^' . preg_quote(BASE_PATH, '#') . '#';
        $uri = preg_replace($pattern, '', $uri);
    }

    // اگر خالی شد، ریشه را برگردان
    return rtrim($uri, '/') ?: '/';
}
```

---

### ۴️⃣ اصلاح فایل `.htaccess` (افزودن `RewriteBase`)

فایل `.htaccess` باید بداند که پروژه در چه مسیری قرار دارد تا RewriteRuleها به درستی کار کنند. دستور `RewriteBase` این کار را انجام می‌دهد.

**فایل:** `.htaccess` (در ریشه)

```apache
# ============================================
# UNO Tracker - Apache 2.4 Configuration
# ============================================

RewriteEngine On

# 🆕 تعیین مسیر پایه برای Rewrite (ساب‌فولدر)
RewriteBase /uno/

# ============================================
# 🔒 Redirect to HTTPS (فعال برای پروداکشن)
# ============================================
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ============================================
# 🎯 Redirect all requests to index.php
# ============================================
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# ... بقیه قوانین امنیتی (بدون تغییر) ...
```

---

### ۵️⃣ اصلاح مسیرهای Assets در فایل‌های Layout

در تمام فایل‌های Layout (مانند `views/layouts/main.php`، `views/layouts/tv.php` و ...) که فایل‌های استاتیک (CSS, JS, Images) را لود می‌کنند، باید به‌جای اسلش ابتدایی (`/`)، از تابع `base_url()` استفاده کنید.

**نمونه تغییر در `views/layouts/main.php`:**

**کد قبلی (اشتباه در ساب‌فولدر):**

```html
<link rel="stylesheet" href="/assets/css/sweetalert2.min.css" />
<script src="/assets/js/htmx.min.js"></script>
<img src="/assets/images/logo.svg" alt="UNO Tracker" />
```

**کد اصلاح‌شده (صحیح):**

```html
<link
  rel="stylesheet"
  href="<?= base_url('assets/css/sweetalert2.min.css') ?>"
/>
<script src="<?= base_url('assets/js/htmx.min.js') ?>"></script>
<img src="<?= base_url('assets/images/logo.svg') ?>" alt="UNO Tracker" />
```

> **نکته:** اگر می‌خواهید این کار را یکباره و بدون تغییر تک‌تک فایل‌ها انجام دهید، می‌توانید در `index.php` یک متغیر سراسری تعریف کنید و آن را در Viewها تزریق کنید. اما روش فوق استانداردترین و امن‌ترین روش است.

---

### ۶️⃣ اصلاح مسیرهای AJAX و HTMX در فایل‌های JavaScript

در فایل‌های JavaScript که از `window.BASE_URL` استفاده می‌کنند (مثل `game-sse.js`، `tv-sse.js` و ...)، مطمئن شوید که این متغیر به‌درستی مقداردهی شده است.

در `views/pages/game/partials/game-content.php` و `views/pages/tv/show.php`، مقدار `window.BASE_URL` را با استفاده از `base_url()` تنظیم کنید:

```javascript
// قبلاً: window.BASE_URL = '<?= base_url() ?>';
window.BASE_URL = "<?= base_url() ?>";
```

همچنین در فایل‌های جاوااسکریپت که مستقیماً از `/sse/...` یا `/game/...` استفاده می‌کنند، باید از `window.BASE_URL` استفاده کنند:

```javascript
// در game-sse.js و tv-sse.js
const sseUrl = (window.BASE_URL || "") + "/sse/game/" + SSE_CONFIG.gameId;
```

---

## 🧪 عیب‌یابی نهایی بعد از انتقال

پس از اعمال تمام تغییرات بالا، پروژه را در هاست آپلود کنید و موارد زیر را بررسی کنید:

| آیتم                   | نحوه تست                                                                                                                    |
| ---------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| **صفحه اصلی**          | به `https://hamidionline.ir/uno/` بروید و ببینید آیا به داشبورد یا لاگین هدایت می‌شوید.                                     |
| **مسیرهای assets**     | با ابزار Developer Console (F12) تب Network را باز کنید و ببینید فایل‌های CSS/JS با status 200 لود می‌شوند یا 404.          |
| **لینک‌های داخلی**     | روی منوها کلیک کنید و ببینید به آدرس‌های `/uno/dashboard` و `/uno/games` می‌روند یا نه.                                     |
| **SSE و WebSocket**    | صفحه بازی را باز کنید و ببینید اتصال SSE با آدرس `/uno/sse/game/...` برقرار می‌شود.                                         |
| **لاگین و احراز هویت** | فرم لاگین را پر کنید و ببینید بعد از ارسال، به درستی به `/uno/dashboard` هدایت می‌شوید.                                     |
| **آپلود فایل**         | اگر آپلود آواتار دارید، مسیر آپلود را بررسی کنید (معمولاً نیاز به تغییر ندارد چون مسیر فیزیکی با `ROOT_PATH` مشخص شده است). |

---

## 📌 خلاصه تغییرات لازم

| فایل                   | تغییر                                                                |
| ---------------------- | -------------------------------------------------------------------- |
| `index.php`            | افزودن `define('BASE_PATH', '/uno');`                                |
| `src/Core/Helpers.php` | اصلاح تابع `base_url()` برای استفاده از `BASE_PATH`                  |
| `src/Core/Request.php` | اصلاح متد `uri()` برای حذف `BASE_PATH` از ابتدای آدرس                |
| `.htaccess`            | افزودن `RewriteBase /uno/`                                           |
| `views/layouts/*.php`  | تغییر مسیرهای Assets از `/assets` به `<?= base_url('assets/...') ?>` |
| `views/pages/**/*.php` | اطمینان از مقداردهی `window.BASE_URL` با تابع `base_url()`           |

---

با این تغییرات، پروژه شما بدون هیچ مشکل و با حفظ تمام قابلیت‌ها (SSE، Routing، Assets، AJAX) در ساب‌فولدر `uno/` به‌خوبی کار خواهد کرد. در صورت بروز خطا، لاگ‌های سرور را بررسی کنید و مطمئن شوید که `mod_rewrite` در هاست فعال است.
