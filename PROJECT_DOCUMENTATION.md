# Dokumentasi Keseluruhan Sistem - Onboardify LMS

Dokumentasi ini merangkum seluruh arsitektur, isi sistem, fitur-fitur, dan hak akses pengguna pada aplikasi **Onboardify (Startup E-Training Onboarding LMS)**.

---

## 1. Ikhtisar Aplikasi
**Onboardify** adalah platform LMS (Learning Management System) multi-entitas yang dirancang untuk program *onboarding* karyawan di lingkungan startup. Sistem ini memiliki integrasi evaluasi kuis pilihan ganda dan esai yang dinilai secara otomatis menggunakan **AI (Groq / Llama 3)** dan diverifikasi oleh Tutor manusia.

Antarmuka sistem menggunakan tema visual **Soft Neo-Brutalist & Pastel Card** dengan kartu bergaris tegas (`border-2 border-slate-900`), sudut membulat ramah, bayangan taktil, dan palet warna pastel (*Mint Green, Sunflower Yellow, Coral Pink, Cyan Blue*).

---

## 2. Struktur Hak Akses & Matriks Fitur (Siapa Saja yang Bisa Menggunakan Fitur Apa)

Sistem memiliki **3 Role Pengguna**:

### A. Role: Administrator (Super Admin)
Admin memegang kendali penuh atas konfigurasi platform, entitas perusahaan, pengguna, kursus, dan monitoring global.

* **Fitur Utama Admin**:
  1. **Dashboard Analitik Terpusat**:
     * Metrik jumlah total perusahaan, total karyawan, total kursus, dan rata-rata kelulusan global.
     * Grafik batang persentase kemajuan per perusahaan klien.
     * Pelacakan kursus per perusahaan klien beserta daftar individual karyawan.
     * Shortcut aksi cepat (*Tambah Perusahaan, Tambah User, Buat Kursus*).
  2. **Kelola Perusahaan (`/companies`)**:
     * Tambah, lihat, ubah, dan hapus perusahaan.
     * Pengelompokan tipe: **Pihak Startup (Penyedia Platform)** vs **Perusahaan Klien (Pengguna Layanan)**.
  3. **Kelola Karyawan & Tutor (`/employees`)**:
     * Pendaftaran user baru dengan asosiasi otomatis (Tutor ke Startup, Karyawan ke Klien).
     * Filter pencarian berdasarkan nama, email, role, dan perusahaan.
     * Edit dan soft-delete user.
  4. **Kelola Kursus Pelatihan (`/courses`)**:
     * Pembuatan kursus lengkap dengan thumbnail, level (Beginner/Intermediate/Advanced), dan penugasan Tutor penanggung jawab.
     * **Penugasan Multi-Klien**: Menentukan perusahaan klien mana saja yang berhak mengakses kursus tersebut.
  5. **Kelola Modul Materi & Kuis**:
     * Hak akses penuh membuat dan mengedit modul materi serta kuis evaluasi.
  6. **Pemantauan Rapor Progres Global (`/progress`)**:
     * Melihat ikhtisar kemajuan per perusahaan klien.
     * Membuka rapor individual karyawan (status tiap kursus, persentase modul, dan nilai kuis).
  7. **Review Esai AI (`/reviews`)**:
     * Memantau dan melakukan intervensi penilaian esai jika diperlukan.

---

### B. Role: Tutor (Pengajar / Mentor)
Tutor bertanggung jawab atas penyusunan konten pelatihan, pembuatan kuis evaluasi, pemeriksaan esai peserta, dan pemantauan kelulusan. Seluruh tutor (Tutor 1, Tutor 2, dst.) berkolaborasi mengampu kurikulum, memeriksa lembar jawaban esai karyawan, dan memantau kemajuan belajar secara sinkron.

* **Fitur Utama Tutor**:
  1. **Dashboard Tutor (`/dashboard`)**:
     * Metrik jumlah kursus aktif, antrean esai yang menunggu review, dan rata-rata nilai kuis peserta secara sinkron.
     * Pemantauan kemajuan peserta di seluruh kelas dan perusahaan klien.
  2. **Kelola Materi Kursus & Modul (`/courses`, `/modules`)**:
     * Kolaborasi penuh dalam mengelola konten modul materi kursus.
     * Menyematkan video YouTube (otomatis terkonversi ke player embed).
     * Mengatur urutan materi (*order number*).
  3. **Kelola Kuis Evaluasi (`/courses/{course}/quizzes`)**:
     * Membuat kuis Pilihan Ganda atau Esai.
     * Menentukan passing grade kelulusan (%) dan batas waktu pengerjaan (menit).
     * Menyusun butir pertanyaan dan kunci jawaban.
  4. **Pemeriksaan & Finalisasi Nilai Esai AI (`/reviews`)**:
     * Halaman review esai AI tampil dan aktif untuk seluruh Tutor.
     * Melihat jawaban esai karyawan berdampingan dengan evaluasi, estimasi skor, dan saran yang dibuat oleh AI (Groq Llama 3).
     * Memfinalisasi nilai akhir (mengadopsi nilai AI atau memberi skor kustom 0-100).
     * Memberikan catatan/feedback langsung kepada karyawan.
  5. **Pemantauan Progres Belajar Karyawan (`/progress`)**:
     * Memantau daftar perusahaan klien dan rapor belajar setiap karyawan.

---

### C. Role: Karyawan (Peserta Onboarding)
Karyawan adalah peserta pelatihan yang mengikuti kurikulum onboarding perusahaan mereka.

* **Fitur Utama Karyawan**:
  1. **Dashboard Karyawan (`/dashboard`)**:
     * Kartu persentase target onboarding personal lengkap dengan *capsule progress bar*.
     * Daftar materi kursus aktif yang sedang dipelajari dengan tombol "Lanjutkan Belajar".
     * Riwayat kuis evaluasi terbaru beserta skor dan status kelulusan.
  2. **Katalog Materi Pelatihan (`/courses`)**:
     * Mengakses daftar kursus yang secara resmi ditugaskan ke perusahaan tempat karyawan bekerja.
     * Proteksi isolasi: Karyawan tidak dapat mengakses kursus perusahaan klien lain.
  3. **Mempelajari Modul Pembelajaran (`/modules/{module}`)**:
     * Menonton video materi YouTube tanpa distraksi iklan luar.
     * Membaca panduan materi.
     * Menekan tombol **"Tandai Selesai & Lanjut"** untuk membuka modul berikutnya.
  4. **Mengerjakan Kuis Interaktif (`/quizzes/{quiz}/take`)**:
     * Kuis otomatis terkunci sampai karyawan menuntaskan seluruh modul materi dalam kursus tersebut.
     * Timer hitung mundur interaktif (*sticky topbar*).
     * Auto-submit otomatis saat waktu habis.
     * Proteksi anti-pengerjaan ganda.
  5. **Rapor & Evaluasi Hasil Kuis (`/quizzes/{quiz}/history`)**:
     * Melihat skor akhir (Pilihan Ganda langsung terhitung, Esai menampilkan indikator menunggu review tutor).
     * Status kelulusan: *Lulus, Tidak Lulus,* atau *Menunggu Review*.
     * Ulasan per butir soal lengkap dengan feedback AI dan catatan dari Tutor.
  6. **Rapor Kemajuan Pribadi (`/progress`)**:
     * Memantau seluruh rekam jejak penyelesaian modul dan kuis secara mandiri.
  7. **Profil Akun (`/profile`)**:
     * Memperbarui foto profil (avatar), nama, email, dan kata sandi.

---

## 3. Komponen & Isi Direktori Teknis

* **Backend Controllers** ([`app/Http/Controllers/`](file:///Users/hanafadilah75gmail.com/Documents/S/LMS%20TAA/app/Http/Controllers)):
  * `AuthController.php`: Otentikasi sesi, login, logout, edit profil.
  * `DashboardController.php`: Agregasi statistik sesuai peran user.
  * `CompanyController.php`: Manajemen CRUD entitas perusahaan dan user karyawan/tutor.
  * `CourseController.php`: Manajemen kursus dan relasi perusahaan klien.
  * `ModuleController.php`: Manajemen modul materi, parsing video YouTube, pencatatan status selesai.
  * `QuizController.php`: Manajemen kuis, butir soal, logika pengerjaan, timer, submit, dan riwayat.
  * `AiReviewController.php`: Antrean review esai, integrasi data AI, dan finalisasi skor tutor.
  * `ProgressController.php`: Perhitungan persentase penyelesaian kursus dan rapor individual.
* **Services** ([`app/Services/`](file:///Users/hanafadilah75gmail.com/Documents/S/LMS%20TAA/app/Services)):
  * `GroqService.php`: Komunikasi HTTP ke Groq API (Llama 3) untuk analisis prompt esai dan ekstraksi JSON skor.
* **Models** ([`app/Models/`](file:///Users/hanafadilah75gmail.com/Documents/S/LMS%20TAA/app/Models)):
  * `User`, `Company`, `Course`, `Module`, `Quiz`, `Question`, `Option`, `UserAnswer`, `EssayReview`, `UserProgress`.
* **Testing** ([`tests/Feature/`](file:///Users/hanafadilah75gmail.com/Documents/S/LMS%20TAA/tests/Feature)):
  * `CompanyCourseProgressTest.php`: Tes integrasi relasi multi-klien dan persentase progres.
  * `DetailProgressAndRolesTest.php`: Tes terinci seluruh role, proteksi hak akses, dan verifikasi rapor bebas error 500.

---

## 4. Keamanan & Validasi Sistem
1. **Pemisahan Data Antar-Klien**: Karyawan hanya diizinkan melihat kursus yang diasosiasikan dengan `company_id` mereka.
2. **Role Middleware**: Membatasi aksi administratif hanya untuk Admin dan aksi kurikulum hanya untuk Tutor terkait.
3. **Pencegahan Error Evaluasi**: Safe null-coalescing pada kuis yang belum dikerjakan memastikan tidak ada `Undefined array key` atau `Error 500`.
4. **Proteksi Kuis Prasyarat**: Pengecekan jumlah modul selesai sebelum mengizinkan pengerjaan kuis.

