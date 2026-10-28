# Worker Portal — labour.importer168.com

เว็บแอปสำหรับแรงงานต่างด้าวดูข้อมูลและไฟล์เอกสารของตนเองผ่านมือถือ (The Importer 168)

Laravel 13 · PHP 8.3 · ไม่มีฐานข้อมูลของตัวเอง — ข้อมูลทั้งหมดดึงจากระบบ admin (`admin.importer168.com`) ผ่าน API

## การเข้าสู่ระบบของแรงงาน

| ช่อง | ค่า |
|---|---|
| User | เลขที่หนังสือเดินทาง (`labour_passport_number`) ไม่สนตัวพิมพ์เล็ก/ใหญ่ |
| Password | วันเกิดรูปแบบ `ddmmyyyy` เช่น เกิด 6 ธ.ค. 1997 → `06121997` |

เข้าได้เฉพาะแรงงานที่ `labour_status = enable` ในระบบ admin (แรงงานที่ไม่มีวันเกิดในระบบจะเข้าไม่ได้)
ใส่ผิดเกิน 8 ครั้ง/นาที ต่อ passport จะถูกล็อกชั่วคราว (และฝั่ง admin จำกัด 10 ครั้ง/นาที อีกชั้น)

## หน้าจอ (ตาม UI.pdf)

1. **Login** — passport + วันเกิด
2. **หน้าแรก** — ชื่อ / นายจ้าง / สถานะ, การ์ด Passport, Work Permit, Visa, 90 วัน (วันหมดอายุ + วันคงเหลือ), ที่อยู่แจ้งที่พัก (ที่อยู่บริษัทนายจ้าง), ปุ่มดาวน์โหลดเอกสาร, กระดิ่งแจ้งเตือนเอกสารใกล้หมดอายุ (ใช้เกณฑ์วันจาก "ตั้งค่าวันแจ้งเตือน" ของ admin)
3. **เอกสาร** — รายการไฟล์ทั้งหมดของแรงงาน กด "เปิด" แล้วระบบจะขอลิงก์ชั่วคราว (15 นาที) จาก admin แล้วเปิดไฟล์ (ไฟล์บน R2 ไปที่ presigned URL, ไฟล์บนเครื่อง admin ส่งผ่านแอป)
4. **ข้อมูลของฉัน** — ข้อมูลส่วนตัว การจ้างงาน วีซ่า และปุ่มออกจากระบบ

รองรับติดตั้งเป็นแอปบนหน้าจอมือถือ (PWA manifest + ไอคอนใน `public/icons/`)

## การติดตั้ง / Deploy

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Document root ชี้ที่ `public/`. ไม่ต้องรัน migrate (ไม่ใช้ฐานข้อมูล: session และ cache เป็นไฟล์ใน `storage/`)

ค่าใน `.env` ที่ต้องตั้ง

```dotenv
APP_URL=https://labour.importer168.com
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=file
CACHE_STORE=file
SESSION_SECURE_COOKIE=true

ADMIN_API_URL=https://admin.importer168.com
ADMIN_API_KEY=<ค่าเดียวกับ PORTAL_API_KEY ใน .env ของ admin>
ADMIN_API_TIMEOUT=20
```

ฝั่ง admin ต้องตั้ง `PORTAL_API_KEY` ใน `.env` (ค่าเดียวกัน) แล้ว `php artisan config:clear`
สร้างคีย์ใหม่ได้ด้วย `php -r "echo bin2hex(random_bytes(24));"`

## API ที่ใช้ (ฝั่ง admin)

ทุก request ส่ง header `X-Portal-Key: <key>`

| Method | Path | ใช้ทำอะไร |
|---|---|---|
| POST | `/api/portal/login` `{passport, password}` | ตรวจ passport + วันเกิด → `{labour_id, profile}` (422 `invalid_credentials` / `account_disabled`) |
| GET | `/api/portal/labours/{id}` | โปรไฟล์ล่าสุด (403 ถ้าถูกปิดใช้งาน) |
| GET | `/api/portal/labours/{id}/documents/{key}/link` | ลิงก์เปิดไฟล์แบบ signed อายุ 15 นาที |
| GET | `/portal-file/{id}/{key}?expires&signature` | ปลายทางของลิงก์ด้านบน (ไม่ต้องล็อกอิน ลายเซ็นคือสิทธิ์) |

## โครงสร้างโค้ด

- `app/Services/AdminApi.php` — client เรียก API ของ admin (retry เฉพาะ network/5xx)
- `app/Services/WorkerSession.php` — เก็บแรงงานที่ล็อกอินใน session และ cache โปรไฟล์ 2 นาที (`?refresh=1` เพื่อดึงใหม่)
- `app/Http/Controllers/AuthController.php`, `PortalController.php`
- `app/Http/Middleware/EnsureWorkerLoggedIn.php` (`worker`), `RedirectIfWorkerLoggedIn.php` (`worker.guest`)
- `resources/views/` — `auth/login`, `home`, `documents`, `profile`, `layouts/app`, `components/icon` (SVG inline), `components/remaining`
- `public/css/portal.css`, `public/js/portal.js` — ธีมมือถือ (ไม่ต้อง build ด้วย Vite)

## ทดสอบ

```bash
php artisan test        # tests/Feature/PortalTest.php (จำลอง API ของ admin ด้วย Http::fake)
```

ทดสอบจริงกับเครื่อง dev: รัน admin ที่พอร์ตหนึ่ง แล้วรัน portal ด้วย
`ADMIN_API_URL=http://127.0.0.1:8765 php artisan serve --port=8766`

## ภาษา (English หลัก + မြန်မာ)

- ค่าเริ่มต้นเป็นอังกฤษ เปลี่ยนได้จากปุ่มภาษาในหน้า login / แถบบน / หน้า Profile (จำไว้ในคุกกี้ 1 ปี, `?lang=my` / `?lang=en`)
- ข้อความทั้งหมดอยู่ใน `lang/en.json` (ต้นฉบับ) และ `lang/my.json` เพิ่มภาษาได้ที่ `config/app.php` → `locales`
- แปลอัตโนมัติด้วย Google Cloud Translation API: ตั้ง `GOOGLE_TRANSLATE_API_KEY` ใน `.env` แล้วรัน
  ```bash
  php artisan lang:translate            # แปลเฉพาะข้อความที่ยังไม่มีคำแปล
  php artisan lang:translate --force    # แปลใหม่ทั้งหมด (ทับคำที่แก้มือไว้ด้วย)
  ```
  คำสั้นๆ บางคำ Google แปลผิดบริบท (เช่น "No." = เลขที่, "Active") จึงแก้มือไว้ใน `lang/my.json` แล้ว ถ้ารัน `--force` ต้องแก้ซ้ำ
- ขนาดตัวอักษรทั้งแอปกำหนดจาก type scale ท้ายไฟล์ `public/css/portal.css` (`--fs-*`)
