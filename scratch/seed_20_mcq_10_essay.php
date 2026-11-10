<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\UserAnswer;
use App\Models\EssayReview;
use Illuminate\Support\Facades\DB;

error_reporting(E_ALL & ~E_DEPRECATED);

echo "========================================================================\n";
echo "🎯 SETUP 20 SOAL PILIHAN GANDA & 10 SOAL ESAI PADA KUIS LMS\n";
echo "========================================================================\n\n";

DB::beginTransaction();

try {
    $quiz1 = Quiz::find(1); // Kursus A - MCQ
    $quiz2 = Quiz::find(2); // Kursus A - Essay

    if (!$quiz1 || !$quiz2) {
        throw new Exception("Kuis 1 atau Kuis 2 tidak ditemukan!");
    }

    // Bersihkan jawaban lama agar tidak terjadi inkonsistensi foreign key
    $oldQuestionIds = Question::whereIn('quiz_id', [$quiz1->id, $quiz2->id])->pluck('id');
    $oldAnswerIds = UserAnswer::whereIn('question_id', $oldQuestionIds)->pluck('id');
    EssayReview::whereIn('user_answer_id', $oldAnswerIds)->delete();
    UserAnswer::whereIn('id', $oldAnswerIds)->delete();
    Option::whereIn('question_id', $oldQuestionIds)->delete();
    Question::whereIn('quiz_id', [$quiz1->id, $quiz2->id])->delete();

    echo "🧹 Soal lama pada Kuis 1 & Kuis 2 berhasil dibersihkan.\n";

    // -------------------------------------------------------------------------
    // 1. SETUP 20 SOAL PILIHAN GANDA (QUIZ 1)
    // -------------------------------------------------------------------------
    echo "📝 Membuat 20 Soal Pilihan Ganda (Quiz 1)...\n";
    $quiz1->update(['duration' => 30, 'passing_grade' => 70]);

    $mcqQuestions = [
        [
            'q' => 'Apakah kepanjangan dari MVC dalam pola arsitektur software web?',
            'opts' => [
                ['text' => 'Model View Controller', 'correct' => true],
                ['text' => 'Module Variable Class', 'correct' => false],
                ['text' => 'Maintain View Code', 'correct' => false],
                ['text' => 'Managed Virtual Container', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah CLI Artisan untuk menjalankan migrasi database yang baru dibuat di Laravel adalah?',
            'opts' => [
                ['text' => 'php artisan migrate', 'correct' => true],
                ['text' => 'php artisan db:migrate', 'correct' => false],
                ['text' => 'php artisan make:migration', 'correct' => false],
                ['text' => 'php artisan database:run', 'correct' => false],
            ]
        ],
        [
            'q' => 'File konfigurasi environment utama pada proyek Laravel disimpan dalam file bernama?',
            'opts' => [
                ['text' => '.env', 'correct' => true],
                ['text' => 'config.json', 'correct' => false],
                ['text' => 'environment.xml', 'correct' => false],
                ['text' => 'setting.ini', 'correct' => false],
            ]
        ],
        [
            'q' => 'Direktori manakah yang berfungsi sebagai web root (entry point) publik di Laravel?',
            'opts' => [
                ['text' => 'public/', 'correct' => true],
                ['text' => 'resources/', 'correct' => false],
                ['text' => 'app/', 'correct' => false],
                ['text' => 'storage/', 'correct' => false],
            ]
        ],
        [
            'q' => 'HTTP method manakah yang paling tepat digunakan untuk memperbarui sebagian (partial update) data resource?',
            'opts' => [
                ['text' => 'PATCH', 'correct' => true],
                ['text' => 'GET', 'correct' => false],
                ['text' => 'POST', 'correct' => false],
                ['text' => 'DELETE', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah Artisan yang benar untuk membuat Controller baru adalah?',
            'opts' => [
                ['text' => 'php artisan make:controller NamaController', 'correct' => true],
                ['text' => 'php artisan create:controller NamaController', 'correct' => false],
                ['text' => 'php artisan new:controller NamaController', 'correct' => false],
                ['text' => 'php artisan build:controller NamaController', 'correct' => false],
            ]
        ],
        [
            'q' => 'Komponen Laravel yang bertugas menyaring dan memvalidasi HTTP Request sebelum mencapai controller adalah?',
            'opts' => [
                ['text' => 'Middleware', 'correct' => true],
                ['text' => 'View Blade', 'correct' => false],
                ['text' => 'Database Seeder', 'correct' => false],
                ['text' => 'Artisan Command', 'correct' => false],
            ]
        ],
        [
            'q' => 'Template engine bawaan yang digunakan secara default oleh framework Laravel adalah?',
            'opts' => [
                ['text' => 'Blade', 'correct' => true],
                ['text' => 'Twig', 'correct' => false],
                ['text' => 'Smarty', 'correct' => false],
                ['text' => 'Handlebars', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah Artisan yang tepat untuk membuat file migration tabel baru adalah?',
            'opts' => [
                ['text' => 'php artisan make:migration create_users_table', 'correct' => true],
                ['text' => 'php artisan db:create_table users', 'correct' => false],
                ['text' => 'php artisan generate:migration users', 'correct' => false],
                ['text' => 'php artisan schema:create users', 'correct' => false],
            ]
        ],
        [
            'q' => 'Di manakah file definisi routing web utama disimpan di Laravel?',
            'opts' => [
                ['text' => 'routes/web.php', 'correct' => true],
                ['text' => 'app/Routes.php', 'correct' => false],
                ['text' => 'config/routing.php', 'correct' => false],
                ['text' => 'public/routes.php', 'correct' => false],
            ]
        ],
        [
            'q' => 'Method Eloquent yang digunakan untuk mengambil data pertama atau melempar exception 404 jika tidak ditemukan adalah?',
            'opts' => [
                ['text' => 'firstOrFail()', 'correct' => true],
                ['text' => 'firstOrNull()', 'correct' => false],
                ['text' => 'getOrFail()', 'correct' => false],
                ['text' => 'findOrNone()', 'correct' => false],
            ]
        ],
        [
            'q' => 'Relasi "One to Many" dalam model Eloquent didefinisikan dengan method bernama?',
            'opts' => [
                ['text' => 'hasMany()', 'correct' => true],
                ['text' => 'belongsToMany()', 'correct' => false],
                ['text' => 'hasOne()', 'correct' => false],
                ['text' => 'manyToOne()', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah CLI untuk menghapus seluruh application cache, route cache, dan config cache di Laravel adalah?',
            'opts' => [
                ['text' => 'php artisan optimize:clear', 'correct' => true],
                ['text' => 'php artisan cache:delete_all', 'correct' => false],
                ['text' => 'php artisan remove:cache', 'correct' => false],
                ['text' => 'php artisan clean:all', 'correct' => false],
            ]
        ],
        [
            'q' => 'Directive Blade yang wajib ditambahkan pada setiap form HTML POST di Laravel untuk mencegah serangan pemalsuan request adalah?',
            'opts' => [
                ['text' => '@csrf', 'correct' => true],
                ['text' => '@auth', 'correct' => false],
                ['text' => '@security', 'correct' => false],
                ['text' => '@token', 'correct' => false],
            ]
        ],
        [
            'q' => 'Tipe kolom migration manakah yang digunakan untuk menyimpan nilai nominal uang dengan presisi tinggi?',
            'opts' => [
                ['text' => '$table->decimal()', 'correct' => true],
                ['text' => '$table->float()', 'correct' => false],
                ['text' => '$table->string()', 'correct' => false],
                ['text' => '$table->tinyInteger()', 'correct' => false],
            ]
        ],
        [
            'q' => 'Fitur Laravel yang digunakan untuk menangani pengiriman email dan tugas berat secara asinkron di latar belakang adalah?',
            'opts' => [
                ['text' => 'Queues and Jobs', 'correct' => true],
                ['text' => 'Session Manager', 'correct' => false],
                ['text' => 'Blade Directives', 'correct' => false],
                ['text' => 'Cookie Jar', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah Artisan untuk menjalankan development server lokal secara instan adalah?',
            'opts' => [
                ['text' => 'php artisan serve', 'correct' => true],
                ['text' => 'php artisan start', 'correct' => false],
                ['text' => 'php artisan run:server', 'correct' => false],
                ['text' => 'php artisan host', 'correct' => false],
            ]
        ],
        [
            'q' => 'Di folder manakah seluruh konfigurasi aplikasi seperti database, cache, dan mail disimpan?',
            'opts' => [
                ['text' => 'config/', 'correct' => true],
                ['text' => 'storage/', 'correct' => false],
                ['text' => 'bootstrap/', 'correct' => false],
                ['text' => 'app/Settings/', 'correct' => false],
            ]
        ],
        [
            'q' => 'Method Eloquent yang digunakan untuk melakukan Eager Loading relasi guna menghindari masalah N+1 query adalah?',
            'opts' => [
                ['text' => 'with()', 'correct' => true],
                ['text' => 'loadOnly()', 'correct' => false],
                ['text' => 'joinLazy()', 'correct' => false],
                ['text' => 'fetchRel()', 'correct' => false],
            ]
        ],
        [
            'q' => 'Perintah Artisan yang digunakan untuk membatalkan (rollback) satu batch migrasi database terakhir adalah?',
            'opts' => [
                ['text' => 'php artisan migrate:rollback', 'correct' => true],
                ['text' => 'php artisan migrate:undo', 'correct' => false],
                ['text' => 'php artisan migrate:down', 'correct' => false],
                ['text' => 'php artisan db:reset_last', 'correct' => false],
            ]
        ],
    ];

    foreach ($mcqQuestions as $item) {
        $q = Question::create([
            'quiz_id' => $quiz1->id,
            'question' => $item['q'],
            'type' => 'multiple_choice',
        ]);
        foreach ($item['opts'] as $opt) {
            Option::create([
                'question_id' => $q->id,
                'option_text' => $opt['text'],
                'is_correct' => $opt['correct'],
            ]);
        }
    }
    echo "  ✅ Sukses menambahkan " . count($mcqQuestions) . " soal pilihan ganda!\n";

    // -------------------------------------------------------------------------
    // 2. SETUP 10 SOAL ESAI (QUIZ 2)
    // -------------------------------------------------------------------------
    echo "📝 Membuat 10 Soal Esai (Quiz 2)...\n";
    $quiz2->update(['duration' => 60, 'passing_grade' => 70]);

    $essayQuestions = [
        'Jelaskan perbedaan mendasar antara Eloquent ORM dengan Query Builder biasa di Laravel, serta sebutkan kapan waktu terbaik menggunakan masing-masing!',
        'Bagaimana cara kerja Service Provider di Laravel? Jelaskan kegunaan method register() dan boot() serta alur eksekusinya!',
        'Jelaskan konsep Dependency Injection (DI) dan bagaimana Service Container di Laravel mengelolanya secara otomatis pada controller!',
        'Jelaskan apa itu Middleware dalam Laravel dan berikan contoh konkret kasus penggunaannya dalam menjaga keamanan atau otentikasi aplikasi!',
        'Apa yang dimaksud dengan masalah performa N+1 Query pada ORM, dan bagaimana teknik Eager Loading (with()) menyelesaikannya?',
        'Jelaskan konsep Database Transaction (DB::transaction) di Laravel dan mengapa rollback otomatis sangat penting pada operasi finansial atau multi-tabel!',
        'Jelaskan siklus hidup Request (Lifecycle of a Request) di Laravel dari entry point public/index.php hingga HTTP response dikembalikan ke browser!',
        'Bagaimana strategi yang tepat dalam mengamankan input pengguna dari ancaman SQL Injection dan Cross-Site Scripting (XSS) pada aplikasi Laravel?',
        'Jelaskan konsep Queue dan Jobs di Laravel, serta mengapa pemrosesan tugas asinkron krusial untuk skalabilitas sistem aplikasi web berbeban tinggi!',
        'Jelaskan perbedaan antara Otentikasi (Authentication) dan Otorisasi (Authorization) di Laravel, serta bagaimana Policies atau Gates diimplementasikan!',
    ];

    foreach ($essayQuestions as $questionText) {
        Question::create([
            'quiz_id' => $quiz2->id,
            'question' => $questionText,
            'type' => 'essay',
        ]);
    }
    echo "  ✅ Sukses menambahkan " . count($essayQuestions) . " soal esai!\n";

    DB::commit();
    echo "\n🎉 SEEDING BERHASIL! Total: 20 Pilihan Ganda & 10 Esai.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

