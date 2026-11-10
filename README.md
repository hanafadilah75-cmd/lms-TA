# 🚀 Onboardify - LMS E-Training Onboarding Startup

**Onboardify** adalah platform *Learning Management System* (LMS) berbasis web yang dirancang khusus untuk mengoptimalkan proses *training* dan orientasi (*onboarding*) karyawan startup. Platform ini mengintegrasikan pembelajaran modular, kuis evaluasi berbasis pilihan ganda dan esai, serta penilaian otomatis esai menggunakan **Artificial Intelligence (AI via Groq / Llama 3)** dengan verifikasi akhir oleh tutor (*Human-in-the-Loop*).

Platform ini mengusung antarmuka visual modern bertema **Soft Neo-Brutalist & Pastel Card** (kartu bergaris tegas, sudut membulat ramah, *capsule progress bar*, serta palet warna pastel mint, sunflower yellow, coral pink, dan sky blue).

---

## 📑 Daftar Isi
1. [Tech Stack & Arsitektur](#-tech-stack--arsitektur)
2. [Matriks Role & Fitur Pengguna](#-matriks-role--fitur-pengguna)
3. [Fitur Unggulan Sistem](#-fitur-unggulan-sistem)
4. [Skema Basis Data & Relasi](#-skema-basis-data--relasi)
5. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
6. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
7. [Pengujian Otomatis (Automated Testing)](#-pengujian-otomatis-automated-testing)

---

## 🛠 Tech Stack & Arsitektur

* **Framework Backend**: Laravel 12 (PHP 8.2+)
* **Basis Data**: MySQL (Produksi/Lokal), SQLite `:memory:` (Pengujian Unit & Fitur)
* **Frontend**: Blade Templating Engine
* **Styling & CSS**: Tailwind CSS v4, Google Fonts (*Plus Jakarta Sans*)
* **Build Tool**: Vite v8
* **AI Engine**: Groq SDK / API (Model `llama-3.3-70b-versatile` / `llama3-8b-8192`) untuk penilaian otomatis esai
* **Autentikasi**: Laravel Session-based Auth dengan Role-Based Access Control (RBAC) & Middleware kustom

---

## 👥 Matriks Role & Fitur Pengguna

Sistem ini membagi akses ke dalam 3 role utama:

| Fitur / Modul | Admin | Tutor | Karyawan |
| :--- | :---: | :---: | :---: |
| **Login & Profil Pribadi** | ✅ | ✅ | ✅ |
| **Dashboard Khusus Role** | ✅ (Global Analytics) | ✅ (Tutor Analytics) | ✅ (Personal Progress) |
| **Kelola Perusahaan (Startup & Klien)** | ✅ (CRUD) | ❌ | ❌ |
| **Kelola Akun User (Tutor & Karyawan)** | ✅ (CRUD & Filter) | ❌ | ❌ |
| **Kelola Kursus Pelatihan** | ✅ (Semua Kursus) | ✅ (Kursus Diampu) | ❌ |
| **Penugasan Kursus ke Klien (Multi-Tenant)** | ✅ | ❌ | ❌ |
| **Kelola Modul Materi & Video Embed** | ✅ | ✅ | ❌ |
| **Kelola Kuis, Soal & Kunci Jawaban** | ✅ | ✅ | ❌ |
| **Review & Finalisasi Nilai Esai AI** | ✅ | ✅ (Sesuai Kursus) | ❌ |
| **Pemantauan Rapor Progres Karyawan** | ✅ (Semua Klien) | ✅ (Klien Terkait) | ✅ (Hanya Milik Sendiri) |
| **Mempelajari Materi Kursus** | ✅ | ✅ | ✅ (Sesuai Perusahaan) |
| **Mengerjakan Kuis Interaktif + Timer** | ❌ | ❌ | ✅ |
| **Melihat Hasil & Feedback AI/Tutor** | ❌ | ❌ | ✅ |

---

## 🌟 Fitur Unggulan Sistem

### 1. 🏢 Manajemen Multi-Klien & Pemisahan Entitas
* **Entitas Startup (Penyedia Platform)**: Tempat bernaung akun Administrator dan Tutor.
* **Entitas Perusahaan Klien**: Perusahaan mitra tempat karyawan/peserta onboarding terdaftar.
* Kursus dapat ditugaskan secara selektif ke satu atau banyak perusahaan klien (*many-to-many relationship*).
* Karyawan hanya dapat melihat dan mengakses kursus yang ditugaskan secara resmi ke perusahaan mereka.

### 2. 📚 Modul Pembelajaran Terstruktur
* Materi tersusun berdasarkan nomor urut (*order number*) bertahap.
* Integrasi pemutar video YouTube otomatis (konversi URL watch menjadi embed interaktif).
* Format bacaan kaya (*rich text / HTML support*).
* Tombol **"Tandai Selesai & Lanjut"** yang otomatis merekam jejak progres belajar ke basis data.
* Navigasi intuitif antar-modul (*Previous/Next Module*).

### 3. ⏱️ Kuis Evaluasi Cerdas & Timer Real-time
* Dua tipe pertanyaan: **Pilihan Ganda** (*Multiple Choice*) dan **Esai** (*Essay*).
* **Proteksi Prasyarat**: Karyawan wajib menuntaskan 100% modul materi kursus sebelum gembok kuis terbuka.
* **Timer Hitung Mundur Interaktif**: Otomatis mengirimkan (*auto-submit*) jawaban jika waktu pengerjaan habis.
* **Anti-Duplicate Submission**: Mencegah pengerjaan ganda atau manipulasi submit ulang.

### 4. 🤖 AI-Powered Essay Grading (Groq Llama 3)
* Ketika karyawan mengirimkan jawaban esai, sistem memanggil API Groq secara *real-time*.
* AI menganalisis kesesuaian jawaban terhadap pertanyaan, memberikan nilai estimasi (0-100), feedback kekuatan jawaban, dan saran perbaikan (*suggestion*).
* Status esai ditandai **"Menunggu Review Tutor"** hingga diverifikasi.

### 5. 👨‍🏫 Human-in-the-Loop Tutor Review
* Tutor dapat memeriksa jawaban karyawan berdampingan dengan skor dan masukan yang diberikan AI.
* Tutor memiliki wewenang penuh untuk menerima skor AI atau mengubahnya sesuai kriteria penilaian manusia.
* Tutor dapat menyematkan catatan pembinaan personal yang akan langsung tampil di rapor evaluasi karyawan.

### 6. 📊 Dashboard Analitik & Rapor Progres Belajar
* **Admin Dashboard**: Total perusahaan, total karyawan, total kursus, rata-rata kelulusan global, grafik SVG persentase per klien, pelacakan kursus per perusahaan klien, dan rincian individual.
* **Tutor Dashboard**: Ringkasan kursus yang diampu, antrean review esai yang membutuhkan tindakan, serta rata-rata nilai kuis peserta.
* **Karyawan Dashboard**: Target kemajuan onboarding personal, persentase kelulusan, daftar materi yang sedang dipelajari, dan riwayat nilai kuis.
* **Rapor Rinci**: Pemantauan detail status pengerjaan modul dan kuis per karyawan dengan filter perusahaan.

---

## 🗄 Skema Basis Data & Relasi

1. **`users`**:
   * Menyimpan data kredensial, role (`admin`, `tutor`, `karyawan`), relasi `company_id`, dan avatar.
2. **`companies`**:
   * Menyimpan profil perusahaan dengan tipe `startup` atau `client`.
3. **`courses`**:
   * Menyimpan kursus onboarding, tingkat kesulitan (`beginner`, `intermediate`, `advanced`), relasi `tutor_id`, dan thumbnail.
4. **`company_course` (Pivot)**:
   * Menghubungkan kursus dengan perusahaan klien yang diberikan hak akses.
5. **`modules`**:
   * Bab materi per kursus, menyimpan judul, konten teks/HTML, video URL YouTube, dan nomor urutan.
6. **`quizzes`**:
   * Evaluasi kursus, menyimpan passing grade, durasi pengerjaan (menit), dan tipe evaluasi.
7. **`questions` & `options`**:
   * Butir-butir pertanyaan kuis beserta opsi jawaban dan penanda kunci jawaban benar.
8. **`user_progress`**:
   * Mencatat modul-modul yang telah diselesaikan oleh masing-masing karyawan beserta waktu selesai.
9. **`user_answers`**:
   * Jawaban yang dikirimkan karyawan, skor sementara AI, feedback AI, dan skor final setelah diverifikasi.
10. **`essay_reviews`**:
    * Jejak audit review esai oleh tutor (skor tutor dan catatan pembinaan).

---

## 📁 Struktur Direktori Proyek

```
LMS TAA/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php         # Autentikasi, login, logout, profil
│   │   │   ├── DashboardController.php    # Dashboard admin, tutor, karyawan
│   │   │   ├── CompanyController.php      # Kelola perusahaan & akun user
│   │   │   ├── CourseController.php       # Kelola kursus & penugasan klien
│   │   │   ├── ModuleController.php       # Kelola modul materi & progress
│   │   │   ├── QuizController.php         # Kelola kuis, pertanyaan, pengerjaan
│   │   │   ├── AiReviewController.php     # Antrean & finalisasi review esai AI
│   │   │   └── ProgressController.php     # Pemantauan rapor & progres belajar
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php         # Penjaga otorisasi role
│   │   └── Requests/                      # Form Request validation
│   ├── Models/                            # Model Eloquent (User, Course, Quiz, dll.)
│   └── Services/
│       └── GroqService.php                # Service integrasi Groq AI API
├── database/
│   ├── migrations/                        # Skema tabel database
│   └── seeders/                           # Data awal pengujian
├── resources/
│   ├── css/
│   │   └── app.css                        # Styling Tailwind v4 & utility Neo-Brutalist
│   └── views/
│       ├── layouts/app.blade.php          # Layout utama (Sidebar, Topbar, Alerts)
│       ├── auth/login.blade.php           # Halaman login pastel neo-card
│       ├── dashboard/                     # View dashboard (admin, tutor, karyawan)
│       ├── companies/                     # View kelola perusahaan & karyawan
│       ├── courses/                       # View katalog & detail kursus
│       ├── modules/                       # View materi modul pembelajaran
│       ├── quizzes/                       # View pengerjaan kuis, riwayat, kelola soal
│       ├── reviews/                       # View antrean review esai tutor
│       ├── progress/                      # View rapor progres belajar per klien/user
│       └── profile/edit.blade.php         # View edit profil & password
├── routes/
│   └── web.php                            # Definisi rute aplikasi & middleware grouping
└── tests/
    └── Feature/
        ├── CompanyCourseProgressTest.php  # Pengujian integrasi alur klien & progres
        └── DetailProgressAndRolesTest.php # Pengujian seluruh role, view, dan rapor
```

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Prasyarat Sistem
* PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`)
* Composer
* Node.js (>= 18) & NPM
* Server MySQL

### 2. Kloning & Pengaturan Environment
```bash
# Masuk ke direktori proyek
cd "LMS TAA"

# Salin file konfigurasi environment
cp .env.example .env

# Generate Application Encryption Key
php artisan key:generate
```

### 3. Konfigurasi Database & API Key di `.env`
Sesuaikan kredensial database dan API key Groq pada file `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_ta
DB_USERNAME=root
DB_PASSWORD=

# Kunci API Groq untuk Evaluasi Esai Otomatis
GROQ_API_KEY=gsk_your_groq_api_key_here
```

### 4. Instalasi Dependensi PHP & JavaScript
```bash
# Instal dependensi Composer backend
composer install

# Instal paket frontend NPM
npm install
```

### 5. Migrasi & Storage Link
```bash
# Jalankan migrasi tabel database
php artisan migrate

# Buat symbolic link untuk upload avatar & thumbnail
php artisan storage:link
```

### 6. Menjalankan Aplikasi
Buka dua terminal terpisah:

**Terminal 1 (Vite Dev Server untuk Asset CSS/JS):**
```bash
npm run dev
# Atau untuk build produksi:
npm run build
```

**Terminal 2 (Server Lokal Laravel):**
```bash
php artisan serve
```

Akses aplikasi di peramban web: [http://localhost:8000](http://localhost:8000).

---

## 🧪 Pengujian Otomatis (Automated Testing)

Proyek ini telah dilengkapi dengan rangkaian *automated test* (Feature & Unit Tests) berbasis PHPUnit / SQLite in-memory:

```bash
php artisan test
```

**Status Pengujian Terverifikasi:**
```text
   PASS  Tests\Unit\ExampleTest
   ✓ that true is true

   PASS  Tests\Feature\ExampleTest
   ✓ the application returns a successful response

   PASS  Tests\Feature\GroqServiceTest
   ✓ groq service review essay returns valid structure

   PASS  Tests\Feature\CompanyCourseProgressTest
   ✓ user creation conditional company routing rule
   ✓ dashboard groups course per company with correct progress

   PASS  Tests\Feature\DetailProgressAndRolesTest
   ✓ admin view employee progress detail without answers
   ✓ admin view employee progress detail with pending essay
   ✓ employee access authorization on progress detail
   ✓ dashboards render for all roles
   ✓ company employee search and filter
   ✓ course and module views
   ✓ reviews views
   ✓ login page renders

  Tests:    14 passed (64 assertions)
  Duration: 0.61s
  Result:   PASSED (100%)
```

---

## 📄 Lisensi
Hak Cipta © 2026 Onboardify Platform. Dikembangkan untuk keperluan Onboarding E-Training Karyawan Startup.
