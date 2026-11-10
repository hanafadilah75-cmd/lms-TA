<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

error_reporting(E_ALL & ~E_DEPRECATED);

echo "========================================================================\n";
echo "🎯 TESTING FITUR KUSTOMISASI JAWABAN (ROLE TINGGI: ADMIN & TUTOR)\n";
echo "========================================================================\n\n";

$admin = User::where('role', 'admin')->first();
$tutor1 = User::where('email', 'tutor1@lms.com')->first();
$tutor2 = User::where('email', 'tutor2@lms.com')->first();
$karyawan = User::where('role', 'karyawan')->first();

$quiz1 = Quiz::with('questions.options')->find(1); // MCQ
$quiz2 = Quiz::with('questions')->find(2); // Essay

$quizController = $app->make(\App\Http\Controllers\QuizController::class);

$allPassed = true;

// -------------------------------------------------------------------------
// 1. UJI ADMIN: Kustomisasi Soal Pilihan Ganda & Ganti Kunci Jawaban
// -------------------------------------------------------------------------
echo "👑 [1/4] UJI ADMIN: Kustomisasi Soal Pilihan Ganda & Mengubah Kunci Jawaban\n";
Auth::login($admin);
$mcqQuestion = $quiz1->questions->first();

// Buka form edit
$resEdit = $quizController->editQuestion($quiz1, $mcqQuestion);
if ($resEdit->name() === 'quizzes.edit_question') {
    echo "  ✅ Admin berhasil membuka form kustomisasi soal (quizzes.edit_question)\n";
} else {
    echo "  ❌ Admin gagal membuka form edit\n";
    $allPassed = false;
}

// Admin mengubah teks opsi dan memilih Opsi B (index 1) sebagai Kunci Jawaban Baru
$reqUpdateMcq = Request::create("/quizzes/{$quiz1->id}/questions/{$mcqQuestion->id}", 'PUT', [
    'question' => 'Apakah kepanjangan dari MVC dalam pola arsitektur software web? (Diverifikasi Admin)',
    'type' => 'multiple_choice',
    'options' => [
        'Model Virtual Controller (Salah)',
        'Model View Controller (KUNCI BARU ADMIN)',
        'Module View Container (Salah)',
        'Maintain View Class (Salah)',
    ],
    'correct_option' => 1 // Kunci baru di Opsi B (index 1)
]);

$quizController->updateQuestion($reqUpdateMcq, $quiz1, $mcqQuestion);
$mcqQuestion->refresh();
$mcqQuestion->load('options');

$correctOpt = $mcqQuestion->options->firstWhere('is_correct', true);
if ($correctOpt && strpos($correctOpt->option_text, 'KUNCI BARU ADMIN') !== false) {
    echo "  ✅ Kunci jawaban berhasil dikustomisasi oleh Admin ke Opsi B: '{$correctOpt->option_text}'\n";
} else {
    echo "  ❌ Kunci jawaban gagal diperbarui oleh Admin\n";
    $allPassed = false;
}

// -------------------------------------------------------------------------
// 2. UJI TUTOR 2: Kustomisasi Soal Esai & Mengisi Kunci Jawaban Acuan
// -------------------------------------------------------------------------
echo "\n👨‍🏫 [2/4] UJI TUTOR 2: Kustomisasi Soal Esai & Menetapkan Kunci Jawaban Acuan\n";
Auth::login($tutor2);
$essayQuestion = $quiz2->questions->first();

$resEditEssay = $quizController->editQuestion($quiz2, $essayQuestion);
if ($resEditEssay->name() === 'quizzes.edit_question') {
    echo "  ✅ Tutor 2 berhasil membuka form kustomisasi soal esai\n";
} else {
    echo "  ❌ Tutor 2 gagal membuka form edit\n";
    $allPassed = false;
}

$customIdealAnswer = "KUNCI JAWABAN CUSTOM TUTOR 2: Karyawan harus menjelaskan aspek Active Record pada Eloquent ORM untuk pemetaan relasi model, serta efisiensi Query Builder untuk performa query data mentah berkecepatan tinggi.";

$reqUpdateEssay = Request::create("/quizzes/{$quiz2->id}/questions/{$essayQuestion->id}", 'PUT', [
    'question' => $essayQuestion->question . ' (Kustomisasi Tutor 2)',
    'type' => 'essay',
    'ideal_answer' => $customIdealAnswer
]);

$quizController->updateQuestion($reqUpdateEssay, $quiz2, $essayQuestion);
$essayQuestion->refresh();

if ($essayQuestion->ideal_answer === $customIdealAnswer) {
    echo "  ✅ Kunci jawaban acuan esai berhasil dikustomisasi oleh Tutor 2!\n";
    echo "     - Cuplikan: " . substr($essayQuestion->ideal_answer, 0, 70) . "...\n";
} else {
    echo "  ❌ Kustomisasi esai oleh Tutor 2 gagal\n";
    $allPassed = false;
}

// -------------------------------------------------------------------------
// 3. UJI TUTOR 1: Menambah Opsi Jawaban Baru (Opsi E) pada Pilihan Ganda
// -------------------------------------------------------------------------
echo "\n👨‍🏫 [3/4] UJI TUTOR 1: Menambah Opsi Jawaban Baru pada Soal Pilihan Ganda\n";
Auth::login($tutor1);
$reqAddOption = Request::create("/quizzes/{$quiz1->id}/questions/{$mcqQuestion->id}", 'PUT', [
    'question' => $mcqQuestion->question,
    'type' => 'multiple_choice',
    'options' => [
        'Opsi A: Model Virtual Controller',
        'Opsi B: Model View Controller (KUNCI BENAR)',
        'Opsi C: Module View Container',
        'Opsi D: Maintain View Class',
        'Opsi E: Jawaban Custom Tambahan Tutor 1'
    ],
    'correct_option' => 1
]);

$quizController->updateQuestion($reqAddOption, $quiz1, $mcqQuestion);
$mcqQuestion->refresh();
$mcqQuestion->load('options');

if ($mcqQuestion->options->count() === 5) {
    echo "  ✅ Tutor 1 berhasil mengkustomisasi soal menjadi 5 opsi jawaban (A, B, C, D, E)!\n";
} else {
    echo "  ❌ Penambahan opsi oleh Tutor 1 gagal\n";
    $allPassed = false;
}

// -------------------------------------------------------------------------
// 4. UJI KEAMANAN: Karyawan Dilarang Mengubah Soal / Kunci Jawaban
// -------------------------------------------------------------------------
echo "\n🔒 [4/4] UJI KEAMANAN: Role Karyawan Dilarang Kustomisasi Kunci Jawaban\n";
Auth::login($karyawan);
$isForbidden = false;

try {
    $quizController->editQuestion($quiz1, $mcqQuestion);
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    if ($e->getStatusCode() === 403) {
        $isForbidden = true;
    }
}

if ($isForbidden) {
    echo "  ✅ Karyawan diblokir dari kustomisasi soal (HTTP 403 Forbidden - AMAN)\n";
} else {
    echo "  ❌ Keamanan gagal: Karyawan dapat mengedit kunci jawaban!\n";
    $allPassed = false;
}

echo "\n========================================================================\n";
if ($allPassed) {
    echo "🎉 HASIL AKHIR: FITUR KUSTOMISASI SOAL & KUNCI JAWABAN SUKSES 100%!\n";
    echo "   - Admin & Tutor memiliki wewenang penuh mengkustomisasi opsi & kunci.\n";
    echo "   - Pilihan Ganda: Bebas ubah kalimat, opsi teks, dan kunci benar.\n";
    echo "   - Esai: Bebas rumus rubrik acuan ideal untuk panduan AI & Tutor.\n";
    echo "   - Karyawan tidak dapat mengubah kunci jawaban (terlindungi 403).\n";
} else {
    echo "⚠️ Beberapa tahap uji coba kustomisasi gagal.\n";
}
echo "========================================================================\n";

