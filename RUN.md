# Quick Start - Chạy với MySQL (XAMPP)

> **Yêu cầu:** XAMPP đã cài sẵn MySQL trên cổng 3306. PHP 8.2+ đã cài.

---

## 🎯 3 bước để chạy được

### Bước 1: Kiểm tra XAMPP MySQL đang chạy

Mở XAMPP Control Panel và đảm bảo **MySQL** đang running (Start nếu chưa).

### Bước 2: Chạy setup

```powershell
cd "c:\Users\nguye\OneDrive\Desktop\New folder (2)\ai-lecture-notes"
powershell -ExecutionPolicy Bypass -File setup.ps1
```

Script này tự động:
1. Cài Composer dependencies (~50MB)
2. Build Tailwind CSS (nếu có npm)
3. Tạo `.env` (đã cấu hình MySQL)
4. Generate APP_KEY
5. **Tạo database `ai_lecture_notes` trong MySQL**
6. Chạy migration + seed (tạo 18 bảng + 2 user demo)
7. Storage symlink

### Bước 3: Khởi động server

```powershell
powershell -ExecutionPolicy Bypass -File start.ps1
```

→ Browser tự mở **http://localhost:8000**

---

## 🔑 Đăng nhập

| Email | Password | Vai trò |
|---|---|---|
| `demo@aurora.app` | `password` | Teacher |
| `student@aurora.app` | `password` | Student |

Hoặc tự đăng ký tại `/register`.

---

## 🤖 Upload audio cần Gemini API key

Đăng ký miễn phí: https://aistudio.google.com/app/apikey

Sau khi có key, mở `.env` và dán:
```
GEMINI_API_KEY=AIzaSy...your_key_here
```

Sau đó restart server. Bây giờ upload file audio sẽ hoạt động.

> Nếu chưa có key, bạn vẫn có thể xem Dashboard, đăng ký, xem UI — chỉ upload thì sẽ báo lỗi.

---

## 🔄 Reset từ đầu

```powershell
# Xóa vendor, node_modules, build
Remove-Item -Recurse -Force vendor, node_modules, public\build -ErrorAction SilentlyContinue

# Reset database MySQL (xóa và tạo lại)
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "DROP DATABASE IF EXISTS ai_lecture_notes; CREATE DATABASE ai_lecture_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Chay lai setup
powershell -ExecutionPolicy Bypass -File setup.ps1
```

---

## ❓ Xử lý lỗi

| Lỗi | Cách sửa |
|---|---|
| `php is not recognized` | Mở lại PowerShell mới (PATH chưa refresh) |
| `composer is not recognized` | Cài Composer: https://getcomposer.org/Composer-Setup.exe |
| `MySQL: connection refused` | Kiểm tra XAMPP Control Panel - MySQL đang chạy? |
| `Unknown database ai_lecture_notes` | Database chưa được tạo. Chạy lại setup.ps1 |
| `vendor/autoload.php: not found` | Chạy lại `setup.ps1` |
| `Permission denied storage` | Chạy với quyền Admin HOẶC tự tạo folder `public\storage` |
| Upload báo "GEMINI_API_KEY chưa cấu hình" | Lấy key tại https://aistudio.google.com/app/apikey |

---

## 📋 Files quan trọng

```
ai-lecture-notes/
├── ⭐ RUN.md          ← Bạn đang đọc file này
├── ⭐ setup.ps1       ← Chạy cái này đầu tiên
├── ⭐ start.ps1       ← Sau khi setup xong, chạy cái này để start server
├── ⭐ install-php.ps1 ← Cài PHP nếu chưa có (cần quyền Admin)
├── .env               ← Config (thêm GEMINI_API_KEY ở đây)
├── composer.json      ← PHP dependencies
├── package.json       ← Node dependencies (optional)
└── README.md          ← Tài liệu đầy đủ
```

---

## 🚀 Phím tắt

| Lệnh | Mô tả |
|---|---|
| `php artisan serve` | Chạy web server |
| `php artisan migrate:fresh --seed` | Reset DB + seed |
| `php artisan tinker` | REPL |
| `php artisan route:list` | Xem tất cả routes |
| `php artisan storage:link` | Tạo symlink storage |
| `npm run dev` | Build assets watch mode |
| `npm run build` | Build production |
