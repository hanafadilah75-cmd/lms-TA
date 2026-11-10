-- ========================================================
-- LMS TA - FULL DATABASE DUMP (FOR PHPMYADMIN / MYSQL)
-- Generated on: 2026-10-29 14:41:30
-- ========================================================

CREATE DATABASE IF NOT EXISTS `lms_ta` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lms_ta`;

SET FOREIGN_KEY_CHECKS=0;

create table `companies` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `address` text null, `email` varchar(255) null, `phone` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
create table `users` (`id` bigint unsigned not null auto_increment primary key, `company_id` bigint unsigned null, `name` varchar(255) not null, `email` varchar(255) not null, `email_verified_at` timestamp null, `password` varchar(255) not null, `role` varchar(255) not null default 'karyawan', `avatar` varchar(255) null, `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `users` add constraint `users_company_id_foreign` foreign key (`company_id`) references `companies` (`id`) on delete set null;
alter table `users` add unique `users_email_unique`(`email`);
create table `password_reset_tokens` (`email` varchar(255) not null, `token` varchar(255) not null, `created_at` timestamp null, primary key (`email`));
create table `sessions` (`id` varchar(255) not null, `user_id` bigint unsigned null, `ip_address` varchar(45) null, `user_agent` text null, `payload` longtext not null, `last_activity` int not null, primary key (`id`));
alter table `sessions` add index `sessions_user_id_index`(`user_id`);
alter table `sessions` add index `sessions_last_activity_index`(`last_activity`);
create table `cache` (`key` varchar(255) not null, `value` mediumtext not null, `expiration` bigint not null, primary key (`key`));
alter table `cache` add index `cache_expiration_index`(`expiration`);
create table `cache_locks` (`key` varchar(255) not null, `owner` varchar(255) not null, `expiration` bigint not null, primary key (`key`));
alter table `cache_locks` add index `cache_locks_expiration_index`(`expiration`);
create table `jobs` (`id` bigint unsigned not null auto_increment primary key, `queue` varchar(255) not null, `payload` longtext not null, `attempts` smallint unsigned not null, `reserved_at` int unsigned null, `available_at` int unsigned not null, `created_at` int unsigned not null);
alter table `jobs` add index `jobs_queue_index`(`queue`);
create table `job_batches` (`id` varchar(255) not null, `name` varchar(255) not null, `total_jobs` int not null, `pending_jobs` int not null, `failed_jobs` int not null, `failed_job_ids` longtext not null, `options` mediumtext null, `cancelled_at` int null, `created_at` int not null, `finished_at` int null, primary key (`id`));
create table `failed_jobs` (`id` bigint unsigned not null auto_increment primary key, `uuid` varchar(255) not null, `connection` varchar(255) not null, `queue` varchar(255) not null, `payload` longtext not null, `exception` longtext not null, `failed_at` timestamp not null default CURRENT_TIMESTAMP);
alter table `failed_jobs` add index `failed_jobs_connection_queue_failed_at_index`(`connection`, `queue`, `failed_at`);
alter table `failed_jobs` add unique `failed_jobs_uuid_unique`(`uuid`);
create table `courses` (`id` bigint unsigned not null auto_increment primary key, `tutor_id` bigint unsigned not null, `title` varchar(255) not null, `description` text null, `thumbnail` varchar(255) null, `level` varchar(255) not null default 'beginner', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `courses` add constraint `courses_tutor_id_foreign` foreign key (`tutor_id`) references `users` (`id`) on delete cascade;
create table `modules` (`id` bigint unsigned not null auto_increment primary key, `course_id` bigint unsigned not null, `title` varchar(255) not null, `content` longtext null, `video_url` varchar(255) null, `order_number` int not null default '1', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `modules` add constraint `modules_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete cascade;
create table `quizzes` (`id` bigint unsigned not null auto_increment primary key, `course_id` bigint unsigned not null, `title` varchar(255) not null, `type` varchar(255) not null default 'multiple_choice', `passing_grade` int not null default '60', `duration` int not null default '30', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `quizzes` add constraint `quizzes_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete cascade;
create table `questions` (`id` bigint unsigned not null auto_increment primary key, `quiz_id` bigint unsigned not null, `question` text not null, `type` varchar(255) not null default 'multiple_choice', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `questions` add constraint `questions_quiz_id_foreign` foreign key (`quiz_id`) references `quizzes` (`id`) on delete cascade;
create table `options` (`id` bigint unsigned not null auto_increment primary key, `question_id` bigint unsigned not null, `option_text` text not null, `is_correct` tinyint(1) not null default '0', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null);
alter table `options` add constraint `options_question_id_foreign` foreign key (`question_id`) references `questions` (`id`) on delete cascade;
create table `user_progress` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `module_id` bigint unsigned not null, `status` varchar(255) not null default 'completed', `completed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `user_progress` add constraint `user_progress_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `user_progress` add constraint `user_progress_module_id_foreign` foreign key (`module_id`) references `modules` (`id`) on delete cascade;
create table `user_answers` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `question_id` bigint unsigned not null, `answer` text null, `ai_score` float(53) null, `ai_feedback` text null, `final_score` float(53) null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `user_answers` add constraint `user_answers_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `user_answers` add constraint `user_answers_question_id_foreign` foreign key (`question_id`) references `questions` (`id`) on delete cascade;
create table `essay_reviews` (`id` bigint unsigned not null auto_increment primary key, `user_answer_id` bigint unsigned not null, `ai_score` float(53) null, `ai_feedback` text null, `tutor_score` float(53) null, `tutor_notes` text null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `essay_reviews` add constraint `essay_reviews_user_answer_id_foreign` foreign key (`user_answer_id`) references `user_answers` (`id`) on delete cascade;
alter table `companies` add `type` enum('startup', 'client') not null default 'client' after `phone`;
create table `company_course` (`id` bigint unsigned not null auto_increment primary key, `company_id` bigint unsigned not null, `course_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `company_course` add constraint `company_course_company_id_foreign` foreign key (`company_id`) references `companies` (`id`) on delete cascade;
alter table `company_course` add constraint `company_course_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete cascade;

-- ========================================================
-- DATA INSERTS
-- ========================================================

-- Table data for: `companies`
INSERT INTO `companies` (`id`, `name`, `address`, `email`, `phone`, `created_at`, `updated_at`, `deleted_at`, `type`) VALUES (1, "EduTech Global Startup (Penyedia Platform)", "One Pacific Place Lt. 10, SCBD, Jakarta Selatan", "provider@edutech.io", "021-5088123", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL, "startup");
INSERT INTO `companies` (`id`, `name`, `address`, `email`, `phone`, `created_at`, `updated_at`, `deleted_at`, `type`) VALUES (2, "Perusahaan X (Tech Solutions Inc.)", "Gedung Cyber 2 Lt. 15, Jl. HR Rasuna Said, Jakarta Selatan", "hrd@techsolutions.com", "021-5551234", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL, "client");
INSERT INTO `companies` (`id`, `name`, `address`, `email`, `phone`, `created_at`, `updated_at`, `deleted_at`, `type`) VALUES (3, "Perusahaan Y (Creative Studio LLC)", "Jl. Dago No. 120, Coblong, Bandung", "hello@creativestudio.co.id", "022-7774321", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL, "client");

-- Table data for: `users`
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, "Super Admin", "admin@lms.com", NULL, "$2y$04$f.lAym7gSG1jtXVrmUFZ5uuvuPoJ60EOcnH4TDqnAgqp4.gKU/URy", "admin", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, "Tanya Tutor", "tutor1@lms.com", NULL, "$2y$04$81qwv.cJDPX.uZZ.9pCbQODzpkP4iDSpsc5cZSLZ6n7vZQr1F/3GO", "tutor", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 1, "hana fadilah", "hana@lms.com", NULL, "$2y$04$QxKMdmfQkSGQDxo0KYQSCuSLk2xFXEBepiX/KSvSmMTp4/fCYq9Pa", "tutor", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 2, "Alice Employee", "alice@tech.com", NULL, "$2y$04$qMu5OX9NEr7pTPWZUYpvz.cF73E7ewbMVzDYXdB7SsUasyGQdpSrK", "karyawan", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 2, "Bob Employee", "bob@tech.com", NULL, "$2y$04$4zkic.Sbwp1cVufM5zqLOOQ92BmtIiZoqBZ8OUWB6gSRh3bIus426", "karyawan", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 3, "Charlie Employee", "charlie@creative.com", NULL, "$2y$04$6javrFlkGnazEenRRxPEHueSUAZS6pZyEPv9E6vMLaUOa7LMhx586", "karyawan", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, 3, "David Employee", "david@creative.com", NULL, "$2y$04$oRs3rBfz9RMY/.tAG6HOv.BzKceDWOmp0FbeL549W2CQRKCf.U9ey", "karyawan", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `courses`
INSERT INTO `courses` (`id`, `tutor_id`, `title`, `description`, `thumbnail`, `level`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 2, "Kursus A (Startup Engineering 101)", "Materi dasar pengembangan aplikasi web untuk startup menggunakan framework Laravel 11+ dan Tailwind CSS. Kursus ini mencakup konsep MVC, Database Migrations, dan Eloquent ORM.", NULL, "beginner", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `courses` (`id`, `tutor_id`, `title`, `description`, `thumbnail`, `level`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 2, "Kursus B (Modern Design Foundations)", "Materi prinsip dasar visual design dan UX/UI untuk startup kreatif. Di sini Anda akan mempelajari Typography, Visual Hierarchy, dan Layouting dasar.", NULL, "intermediate", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `company_course`
INSERT INTO `company_course` (`id`, `company_id`, `course_id`, `created_at`, `updated_at`) VALUES (1, 2, 1, NULL, NULL);
INSERT INTO `company_course` (`id`, `company_id`, `course_id`, `created_at`, `updated_at`) VALUES (2, 3, 1, NULL, NULL);
INSERT INTO `company_course` (`id`, `company_id`, `course_id`, `created_at`, `updated_at`) VALUES (3, 2, 2, NULL, NULL);
INSERT INTO `company_course` (`id`, `company_id`, `course_id`, `created_at`, `updated_at`) VALUES (4, 3, 2, NULL, NULL);

-- Table data for: `modules`
INSERT INTO `modules` (`id`, `course_id`, `title`, `content`, `video_url`, `order_number`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, "Pengenalan Arsitektur MVC Laravel", "<h3>Apa itu MVC?</h3><p>Model-View-Controller adalah pola arsitektur perangkat lunak yang membagi logika aplikasi menjadi tiga komponen yang saling terhubung:</p><ul><li><b>Model:</b> Mengelola data dan logika bisnis utama.</li><li><b>View:</b> Menangani representasi visual dan antarmuka pengguna (UI).</li><li><b>Controller:</b> Menghubungkan Model and View, memproses input pengguna dan mengirimkan data yang sesuai.</li></ul><p>Laravel menggunakan pola ini secara mendalam untuk membantu developer memisahkan kekhawatiran (separation of concerns) dan merapikan kode program.</p>", "https://www.youtube.com/watch?v=ImtZ5yENzgE", 1, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `modules` (`id`, `course_id`, `title`, `content`, `video_url`, `order_number`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, "Eloquent ORM dan Migrasi Database", "<h3>Konsep Database Migration</h3><p>Migrasi adalah kontrol versi untuk database Anda, yang memungkinkan tim Anda memodifikasi dan membagikan skema database aplikasi dengan mudah.</p><h3>Eloquent ORM</h3><p>Eloquent ORM adalah Object-Relational Mapper bawaan Laravel yang menyediakan implementasi ActiveRecord yang elegan dan sederhana untuk bekerja dengan database Anda. Setiap tabel database memiliki \"Model\" pendamping yang digunakan untuk berinteraksi dengan tabel tersebut.</p>", "https://www.youtube.com/watch?v=376vEykL3Fc", 2, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `modules` (`id`, `course_id`, `title`, `content`, `video_url`, `order_number`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 2, "Prinsip Hierarki Visual dalam UI", "<h3>Apa itu Hierarki Visual?</h3><p>Hierarki visual adalah susunan atau presentasi elemen desain dengan cara yang menyiratkan pentingnya elemen tersebut. Desainer menggunakan ukuran, kontras, warna, dan posisi untuk mengarahkan pandangan audiens.</p><p>Prinsip utama:</p><ol><li>Ukuran yang lebih besar menarik perhatian lebih dahulu.</li><li>Warna cerah memiliki bobot visual lebih berat.</li><li>Ruang kosong (White Space) membantu memfokuskan elemen penting.</li></ol>", "https://www.youtube.com/watch?v=dQw4w9WgXcQ", 1, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `modules` (`id`, `course_id`, `title`, `content`, `video_url`, `order_number`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 2, "Tipografi dan Layouting Dasar", "<h3>Dasar-dasar Tipografi</h3><p>Tipografi adalah seni menata huruf agar teks mudah dibaca dan menarik secara visual. Pemilihan font (Sans Serif vs Serif), kerning, leading, dan kontras warna teks sangat menentukan kegunaan antarmuka.</p>", "https://www.youtube.com/watch?v=dQw4w9WgXcQ", 2, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `quizzes`
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `type`, `passing_grade`, `duration`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, "Kuis Pilihan Ganda Dasar Laravel", "multiple_choice", 60, 15, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `type`, `passing_grade`, `duration`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, "Ujian Esai Arsitektur Laravel", "essay", 70, 20, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `type`, `passing_grade`, `duration`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 2, "Evaluasi MCQ UI Design", "multiple_choice", 60, 10, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `type`, `passing_grade`, `duration`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 2, "Ujian Esai Prinsip Desain", "essay", 70, 25, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `questions`
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, "Apakah kepanjangan dari MVC dalam pola arsitektur Laravel?", "multiple_choice", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, "Perintah CLI untuk menjalankan migrasi database yang baru dibuat di Laravel adalah?", "multiple_choice", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 2, "Jelaskan perbedaan antara Eloquent ORM dengan Query Builder biasa di Laravel, serta kapan saat terbaik menggunakannya!", "essay", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 2, "Bagaimana cara kerja Service Provider di Laravel? Jelaskan kegunaan method register() dan boot()!", "essay", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 3, "Manakah elemen di bawah ini yang paling mempengaruhi hierarki visual dalam sebuah tata letak website?", "multiple_choice", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `questions` (`id`, `quiz_id`, `question`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 4, "Jelaskan mengapa penggunaan kontras dan white space (ruang kosong) sangat krusial dalam merancang visual hierarchy!", "essay", "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `options`
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, "Model View Controller", 1, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, "Module Variable Class", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 1, "Maintain View Code", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 2, "php artisan make:migration", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 2, "php artisan migrate", 1, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 2, "php artisan db:seed", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, 5, "Ukuran elemen (Size) dan Kontras", 1, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (8, 5, "Jumlah kata dalam teks", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);
INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`, `created_at`, `updated_at`, `deleted_at`) VALUES (9, 5, "Domain hosting yang digunakan", 0, "2026-10-29 14:25:02", "2026-10-29 14:25:02", NULL);

-- Table data for: `user_progress`
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (1, 4, 1, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (2, 4, 2, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (3, 5, 1, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (4, 5, 2, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (5, 7, 1, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (6, 7, 2, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_progress` (`id`, `user_id`, `module_id`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES (7, 4, 3, "completed", "2026-10-29 14:25:02", "2026-10-29 14:25:02", "2026-10-29 14:25:02");

-- Table data for: `user_answers`
INSERT INTO `user_answers` (`id`, `user_id`, `question_id`, `answer`, `ai_score`, `ai_feedback`, `final_score`, `created_at`, `updated_at`) VALUES (1, 4, 3, "Eloquent ORM menggunakan model berbasis ActiveRecord untuk interaksi database yang lebih OOP dan elegan. Di sisi lain, Query Builder menggunakan antarmuka fluent untuk menulis SQL mentah secara lebih efisien. Eloquent sangat cocok untuk operasi CRUD harian dan mempermudah pemeliharaan kode, sedangkan Query Builder lebih disarankan untuk memproses data dalam volume sangat besar karena kinerjanya yang lebih cepat.", 88, "Jawaban sangat baik dan komprehensif. Perbedaan antara Eloquent dan Query Builder dijelaskan secara tepat beserta kasus penggunaannya.", NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_answers` (`id`, `user_id`, `question_id`, `answer`, `ai_score`, `ai_feedback`, `final_score`, `created_at`, `updated_at`) VALUES (2, 4, 4, "Service Provider adalah pusat inisialisasi (bootstrapping) aplikasi Laravel. Method register() digunakan untuk mendaftarkan binding ke dalam Service Container tanpa melakukan logika eksternal lainnya. Method boot() dipanggil setelah semua Service Provider selesai terdaftar, sehingga kita dapat memanggil service lain yang sudah siap digunakan.", 82, "Penjelasan mengenai Service Provider, register(), dan boot() sudah tepat dan mudah dipahami.", NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `user_answers` (`id`, `user_id`, `question_id`, `answer`, `ai_score`, `ai_feedback`, `final_score`, `created_at`, `updated_at`) VALUES (3, 6, 6, "Kontras membedakan elemen penting (seperti tombol utama) dari elemen sekitarnya agar mata pengguna tertuju ke sana pertama kali. White space (ruang kosong) memberikan ruang bernapas visual bagi desain, sehingga informasi tidak menumpuk dan alur membaca pengguna menjadi lebih terfokus.", 92, "Definisi dan fungsi kontras serta white space dijelaskan dengan sangat baik dan terstruktur.", NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");

-- Table data for: `essay_reviews`
INSERT INTO `essay_reviews` (`id`, `user_answer_id`, `ai_score`, `ai_feedback`, `tutor_score`, `tutor_notes`, `created_at`, `updated_at`) VALUES (1, 1, 88, "Jawaban sangat baik dan komprehensif. Perbedaan antara Eloquent dan Query Builder dijelaskan secara tepat beserta kasus penggunaannya.", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `essay_reviews` (`id`, `user_answer_id`, `ai_score`, `ai_feedback`, `tutor_score`, `tutor_notes`, `created_at`, `updated_at`) VALUES (2, 2, 82, "Penjelasan mengenai Service Provider, register(), dan boot() sudah tepat dan mudah dipahami.", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");
INSERT INTO `essay_reviews` (`id`, `user_answer_id`, `ai_score`, `ai_feedback`, `tutor_score`, `tutor_notes`, `created_at`, `updated_at`) VALUES (3, 3, 92, "Definisi dan fungsi kontras serta white space dijelaskan dengan sangat baik dan terstruktur.", NULL, NULL, "2026-10-29 14:25:02", "2026-10-29 14:25:02");

SET FOREIGN_KEY_CHECKS=1;
