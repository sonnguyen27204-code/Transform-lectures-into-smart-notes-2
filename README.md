# Aurora — AI Lecture Notes

> Hệ thống quản lý ghi âm bài giảng thông minh với AI — chuyển giọng nói tiếng Việt thành văn bản, tóm tắt nội dung và tạo bộ câu hỏi ôn tập tự động.

![Banner](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/TailwindCSS-3.4-06B6D4?logo=tailwindcss&logoColor=white)
![Gemini](https://img.shields.io/badge/Gemini-1.5%20Flash-4285F4?logo=google&logoColor=white)

---

## ✨ Tính năng chính

- 🎙️ **Upload hoặc ghi âm trực tiếp** từ micro (MP3, WAV, M4A, OGG, WebM)
- 🤖 **AI Speech-to-Text** cho tiếng Việt (qua Google Gemini — multi-model fallback + Groq Whisper backup)
- 🧠 **AI sinh nội dung** (Groq Llama 3.3 70B — miễn phí, nhanh, hỗ trợ tiếng Việt tốt)
- 📝 **Transcript có timestamp** + phân biệt giảng viên / học viên (speaker diarization)
- 💡 **Tự động tóm tắt** + Key Takeaways + phân tích topics & sentiment
- ❓ **Quiz ôn tập trắc nghiệm** với đáp án đúng và giải thích
- 🃏 **Flashcards lật được** cho các thuật ngữ quan trọng
- 📊 **Dashboard thống kê** với stats cards và bài giảng gần đây
- ⚡ **Realtime status update** (auto-refresh khi AI xử lý xong)
- 🎨 **Giao diện dark mode hiện đại** với glassmorphism + Tailwind CSS
- 🔐 **Authentication** với phân quyền Student / Teacher

---

## 🛠 Công nghệ sử dụng

| Layer | Tech |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Database | MySQL 8.0 |
| Frontend | Blade Template + Tailwind CSS 3 |
| Build | Vite / Laravel Mix |
| AI | Google Gemini (chính, multi-model fallback) + Groq Whisper + Groq Llama 3.3 (backup) |
| Auth | Laravel built-in session-based |

**Mô hình:** MVC chuẩn Laravel + Service Layer cho AI logic.

---

## 📋 Yêu cầu môi trường

- PHP **>= 8.2** với các extension: `pdo_sqlite`, `pdo_mysql` (optional), `mbstring`, `openssl`, `gd`, `fileinfo`, `curl`, `zip`
- Composer **>= 2.0**
- Node.js **>= 18** + npm (optional, để build Tailwind — bỏ qua nếu không cần)
- Tài khoản Google AI Studio (lấy API key **miễn phí**) — https://aistudio.google.com/app/apikey
- Tài khoản Groq Cloud (lấy API key **miễn phí**, dùng làm backup) — https://console.groq.com/keys

> **Mặc định dùng SQLite** để không phải cài MySQL. Nếu muốn dùng MySQL, xem hướng dẫn ở cuối README.

---

## 🚀 Hướng dẫn cài đặt

### 1. Clone repo

```bash
git clone https://github.com/yourname/ai-lecture-notes.git
cd ai-lecture-notes
```

### 2. Cài đặt dependencies

```bash
composer install
npm install
```

### 3. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Sau đó mở file `.env` và chỉnh:

```env
APP_NAME="Aurora AI Lecture Notes"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_lecture_notes
DB_USERNAME=root
DB_PASSWORD=

# ⭐ Bắt buộc - Lấy key miễn phí tại https://aistudio.google.com/app/apikey
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

### 4. Tạo database (SQLite - tự động)

Không cần làm gì — file `database/database.sqlite` được tạo tự động khi chạy `php artisan migrate`.

Nếu muốn dùng MySQL thật, sửa `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_lecture_notes
DB_USERNAME=root
DB_PASSWORD=
```
rồi tạo database trong MySQL: `CREATE DATABASE ai_lecture_notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

### 5. Chạy migration + seed

```bash
php artisan migrate --seed
```

Lệnh seed tạo 2 tài khoản demo:
- **demo@aurora.app** / `password` (teacher)
- **student@aurora.app** / `password` (student)

### 6. Build assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Tạo symlink cho storage

```bash
php artisan storage:link
```

### 8. Khởi động server

```bash
php artisan serve
```

Truy cập: **http://localhost:8000**

---

## 📁 Cấu trúc thư mục

```
ai-lecture-notes/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/{Login,Register}Controller.php
│   │   │   ├── DashboardController.php
│   │   │   ├── LectureController.php
│   │   │   ├── QuizController.php
│   │   │   └── HomeController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Lecture.php
│   │   ├── Transcript.php
│   │   ├── TranscriptSegment.php
│   │   ├── Summary.php
│   │   ├── Quiz.php
│   │   ├── QuizOption.php
│   │   ├── Flashcard.php
│   │   └── ProcessingJob.php
│   ├── Services/
│   │   ├── GeminiService.php          ← Gọi Gemini API
│   │   └── AudioProcessingService.php ← Pipeline xử lý audio
│   └── Providers/
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── gemini.php                    ← Config AI
│   └── ...
├── database/
│   ├── migrations/                    ← 9 migrations
│   ├── factories/
│   └── seeders/
├── public/
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/{app,guest}.blade.php
│       ├── auth/{login,register}.blade.php
│       ├── dashboard/index.blade.php
│       ├── lectures/{index,create,show}.blade.php
│       ├── lectures/partials/card.blade.php
│       ├── components/*.blade.php     ← 7 components
│       ├── partials/{sidebar,topbar,footer}.blade.php
│       └── home.blade.php
├── routes/
│   ├── web.php
│   └── console.php
└── storage/
```

---

## 🗄 Database Schema

```
users (1) ──< (N) lectures
                    ├──< (1) transcripts ──< (N) transcript_segments
                    ├──< (1) summaries
                    ├──< (N) quizzes ──< (N) quiz_options
                    ├──< (N) flashcards
                    └──< (N) processing_jobs
```

**Quan hệ chính:**
- Một `Lecture` có một `Transcript` (1-1)
- Một `Transcript` có nhiều `TranscriptSegment` (1-N) — mỗi segment có timestamp + speaker
- Một `Lecture` có một `Summary` (1-1) — chứa brief + key_takeaways (JSON array)
- Một `Lecture` có nhiều `Quiz` (1-N), mỗi quiz có nhiều `QuizOption` (1-N)
- Một `Lecture` có nhiều `Flashcard` (1-N)

Xem chi tiết trong `database/migrations/`.

---

## 🔄 Luồng xử lý chính

```
1. User mở /lectures/create
        ↓
2. Upload file audio (drag-drop hoặc ghi âm từ micro)
        ↓
3. POST /lectures → LectureController::store()
        ↓
4. Lưu file vào storage/app/public/lectures/{year}/{month}/
        ↓
5. Tạo record Lecture (status='pending')
        ↓
6. Gọi AudioProcessingService::process($lecture)
        ↓
7. Service gọi GeminiService::processAudio($path, $mime)
   - Gửi audio base64 + prompt yêu cầu JSON có cấu trúc
   - Gemini trả về: { transcript, segments, summary, quizzes, flashcards }
        ↓
8. Pipeline 4 stages:
   - transcribing (15%) → save Transcript + Segments
   - analyzing (50%)    → save Summary
   - generating (80%)   → save Quizzes + Flashcards
   - completed (100%)
        ↓
9. Redirect về /lectures/{id} với kết quả đầy đủ
```

---

## 🤖 Tích hợp Gemini AI

Dự án dùng Google **Gemini 1.5 Flash** (multi-modal) — có thể nhận audio trực tiếp qua `inline_data` mà không cần tách bước STT riêng. Điều này giúp:

- **Đơn giản hơn**: 1 API call duy nhất thay vì 2 (Whisper + GPT)
- **Tiết kiệm hơn**: Gemini Flash free tier 15 req/phút
- **Chính xác hơn với tiếng Việt**: Mô hình hiểu ngữ cảnh tiếng Việt tốt hơn Whisper

### Prompt thiết kế

Xem chi tiết trong `app/Services/GeminiService.php` — yêu cầu AI trả về JSON thuần (không markdown) với cấu trúc:
```json
{
  "transcript": { "full_text": "...", "segments": [...] },
  "summary": { "brief": "...", "key_takeaways": [...], "topics": [...] },
  "quizzes": [{ "question": "...", "options": [...], "correct_index": 0 }],
  "flashcards": [{ "term": "...", "definition": "..." }]
}
```

### Backup option: OpenAI Whisper

Có thể thay thế bằng OpenAI Whisper API bằng cách tạo service mới implement logic tương tự trong `app/Services/`.

---

## 🎨 UI Highlights

- **Dark mode** với palette: `ink-950` (bg) + `brand-500` (violet) + `accent-500` (cyan)
- **Glassmorphism** (`backdrop-blur-xl` + `bg-ink-900/60`)
- **Gradient text** cho tiêu đề quan trọng
- **Aurora background animation** ở các trang auth
- **Equalizer bars animation** cho audio waveform
- **Flip card** cho flashcards
- **Progress bar shimmer** cho AI processing
- **Click-to-seek transcript** — click đoạn text để tua audio đến giây tương ứng
- **Realtime status poll** — tự động reload khi AI xong

---

## 🧪 Test

```bash
php artisan test
```

---

## 📦 Triển khai Production

```bash
# 1. Build assets
npm run build

# 2. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Storage symlink
php artisan storage:link

# 4. Set APP_ENV=production và APP_DEBUG=false trong .env
```

Upload lên **Shared Hosting** (cPanel) hoặc **VPS** với Nginx/Apache. Đảm bảo:
- PHP 8.2+
- MySQL accessible
- Thư mục `storage/` và `bootstrap/cache/` writable
- Cron job chạy `php artisan schedule:run` mỗi phút (nếu dùng scheduler)

---

## 📝 Các route chính

| Method | URL | Name | Mô tả |
|---|---|---|---|
| GET | `/` | `home` | Landing page |
| GET/POST | `/login` | `login` | Đăng nhập |
| GET/POST | `/register` | `register` | Đăng ký |
| POST | `/logout` | `logout` | Đăng xuất |
| GET | `/dashboard` | `dashboard` | Dashboard |
| GET | `/lectures` | `lectures.index` | Danh sách bài giảng |
| GET | `/lectures/create` | `lectures.create` | Upload |
| POST | `/lectures` | `lectures.store` | Lưu + xử lý AI |
| GET | `/lectures/{id}` | `lectures.show` | Chi tiết |
| DELETE | `/lectures/{id}` | `lectures.destroy` | Xóa |
| GET | `/lectures/{id}/status` | `lectures.status` | API check status (realtime poll) |
| POST | `/quizzes/{id}/submit` | `quizzes.submit` | Submit quiz |

---

## 🐛 Xử lý lỗi thường gặp

### Lỗi "GEMINI_API_KEY chưa được cấu hình"
→ Mở `.env`, điền `GEMINI_API_KEY=...`

### Lỗi "File vượt quá XX MB"
→ Tăng `AUDIO_MAX_SIZE_KB` trong `.env` hoặc check `php.ini` (`upload_max_filesize`, `post_max_size`)

### Lỗi "Class 'vite' not found"
→ Chạy `npm install` và `npm run build` rồi `php artisan view:clear`

### Lỗi "SQLSTATE[HY000] [2002]"
→ MySQL chưa chạy hoặc sai thông tin trong `.env` (host/port/user/pass)

### Lỗi 403 khi xem bài giảng
→ Lecture thuộc user khác. LectureController có check ownership.

---

## 📄 License

MIT License — thoải mái sử dụng cho mục đích học tập.

---

## 👥 Credits

- **Laravel Framework** — https://laravel.com
- **Tailwind CSS** — https://tailwindcss.com
- **Google Gemini AI** — https://ai.google.dev
- **Heroicons** — https://heroicons.com
- **Inter / Space Grotesk** — Google Fonts

---

> Made with ❤️ for the **AI in Education** mini-project.
> Đề tài: *Xây dựng hệ thống "AI Lecture & Voice Notes"* — Quản lý ghi âm bài giảng / Lớp học thông minh.