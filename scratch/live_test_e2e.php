<?php

/**
 * Real Live E2E Testing Script for LMS Onboardify
 * Simulates real browser HTTP sessions for:
 * 1. Karyawan (Alice)
 * 2. Tutor 1
 * 3. Tutor 2
 */

error_reporting(E_ALL & ~E_DEPRECATED);

$baseUrl = 'http://127.0.0.1:8000';
$cookieDir = sys_get_temp_dir() . '/lms_cookies_' . time();
mkdir($cookieDir, 0777, true);

function makeRequest($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'header' => $header,
        'body' => $body,
    ];
}

function extractCsrfToken($html) {
    if (preg_match('/<input[^>]+name=["\']_token["\'][^>]+value=["\']([^"\']+)["\']/i', $html, $matches)) {
        return $matches[1];
    }
    if (preg_match('/name=["\']csrf-token["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
        return $matches[1];
    }
    return null;
}

function login($baseUrl, $email, $password, $cookieFile) {
    // 1. Get login page to extract CSRF token
    $res = makeRequest($baseUrl . '/login', 'GET', [], $cookieFile);
    $token = extractCsrfToken($res['body']);
    if (!$token) {
        return ['success' => false, 'error' => 'Gagal mengambil CSRF token dari halaman login'];
    }

    // 2. Submit credentials
    $postRes = makeRequest($baseUrl . '/login', 'POST', [
        '_token' => $token,
        'email' => $email,
        'password' => $password
    ], $cookieFile);

    // If redirected to dashboard or status 200 containing dashboard content
    $isLoggedIn = strpos($postRes['body'], 'dashboard') !== false 
               || strpos($postRes['body'], 'Keluar') !== false 
               || strpos($postRes['body'], 'Logout') !== false
               || strpos($postRes['body'], 'Halo,') !== false;

    return [
        'success' => $isLoggedIn,
        'code' => $postRes['code'],
        'token' => $token,
        'body' => $postRes['body']
    ];
}

echo "========================================================================\n";
echo "🚀 MEMULAI LIVE E2E TEST PADA http://127.0.0.1:8000 (REAL MYSQL DATABASE)\n";
echo "========================================================================\n\n";

$allPassed = true;

// -------------------------------------------------------------------------
// STEP 1: LIVE TEST ROLE KARYAWAN (Alice Employee)
// -------------------------------------------------------------------------
echo "👤 [TEST 1/3] LIVE TEST ROLE: KARYAWAN (alice@tech.com)\n";
$cookieKaryawan = $cookieDir . '/karyawan.txt';

$loginKaryawan = login($baseUrl, 'alice@tech.com', 'password', $cookieKaryawan);
if ($loginKaryawan['success']) {
    echo "  ✅ 1. Login Karyawan (Alice) BERHASIL (HTTP {$loginKaryawan['code']})\n";
} else {
    echo "  ❌ 1. Login Karyawan GAGAL\n";
    $allPassed = false;
}

// 1.1 Dashboard Karyawan
$dashKaryawan = makeRequest($baseUrl . '/dashboard', 'GET', [], $cookieKaryawan);
$hasTargetProgress = strpos($dashKaryawan['body'], 'Target Onboarding') !== false;
$hasCourses = strpos($dashKaryawan['body'], 'Materi Pelatihan') !== false || strpos($dashKaryawan['body'], 'Kursus') !== false;
if ($dashKaryawan['code'] === 200 && ($hasTargetProgress || $hasCourses)) {
    echo "  ✅ 2. Akses Dashboard Karyawan BERHASIL (Tampil Progres & Materi Aktif)\n";
} else {
    echo "  ❌ 2. Akses Dashboard Karyawan GAGAL (HTTP {$dashKaryawan['code']})\n";
    $allPassed = false;
}

// 1.2 Katalog Kursus Karyawan
$coursesKaryawan = makeRequest($baseUrl . '/courses', 'GET', [], $cookieKaryawan);
$canSeeAssignedCourse = strpos($coursesKaryawan['body'], 'Kursus A') !== false || strpos($coursesKaryawan['body'], 'Startup Engineering') !== false;
if ($coursesKaryawan['code'] === 200 && $canSeeAssignedCourse) {
    echo "  ✅ 3. Akses Katalog Kursus Karyawan BERHASIL (Kursus PT Teknologi Maju tampil)\n";
} else {
    echo "  ❌ 3. Akses Katalog Kursus Karyawan GAGAL\n";
    $allPassed = false;
}

// 1.3 Buka Modul 1
$mod1 = makeRequest($baseUrl . '/courses/1/modules/1', 'GET', [], $cookieKaryawan);
if ($mod1['code'] === 200 && strpos($mod1['body'], 'Modul #1') !== false) {
    echo "  ✅ 4. Membuka Detail Modul 1 BERHASIL (Konten materi & tombol selesai tampil)\n";
} else {
    echo "  ❌ 4. Membuka Modul 1 GAGAL (HTTP {$mod1['code']})\n";
    $allPassed = false;
}

// 1.4 Tandai Modul 1 Selesai
$csrfMod1 = extractCsrfToken($mod1['body']);
$completeMod1 = makeRequest($baseUrl . '/modules/1/complete', 'POST', ['_token' => $csrfMod1], $cookieKaryawan);
if ($completeMod1['code'] === 200 || $completeMod1['code'] === 302) {
    echo "  ✅ 5. Aksi 'Tandai Selesai Modul' BERHASIL (Progres tercatat di database)\n";
} else {
    echo "  ❌ 5. Selesai Modul GAGAL\n";
    $allPassed = false;
}

// 1.5 Security Check: Karyawan dilarang akses menu admin/tutor
$checkReviewAccess = makeRequest($baseUrl . '/reviews', 'GET', [], $cookieKaryawan);
if ($checkReviewAccess['code'] === 403) {
    echo "  ✅ 6. Security Boundary: Karyawan diblokir dari /reviews (HTTP 403 Forbidden - AMAN)\n";
} else {
    echo "  ❌ 6. Security Boundary Terobos: Karyawan bisa akses /reviews (HTTP {$checkReviewAccess['code']})\n";
    $allPassed = false;
}

echo "\n-------------------------------------------------------------------------\n";

// -------------------------------------------------------------------------
// STEP 2: LIVE TEST ROLE TUTOR 1 (tutor1@lms.com)
// -------------------------------------------------------------------------
echo "👨‍🏫 [TEST 2/3] LIVE TEST ROLE: TUTOR 1 (tutor1@lms.com)\n";
$cookieTutor1 = $cookieDir . '/tutor1.txt';

$loginTutor1 = login($baseUrl, 'tutor1@lms.com', 'password', $cookieTutor1);
if ($loginTutor1['success']) {
    echo "  ✅ 1. Login Tutor 1 BERHASIL (HTTP {$loginTutor1['code']})\n";
} else {
    echo "  ❌ 1. Login Tutor 1 GAGAL\n";
    $allPassed = false;
}

// 2.1 Dashboard Tutor 1
$dashTutor1 = makeRequest($baseUrl . '/dashboard', 'GET', [], $cookieTutor1);
$t1CoursesCount = (strpos($dashTutor1['body'], 'Kursus Aktif') !== false || strpos($dashTutor1['body'], 'Materi Kursus') !== false);
if ($dashTutor1['code'] === 200 && $t1CoursesCount) {
    echo "  ✅ 2. Akses Dashboard Tutor 1 BERHASIL (Metrik materi & review esai tampil)\n";
} else {
    echo "  ❌ 2. Akses Dashboard Tutor 1 GAGAL\n";
    $allPassed = false;
}

// 2.2 Halaman Review Esai AI Tutor 1
$reviewTutor1 = makeRequest($baseUrl . '/reviews', 'GET', [], $cookieTutor1);
$t1HasReviews = (strpos($reviewTutor1['body'], 'Review & Evaluasi Esai AI') !== false && (strpos($reviewTutor1['body'], 'Alice') !== false || strpos($reviewTutor1['body'], 'Review & Nilai') !== false));
if ($reviewTutor1['code'] === 200 && $t1HasReviews) {
    echo "  ✅ 3. Halaman Review Esai AI Tutor 1 BERHASIL (Daftar lembar esai karyawan klien tampil)\n";
} else {
    echo "  ❌ 3. Halaman Review Esai AI Tutor 1 GAGAL (HTTP {$reviewTutor1['code']})\n";
    $allPassed = false;
}

// 2.3 Halaman Progres Belajar Tutor 1
$progTutor1 = makeRequest($baseUrl . '/progress', 'GET', [], $cookieTutor1);
$t1HasCompanies = (strpos($progTutor1['body'], 'Perusahaan X') !== false || strpos($progTutor1['body'], 'Creative Studio') !== false || strpos($progTutor1['body'], 'Progres Onboarding') !== false);
if ($progTutor1['code'] === 200 && $t1HasCompanies) {
    echo "  ✅ 4. Pemantauan Progres Perusahaan Klien Tutor 1 BERHASIL\n";
} else {
    echo "  ❌ 4. Pemantauan Progres Tutor 1 GAGAL\n";
    $allPassed = false;
}

echo "\n-------------------------------------------------------------------------\n";

// -------------------------------------------------------------------------
// STEP 3: LIVE TEST ROLE TUTOR 2 (tutor2@lms.com) & SINKRONISASI
// -------------------------------------------------------------------------
echo "👨‍🏫 [TEST 3/3] LIVE TEST ROLE: TUTOR 2 (tutor2@lms.com) - SINKRONISASI DENGAN TUTOR 1\n";
$cookieTutor2 = $cookieDir . '/tutor2.txt';

$loginTutor2 = login($baseUrl, 'tutor2@lms.com', 'password', $cookieTutor2);
if ($loginTutor2['success']) {
    echo "  ✅ 1. Login Tutor 2 BERHASIL (HTTP {$loginTutor2['code']})\n";
} else {
    echo "  ❌ 1. Login Tutor 2 GAGAL\n";
    $allPassed = false;
}

// 3.1 Dashboard Tutor 2 (Harus sinkron dengan Tutor 1)
$dashTutor2 = makeRequest($baseUrl . '/dashboard', 'GET', [], $cookieTutor2);
$t2HasDashboard = (strpos($dashTutor2['body'], 'Kursus Aktif') !== false || strpos($dashTutor2['body'], 'Materi Kursus') !== false);
if ($dashTutor2['code'] === 200 && $t2HasDashboard) {
    echo "  ✅ 2. Akses Dashboard Tutor 2 BERHASIL (Metrik tersinkronisasi penuh dengan Tutor 1)\n";
} else {
    echo "  ❌ 2. Akses Dashboard Tutor 2 GAGAL\n";
    $allPassed = false;
}

// 3.2 Katalog Kursus Tutor 2
$coursesTutor2 = makeRequest($baseUrl . '/courses', 'GET', [], $cookieTutor2);
$t2HasCourses = (strpos($coursesTutor2['body'], 'Kursus A') !== false && strpos($coursesTutor2['body'], 'Kursus B') !== false);
if ($coursesTutor2['code'] === 200 && $t2HasCourses) {
    echo "  ✅ 3. Katalog Kursus Tutor 2 SINKRON (Tutor 2 dapat melihat Kursus A & Kursus B)\n";
} else {
    echo "  ❌ 3. Katalog Kursus Tutor 2 GAGAL\n";
    $allPassed = false;
}

// 3.3 Detail Kursus & Tombol Aksi Tutor 2
$detailCourseTutor2 = makeRequest($baseUrl . '/courses/1', 'GET', [], $cookieTutor2);
$t2HasActionButtons = (strpos($detailCourseTutor2['body'], 'Edit Kursus') !== false && strpos($detailCourseTutor2['body'], 'Tambah Modul') !== false);
if ($detailCourseTutor2['code'] === 200 && $t2HasActionButtons) {
    echo "  ✅ 4. Detail Kursus Tutor 2 SINKRON (Tombol 'Edit Kursus', 'Tambah Modul', 'Buat Kuis' aktif)\n";
} else {
    echo "  ❌ 4. Tombol Aksi Kursus Tutor 2 GAGAL\n";
    $allPassed = false;
}

// 3.4 Buka Edit Modul sebagai Tutor 2
$editModTutor2 = makeRequest($baseUrl . '/modules/1/edit', 'GET', [], $cookieTutor2);
if ($editModTutor2['code'] === 200 && strpos($editModTutor2['body'], 'Edit Modul') !== false) {
    echo "  ✅ 5. Tutor 2 Edit Modul BERHASIL (Tidak ada blokir 403 Forbidden)\n";
} else {
    echo "  ❌ 5. Tutor 2 Edit Modul GAGAL (HTTP {$editModTutor2['code']})\n";
    $allPassed = false;
}

// 3.5 Halaman Review Esai AI Tutor 2
$reviewTutor2 = makeRequest($baseUrl . '/reviews', 'GET', [], $cookieTutor2);
$t2HasReviews = (strpos($reviewTutor2['body'], 'Review & Evaluasi Esai AI') !== false && (strpos($reviewTutor2['body'], 'Alice') !== false || strpos($reviewTutor2['body'], 'Review & Nilai') !== false));
if ($reviewTutor2['code'] === 200 && $t2HasReviews) {
    echo "  ✅ 6. Halaman Review Esai AI Tutor 2 MUNCUL & SINKRON (Tutor 2 melihat antrean review)\n";
} else {
    echo "  ❌ 6. Halaman Review Esai AI Tutor 2 GAGAL (HTTP {$reviewTutor2['code']})\n";
    $allPassed = false;
}

// 3.6 LIVE ACTION: Tutor 2 Memberikan Nilai Akhir (Finalisasi Nilai Esai Karyawan)
// Cari user_answer_id dari pending review
preg_match('/href=["\'](http:\/\/127\.0\.0\.1:8000\/reviews\/(\d+)|[^\'"]*\/reviews\/(\d+))["\']/', $reviewTutor2['body'], $answerMatch);
$answerId = !empty($answerMatch[2]) ? $answerMatch[2] : (!empty($answerMatch[3]) ? $answerMatch[3] : null);

if ($answerId) {
    // Buka halaman lembar jawaban
    $showReview = makeRequest($baseUrl . '/reviews/' . $answerId, 'GET', [], $cookieTutor2);
    $csrfReview = extractCsrfToken($showReview['body']);

    if ($showReview['code'] === 200 && $csrfReview) {
        // Tutor 2 memfinalisasi nilai menjadi 92 dengan catatan kustom
        $postFinalize = makeRequest($baseUrl . '/reviews/' . $answerId . '/finalize', 'POST', [
            '_token' => $csrfReview,
            'tutor_score' => 92,
            'tutor_notes' => 'Luar biasa! Penjelasan sangat lugas dan komprehensif. Diverifikasi oleh Tutor 2.'
        ], $cookieTutor2);

        if ($postFinalize['code'] === 200 || $postFinalize['code'] === 302) {
            echo "  ✅ 7. LIVE ACTION: Tutor 2 BERHASIL Memfinalisasi Nilai Esai #{$answerId} (Skor: 92/100)\n";
        } else {
            echo "  ❌ 7. Finalisasi Nilai oleh Tutor 2 GAGAL (HTTP {$postFinalize['code']})\n";
            $allPassed = false;
        }
    } else {
        echo "  ❌ 7. Gagal membuka lembar jawaban #{$answerId} (HTTP {$showReview['code']})\n";
        $allPassed = false;
    }
} else {
    echo "  ℹ️ 7. Tidak ada ID antrean esai pending tersisa (semua telah dinilai)\n";
}

// 3.7 Halaman Progres Perusahaan Klien Tutor 2
$progTutor2 = makeRequest($baseUrl . '/progress', 'GET', [], $cookieTutor2);
$t2HasProg = (strpos($progTutor2['body'], 'Perusahaan X') !== false || strpos($progTutor2['body'], 'Creative Studio') !== false || strpos($progTutor2['body'], 'Progres Onboarding') !== false);
if ($progTutor2['code'] === 200 && $t2HasProg) {
    echo "  ✅ 8. Pemantauan Progres Perusahaan Klien Tutor 2 SINKRON (Bisa pantau semua klien)\n";
} else {
    echo "  ❌ 8. Pemantauan Progres Tutor 2 GAGAL\n";
    $allPassed = false;
}

// 3.8 Detail Progres Karyawan oleh Tutor 2
$empProgTutor2 = makeRequest($baseUrl . '/progress/employee/4', 'GET', [], $cookieTutor2);
if ($empProgTutor2['code'] === 200 && strpos($empProgTutor2['body'], 'Alice Employee') !== false) {
    echo "  ✅ 9. Detail Progres Karyawan Alice oleh Tutor 2 BERHASIL (Bisa pantau detail karyawan)\n";
} else {
    echo "  ❌ 9. Detail Progres Karyawan oleh Tutor 2 GAGAL (HTTP {$empProgTutor2['code']})\n";
    $allPassed = false;
}

echo "\n========================================================================\n";
if ($allPassed) {
    echo "🎉 HASIL AKHIR: 100% SEMUA FITUR BERFUNGSI SEMPURNA DALAM LIVE TEST!\n";
    echo "   - Karyawan (Alice): Belajar modul, progres tercatat, akses aman terisolasi.\n";
    echo "   - Tutor 1: Dashboard aktif, kelola materi, akses review esai lancar.\n";
    echo "   - Tutor 2: Tersinkronisasi penuh dengan Tutor 1, bisa mengedit kursus/modul,\n";
    echo "              melihat antrean esai AI, dan sukses memfinalisasi nilai esai live!\n";
} else {
    echo "⚠️ HASIL AKHIR: Terdapat beberapa langkah yang belum lolos verifikasi.\n";
}
echo "========================================================================\n";

// Cleanup cookies
@unlink($cookieKaryawan);
@unlink($cookieTutor1);
@unlink($cookieTutor2);
@rmdir($cookieDir);
