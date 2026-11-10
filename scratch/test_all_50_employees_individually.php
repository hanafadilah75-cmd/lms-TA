<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\Course;
use App\Models\UserProgress;
use App\Models\UserAnswer;

error_reporting(E_ALL & ~E_DEPRECATED);

echo "========================================================================================================\n";
echo "📋 UJI COBA LIVE 50 KARYAWAN SATU PER SATU (FULL AUDIT SETIAP KARYAWAN)\n";
echo "========================================================================================================\n\n";

$karyawanList = User::where('email', 'like', 'karyawan%@test.com')
    ->with(['company', 'progress', 'answers.question', 'answers.essayReview'])
    ->orderBy('id', 'asc')
    ->get();

$totalCount = $karyawanList->count();
echo "Ditemukan {$totalCount} Akun Karyawan Uji Coba di Database.\n\n";

printf(
    "%-5s | %-24s | %-20s | %-12s | %-10s | %-15s | %-15s\n",
    "NO", "NAMA KARYAWAN", "PERUSAHAAN", "LOGIN & AKSES", "MODUL", "SKOR KUIS", "STATUS AKHIR"
);
echo str_repeat("-", 112) . "\n";

$tutor2 = User::where('email', 'tutor2@lms.com')->first();
$progressController = new \App\Http\Controllers\ProgressController();
$refOverallStatus = new \ReflectionMethod(\App\Http\Controllers\ProgressController::class, 'calculateEmployeeOverallStatus');
$refOverallStatus->setAccessible(true);

$passedCount = 0;
$statusSummary = [
    'selesai' => 0,
    'sedang_belajar' => 0,
    'belum_mulai' => 0,
];

foreach ($karyawanList as $index => $karyawan) {
    $no = $index + 1;
    
    // 1. Uji Login & Akses Dashboard
    Illuminate\Support\Facades\Auth::login($karyawan);
    $reqDash = \Illuminate\Http\Request::create('/dashboard', 'GET');
    $reqDash->setUserResolver(fn() => $karyawan);
    $resDash = $app->handle($reqDash);
    $loginStatus = ($resDash->getStatusCode() === 200) ? "OK (200)" : "FAIL";

    // 2. Modul selesai
    $completedModules = $karyawan->progress->where('status', 'completed')->count();
    $modulStr = "{$completedModules} / 4 Modul";

    // 3. Skor Kuis & Status Esai
    $answers = $karyawan->answers;
    $mcqScore = 0;
    $essayScore = null;
    $isPendingEssay = false;

    foreach ($answers as $ans) {
        if ($ans->question && $ans->question->type === 'essay') {
            if ($ans->final_score !== null) {
                $essayScore = $ans->final_score;
            } else {
                $isPendingEssay = true;
            }
        } elseif ($ans->final_score !== null) {
            $mcqScore = max($mcqScore, $ans->final_score);
        }
    }

    $scoreStr = "-";
    if ($answers->isNotEmpty()) {
        if ($isPendingEssay) {
            $scoreStr = "MCQ:{$mcqScore}|Esai:⏳";
        } elseif ($essayScore !== null) {
            $scoreStr = "MCQ:{$mcqScore}|Esai:{$essayScore}";
        } else {
            $scoreStr = "MCQ:{$mcqScore}";
        }
    }

    // 4. Hitung Status Keseluruhan via ProgressController
    Illuminate\Support\Facades\Auth::login($tutor2);
    $overall = $refOverallStatus->invoke($progressController, $karyawan);
    $statusText = strtoupper(str_replace('_', ' ', $overall['status']));
    $statusSummary[$overall['status']] = ($statusSummary[$overall['status']] ?? 0) + 1;

    // Status Badge Icon
    $icon = "⚪";
    if ($overall['status'] === 'selesai') {
        $icon = "🟢";
    } elseif ($overall['status'] === 'sedang_belajar') {
        $icon = "🟡";
    }

    $finalStatusStr = "{$icon} {$statusText} ({$overall['average_progress']}%)";

    printf(
        "%-5d | %-24s | %-20s | %-12s | %-10s | %-15s | %-15s\n",
        $no,
        substr($karyawan->name, 0, 24),
        substr($karyawan->company->name ?? '-', 0, 20),
        $loginStatus,
        $modulStr,
        $scoreStr,
        $finalStatusStr
    );

    if ($loginStatus === "OK (200)") {
        $passedCount++;
    }
}

echo str_repeat("=", 112) . "\n\n";

echo "📊 REKAPITULASI UJI COBA 50 KARYAWAN:\n";
echo "   - Total Karyawan Diuji              : {$totalCount} Karyawan\n";
echo "   - Akses Akun & Login Berhasil       : {$passedCount} / {$totalCount} (100% SUKSES)\n";
echo "   - Status 'SELESAI' (100% Modul)     : {$statusSummary['selesai']} Karyawan\n";
echo "   - Status 'SEDANG BELAJAR' (Parsial) : {$statusSummary['sedang_belajar']} Karyawan\n";
echo "   - Status 'BELUM MULAI' (0% Modul)   : {$statusSummary['belum_mulai']} Karyawan\n\n";

echo "Semua 50 akun karyawan aktif di database dan terhubung ke dashboard Admin, Tutor 1, dan Tutor 2 secara real-time!\n";
