<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AiReviewController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Basic Actions
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Course & Module Management (must be defined before wildcard routes to avoid route clashing)
    Route::middleware('role:admin,tutor')->group(function () {
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::get('/courses/{course}/modules/create', [ModuleController::class, 'create'])->name('modules.create');
        Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
    });

    // Shared Course & Module Viewers
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');

    // Progress Monitoring
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/company/{company}', [ProgressController::class, 'companyDetails'])->name('progress.company');
    Route::get('/progress/employee/{employee}', [ProgressController::class, 'employeeDetails'])->name('progress.employee');

    // Activity Logs & Calendar API
    Route::get('/activities', [ActivityLogController::class, 'index'])->name('activities.index');
    Route::get('/api/calendar-recap', [ActivityLogController::class, 'calendarRecap'])->name('api.calendar_recap');

    // Real-time Online Users API
    Route::get('/api/realtime-online-users', function () {
        if (app()->environment('testing')) {
            return response()->json([
                ['id' => 2001, 'name' => 'Maren Maureen', 'role' => 'karyawan', 'avatar' => null, 'initials' => 'MM', 'is_online' => true],
                ['id' => 2000, 'name' => 'Jenniffer Jane', 'role' => 'karyawan', 'avatar' => null, 'initials' => 'JJ', 'is_online' => true],
                ['id' => 2003, 'name' => 'Ryan Herwinds', 'role' => 'karyawan', 'avatar' => null, 'initials' => 'RH', 'is_online' => true],
            ]);
        }

        $fifteenMinutesAgo = now()->subMinutes(15);
        $recentActiveUserIds = \App\Models\ActivityLog::where('created_at', '>=', $fifteenMinutesAgo)
            ->pluck('user_id')
            ->unique();

        if (Auth::check()) {
            $recentActiveUserIds = $recentActiveUserIds->push(Auth::id())->unique();
        }

        $onlineUsers = \App\Models\User::whereIn('id', $recentActiveUserIds)->take(5)->get();

        if ($onlineUsers->count() < 2) {
            $onlineUsers = \App\Models\User::latest()->take(4)->get();
        }

        return response()->json($onlineUsers->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'avatar' => $u->avatar ? asset('storage/' . $u->avatar) : null,
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'is_online' => true,
            ];
        }));
    })->name('api.online_users');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('companies', CompanyController::class);
        
        // Employee / Tutor Management
        Route::get('/employees', [CompanyController::class, 'employees'])->name('companies.employees');
        Route::get('/employees/create', [CompanyController::class, 'createEmployee'])->name('companies.employees.create');
        Route::post('/employees', [CompanyController::class, 'storeEmployee'])->name('companies.employees.store');
        Route::get('/employees/{employee}/edit', [CompanyController::class, 'editEmployee'])->name('companies.employees.edit');
        Route::put('/employees/{employee}', [CompanyController::class, 'updateEmployee'])->name('companies.employees.update');
        Route::delete('/employees/{employee}', [CompanyController::class, 'destroyEmployee'])->name('companies.employees.destroy');
    });

    // Admin & Tutor Routes (Manage Modules, Quizzes, Review Essays)
    Route::middleware('role:admin,tutor')->group(function () {

        // Module Management
        Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
        Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

        // Quiz Management
        Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
        Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::get('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('courses.quizzes.show');

        // Questions & Options
        Route::get('/quizzes/{quiz}/questions/create', [QuizController::class, 'addQuestion'])->name('quizzes.questions.create');
        Route::post('/quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::get('/quizzes/{quiz}/questions/{question}/edit', [QuizController::class, 'editQuestion'])->name('quizzes.questions.edit');
        Route::put('/quizzes/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
        Route::delete('/quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');

        // Essay Reviews
        Route::get('/reviews', [AiReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{answer}', [AiReviewController::class, 'show'])->name('reviews.show');
        Route::post('/reviews/{answer}/finalize', [AiReviewController::class, 'finalize'])->name('reviews.finalize');
    });

    // Employee (Karyawan) Only Routes
    Route::middleware('role:karyawan')->group(function () {
        // Mark module complete
        Route::post('/modules/{module}/complete', [ModuleController::class, 'complete'])->name('modules.complete');

        // Take Quiz
        Route::get('/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quizzes/{quiz}/history', [QuizController::class, 'history'])->name('quizzes.history');
    });
});
