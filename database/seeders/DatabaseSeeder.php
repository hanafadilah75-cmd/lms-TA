<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\UserAnswer;
use App\Models\EssayReview;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Companies with specific categories:
        // - Pihak Startup (Penyedia Platform)
        // - Pihak Perusahaan Klien (Pengguna Layanan)
        $startupCompany = Company::create([
            'name' => 'EduTech Global Startup (Penyedia Platform)',
            'type' => 'startup',
            'address' => 'One Pacific Place Lt. 10, SCBD, Jakarta Selatan',
            'email' => 'provider@edutech.io',
            'phone' => '021-5088123',
        ]);

        $company1 = Company::create([
            'name' => 'Perusahaan X (Tech Solutions Inc.)',
            'type' => 'client',
            'address' => 'Gedung Cyber 2 Lt. 15, Jl. HR Rasuna Said, Jakarta Selatan',
            'email' => 'hrd@techsolutions.com',
            'phone' => '021-5551234',
        ]);

        $company2 = Company::create([
            'name' => 'Perusahaan Y (Creative Studio LLC)',
            'type' => 'client',
            'address' => 'Jl. Dago No. 120, Coblong, Bandung',
            'email' => 'hello@creativestudio.co.id',
            'phone' => '022-7774321',
        ]);

        // 2. Create Users
        // Rule:
        // - Tutor diarahkan/dikaitkan ke pihak startup
        // - Peserta/Karyawan diarahkan/dikaitkan ke perusahaan klien
        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_id' => $startupCompany->id,
        ]);

        // Tutors (Secara otomatis dikaitkan ke Pihak Startup)
        $tutorA = User::create([
            'name' => 'Tanya Tutor',
            'email' => 'tutor1@lms.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'company_id' => $startupCompany->id,
        ]);

        User::create([
            'name' => 'hana fadilah',
            'email' => 'hana@lms.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'company_id' => $startupCompany->id,
        ]);

        // Employees (Secara otomatis dikaitkan ke Perusahaan Klien)
        $employeeA1 = User::create([
            'name' => 'Alice Employee',
            'email' => 'alice@tech.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'company_id' => $company1->id,
        ]);

        $employeeA2 = User::create([
            'name' => 'Bob Employee',
            'email' => 'bob@tech.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'company_id' => $company1->id,
        ]);

        $employeeB1 = User::create([
            'name' => 'Charlie Employee',
            'email' => 'charlie@creative.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'company_id' => $company2->id,
        ]);

        $employeeB2 = User::create([
            'name' => 'David Employee',
            'email' => 'david@creative.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'company_id' => $company2->id,
        ]);

        // 3. Create Courses
        $course1 = Course::create([
            'tutor_id' => $tutorA->id,
            'title' => 'Kursus A (Startup Engineering 101)',
            'description' => 'Materi dasar pengembangan aplikasi web untuk startup menggunakan framework Laravel 11+ dan Tailwind CSS. Kursus ini mencakup konsep MVC, Database Migrations, dan Eloquent ORM.',
            'thumbnail' => null,
            'level' => 'beginner',
        ]);

        $course2 = Course::create([
            'tutor_id' => $tutorA->id,
            'title' => 'Kursus B (Modern Design Foundations)',
            'description' => 'Materi prinsip dasar visual design dan UX/UI untuk startup kreatif. Di sini Anda akan mempelajari Typography, Visual Hierarchy, dan Layouting dasar.',
            'thumbnail' => null,
            'level' => 'intermediate',
        ]);

        // Assign Courses to Client Companies (Many-to-Many):
        // Memetakan secara spesifik untuk perusahaan yang berbeda:
        // - "Kursus A untuk Perusahaan X"
        // - "Kursus A untuk Perusahaan Y"
        // - "Kursus B untuk Perusahaan X"
        // - "Kursus B untuk Perusahaan Y"
        $course1->companies()->attach([$company1->id, $company2->id]);
        $course2->companies()->attach([$company1->id, $company2->id]);

        // 4. Create Modules
        // Course 1 Modules
        $m1 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Pengenalan Arsitektur MVC Laravel',
            'content' => '<h3>Apa itu MVC?</h3><p>Model-View-Controller adalah pola arsitektur perangkat lunak yang membagi logika aplikasi menjadi tiga komponen yang saling terhubung:</p><ul><li><b>Model:</b> Mengelola data dan logika bisnis utama.</li><li><b>View:</b> Menangani representasi visual dan antarmuka pengguna (UI).</li><li><b>Controller:</b> Menghubungkan Model and View, memproses input pengguna dan mengirimkan data yang sesuai.</li></ul><p>Laravel menggunakan pola ini secara mendalam untuk membantu developer memisahkan kekhawatiran (separation of concerns) dan merapikan kode program.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
            'order_number' => 1,
        ]);

        $m2 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Eloquent ORM dan Migrasi Database',
            'content' => '<h3>Konsep Database Migration</h3><p>Migrasi adalah kontrol versi untuk database Anda, yang memungkinkan tim Anda memodifikasi dan membagikan skema database aplikasi dengan mudah.</p><h3>Eloquent ORM</h3><p>Eloquent ORM adalah Object-Relational Mapper bawaan Laravel yang menyediakan implementasi ActiveRecord yang elegan dan sederhana untuk bekerja dengan database Anda. Setiap tabel database memiliki "Model" pendamping yang digunakan untuk berinteraksi dengan tabel tersebut.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=376vEykL3Fc',
            'order_number' => 2,
        ]);

        // Course 2 Modules
        $m3 = Module::create([
            'course_id' => $course2->id,
            'title' => 'Prinsip Hierarki Visual dalam UI',
            'content' => '<h3>Apa itu Hierarki Visual?</h3><p>Hierarki visual adalah susunan atau presentasi elemen desain dengan cara yang menyiratkan pentingnya elemen tersebut. Desainer menggunakan ukuran, kontras, warna, dan posisi untuk mengarahkan pandangan audiens.</p><p>Prinsip utama:</p><ol><li>Ukuran yang lebih besar menarik perhatian lebih dahulu.</li><li>Warna cerah memiliki bobot visual lebih berat.</li><li>Ruang kosong (White Space) membantu memfokuskan elemen penting.</li></ol>',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order_number' => 1,
        ]);

        $m4 = Module::create([
            'course_id' => $course2->id,
            'title' => 'Tipografi dan Layouting Dasar',
            'content' => '<h3>Dasar-dasar Tipografi</h3><p>Tipografi adalah seni menata huruf agar teks mudah dibaca dan menarik secara visual. Pemilihan font (Sans Serif vs Serif), kerning, leading, dan kontras warna teks sangat menentukan kegunaan antarmuka.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order_number' => 2,
        ]);

        // 5. Create Quizzes & Questions
        // Quiz MCQ (Course 1)
        $q1 = Quiz::create([
            'course_id' => $course1->id,
            'title' => 'Kuis Pilihan Ganda Dasar Laravel',
            'type' => 'multiple_choice',
            'passing_grade' => 60,
            'duration' => 15,
        ]);

        $qn1 = Question::create([
            'quiz_id' => $q1->id,
            'question' => 'Apakah kepanjangan dari MVC dalam pola arsitektur Laravel?',
            'type' => 'multiple_choice',
        ]);
        Option::create(['question_id' => $qn1->id, 'option_text' => 'Model View Controller', 'is_correct' => true]);
        Option::create(['question_id' => $qn1->id, 'option_text' => 'Module Variable Class', 'is_correct' => false]);
        Option::create(['question_id' => $qn1->id, 'option_text' => 'Maintain View Code', 'is_correct' => false]);

        $qn2 = Question::create([
            'quiz_id' => $q1->id,
            'question' => 'Perintah CLI untuk menjalankan migrasi database yang baru dibuat di Laravel adalah?',
            'type' => 'multiple_choice',
        ]);
        Option::create(['question_id' => $qn2->id, 'option_text' => 'php artisan make:migration', 'is_correct' => false]);
        Option::create(['question_id' => $qn2->id, 'option_text' => 'php artisan migrate', 'is_correct' => true]);
        Option::create(['question_id' => $qn2->id, 'option_text' => 'php artisan db:seed', 'is_correct' => false]);

        // Quiz Essay (Course 1)
        $q2 = Quiz::create([
            'course_id' => $course1->id,
            'title' => 'Ujian Esai Arsitektur Laravel',
            'type' => 'essay',
            'passing_grade' => 70,
            'duration' => 20,
        ]);

        $qn_eloquent = Question::create([
            'quiz_id' => $q2->id,
            'question' => 'Jelaskan perbedaan antara Eloquent ORM dengan Query Builder biasa di Laravel, serta kapan saat terbaik menggunakannya!',
            'type' => 'essay',
        ]);

        $qn_service = Question::create([
            'quiz_id' => $q2->id,
            'question' => 'Bagaimana cara kerja Service Provider di Laravel? Jelaskan kegunaan method register() dan boot()!',
            'type' => 'essay',
        ]);

        // Quiz MCQ (Course 2)
        $q3 = Quiz::create([
            'course_id' => $course2->id,
            'title' => 'Evaluasi MCQ UI Design',
            'type' => 'multiple_choice',
            'passing_grade' => 60,
            'duration' => 10,
        ]);

        $qn3 = Question::create([
            'quiz_id' => $q3->id,
            'question' => 'Manakah elemen di bawah ini yang paling mempengaruhi hierarki visual dalam sebuah tata letak website?',
            'type' => 'multiple_choice',
        ]);
        Option::create(['question_id' => $qn3->id, 'option_text' => 'Ukuran elemen (Size) dan Kontras', 'is_correct' => true]);
        Option::create(['question_id' => $qn3->id, 'option_text' => 'Jumlah kata dalam teks', 'is_correct' => false]);
        Option::create(['question_id' => $qn3->id, 'option_text' => 'Domain hosting yang digunakan', 'is_correct' => false]);

        // Quiz Essay (Course 2)
        $q4 = Quiz::create([
            'course_id' => $course2->id,
            'title' => 'Ujian Esai Prinsip Desain',
            'type' => 'essay',
            'passing_grade' => 70,
            'duration' => 25,
        ]);

        $qn_hierarchy = Question::create([
            'quiz_id' => $q4->id,
            'question' => 'Jelaskan mengapa penggunaan kontras dan white space (ruang kosong) sangat krusial dalam merancang visual hierarchy!',
            'type' => 'essay',
        ]);

        // ==========================================
        // SEED ANSWERS & REVIEWS FOR TESTING
        // ==========================================

        // 1. Alice Employee (company 1) submits Essay for Course 1 (managed by Tanya Tutor, company 1)
        $ans1 = UserAnswer::create([
            'user_id' => $employeeA1->id,
            'question_id' => $qn_eloquent->id,
            'answer' => 'Eloquent ORM menggunakan model berbasis ActiveRecord untuk interaksi database yang lebih OOP dan elegan. Di sisi lain, Query Builder menggunakan antarmuka fluent untuk menulis SQL mentah secara lebih efisien. Eloquent sangat cocok untuk operasi CRUD harian dan mempermudah pemeliharaan kode, sedangkan Query Builder lebih disarankan untuk memproses data dalam volume sangat besar karena kinerjanya yang lebih cepat.',
            'ai_score' => 88,
            'ai_feedback' => 'Jawaban sangat baik dan komprehensif. Perbedaan antara Eloquent dan Query Builder dijelaskan secara tepat beserta kasus penggunaannya.',
            'final_score' => null, // pending review
        ]);
        EssayReview::create([
            'user_answer_id' => $ans1->id,
            'ai_score' => 88,
            'ai_feedback' => 'Jawaban sangat baik dan komprehensif. Perbedaan antara Eloquent dan Query Builder dijelaskan secara tepat beserta kasus penggunaannya.',
            'tutor_score' => null,
            'tutor_notes' => null,
        ]);

        $ans2 = UserAnswer::create([
            'user_id' => $employeeA1->id,
            'question_id' => $qn_service->id,
            'answer' => 'Service Provider adalah pusat inisialisasi (bootstrapping) aplikasi Laravel. Method register() digunakan untuk mendaftarkan binding ke dalam Service Container tanpa melakukan logika eksternal lainnya. Method boot() dipanggil setelah semua Service Provider selesai terdaftar, sehingga kita dapat memanggil service lain yang sudah siap digunakan.',
            'ai_score' => 82,
            'ai_feedback' => 'Penjelasan mengenai Service Provider, register(), dan boot() sudah tepat dan mudah dipahami.',
            'final_score' => null, // pending review
        ]);
        EssayReview::create([
            'user_answer_id' => $ans2->id,
            'ai_score' => 82,
            'ai_feedback' => 'Penjelasan mengenai Service Provider, register(), dan boot() sudah tepat dan mudah dipahami.',
            'tutor_score' => null,
            'tutor_notes' => null,
        ]);

        // 2. Charlie Employee (company 2) submits Essay for Course 2 (managed by Tanya Tutor)
        $ans3 = UserAnswer::create([
            'user_id' => $employeeB1->id,
            'question_id' => $qn_hierarchy->id,
            'answer' => 'Kontras membedakan elemen penting (seperti tombol utama) dari elemen sekitarnya agar mata pengguna tertuju ke sana pertama kali. White space (ruang kosong) memberikan ruang bernapas visual bagi desain, sehingga informasi tidak menumpuk dan alur membaca pengguna menjadi lebih terfokus.',
            'ai_score' => 92,
            'ai_feedback' => 'Definisi dan fungsi kontras serta white space dijelaskan dengan sangat baik dan terstruktur.',
            'final_score' => null, // pending review
        ]);
        EssayReview::create([
            'user_answer_id' => $ans3->id,
            'ai_score' => 92,
            'ai_feedback' => 'Definisi dan fungsi kontras serta white space dijelaskan dengan sangat baik dan terstruktur.',
            'tutor_score' => null,
            'tutor_notes' => null,
        ]);

        // ==========================================
        // SEED USER MODULE PROGRESS FOR DEMONSTRATION
        // ==========================================
        // 1. "Kursus A untuk Perusahaan X (Tech Solutions Inc.)":
        //    All employees (Alice & Bob) finished all modules (m1 & m2) -> 100% Selesai!
        UserProgress::firstOrCreate(['user_id' => $employeeA1->id, 'module_id' => $m1->id], ['status' => 'completed', 'completed_at' => now()]);
        UserProgress::firstOrCreate(['user_id' => $employeeA1->id, 'module_id' => $m2->id], ['status' => 'completed', 'completed_at' => now()]);
        UserProgress::firstOrCreate(['user_id' => $employeeA2->id, 'module_id' => $m1->id], ['status' => 'completed', 'completed_at' => now()]);
        UserProgress::firstOrCreate(['user_id' => $employeeA2->id, 'module_id' => $m2->id], ['status' => 'completed', 'completed_at' => now()]);

        // 2. "Kursus A untuk Perusahaan Y (Creative Studio LLC)":
        //    Varied completion: David finished both modules (100%), Charlie has 0% -> Average: 50%
        UserProgress::firstOrCreate(['user_id' => $employeeB2->id, 'module_id' => $m1->id], ['status' => 'completed', 'completed_at' => now()]);
        UserProgress::firstOrCreate(['user_id' => $employeeB2->id, 'module_id' => $m2->id], ['status' => 'completed', 'completed_at' => now()]);

        // 3. "Kursus B untuk Perusahaan X (Tech Solutions Inc.)":
        //    Varied completion: Alice finished module 3 (50%), Bob has 0% -> Average: 25%
        UserProgress::firstOrCreate(['user_id' => $employeeA1->id, 'module_id' => $m3->id], ['status' => 'completed', 'completed_at' => now()]);
    }
}
