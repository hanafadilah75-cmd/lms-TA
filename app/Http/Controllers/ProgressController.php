<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display general progress overview.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isKaryawan()) {
            return $this->employeeProgress($user->id);
        }

        // Admin & Tutor view
        $companies = Company::where('type', 'client')->withCount('users')->get();

        // Calculate progress stats per company
        $companyStats = [];
        foreach ($companies as $company) {
            $employees = User::where('company_id', $company->id)->where('role', 'karyawan')->get();
            $employeeCount = $employees->count();

            $totalProgressPercent = 0;
            $completedCount = 0;
            $inProgressCount = 0;
            $notStartedCount = 0;

            foreach ($employees as $employee) {
                $statusData = $this->calculateEmployeeOverallStatus($employee);
                $totalProgressPercent += $statusData['average_progress'];

                if ($statusData['status'] === 'selesai') {
                    $completedCount++;
                } elseif ($statusData['status'] === 'sedang_belajar') {
                    $inProgressCount++;
                } else {
                    $notStartedCount++;
                }
            }

            $companyStats[$company->id] = [
                'company' => $company,
                'employee_count' => $employeeCount,
                'avg_completion_rate' => $employeeCount > 0 ? round($totalProgressPercent / $employeeCount, 1) : 0,
                'completed_count' => $completedCount,
                'in_progress_count' => $inProgressCount,
                'not_started_count' => $notStartedCount,
            ];
        }

        return view('progress.index', compact('companyStats'));
    }

    /**
     * Show detailed progress for a specific company's employees.
     */
    public function companyDetails(Company $company)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Akses ditolak.');
        }

        $employees = User::where('company_id', $company->id)
            ->where('role', 'karyawan')
            ->paginate(10);

        $employeeProgressData = [];
        foreach ($employees as $employee) {
            $employeeProgressData[$employee->id] = $this->calculateEmployeeOverallStatus($employee);
        }

        return view('progress.company', compact('company', 'employees', 'employeeProgressData'));
    }

    /**
     * Show detailed progress for a specific employee.
     */
    public function employeeDetails(User $employee)
    {
        $user = Auth::user();

        if ($user->isKaryawan() && $user->id !== $employee->id) {
            abort(403, 'Akses ditolak.');
        }

        return $this->employeeProgress($employee->id);
    }

    /**
     * Helper to render employee progress detail.
     */
    private function employeeProgress(int $employeeId)
    {
        $employee = User::with('company')->findOrFail($employeeId);

        // Fetch courses for employee's client company
        $coursesQuery = Course::with(['modules', 'quizzes.questions']);
        if ($employee->company_id) {
            $coursesQuery->whereHas('companies', function ($q) use ($employee) {
                $q->where('companies.id', $employee->company_id);
            });
        }
        $courses = $coursesQuery->get();

        $courseProgress = [];
        foreach ($courses as $course) {
            $modules = $course->modules;
            $totalModules = $modules->count();
            $totalPagesInCourse = 0;
            $totalPagesRead = 0;
            $completedModules = 0;

            $progressMap = DB::table('user_progress')
                ->where('user_id', $employeeId)
                ->whereIn('module_id', $modules->pluck('id'))
                ->get()
                ->keyBy('module_id');

            foreach ($modules as $mod) {
                $pCount = max(1, (int) ($mod->total_pages ?? 1));
                $totalPagesInCourse += $pCount;

                if (isset($progressMap[$mod->id])) {
                    $up = $progressMap[$mod->id];
                    if ($up->status === 'completed') {
                        $totalPagesRead += $pCount;
                        $completedModules++;
                    } else {
                        $totalPagesRead += max(0, min((int) ($up->current_page ?? 1), $pCount));
                    }
                }
            }

            $progressPercent = $totalPagesInCourse > 0 ? round(($totalPagesRead / $totalPagesInCourse) * 100, 1) : 0;

            // Get quiz status & score
            $quizIds = $course->quizzes->pluck('id');
            $quizzesCount = $quizIds->count();
            $quizzesPassed = 0;
            $quizScores = [];

            foreach ($course->quizzes as $quiz) {
                $questionIds = $quiz->questions->pluck('id');
                $answers = UserAnswer::where('user_id', $employeeId)
                    ->whereIn('question_id', $questionIds)
                    ->get();

                if ($answers->isNotEmpty() && $quiz->questions->count() > 0 && $answers->count() === $quiz->questions->count()) {
                    $sumScore = 0;
                    $pendingTutor = false;
                    $hasEssay = false;
                    foreach ($answers as $ans) {
                        if ($ans->question->type === 'essay') {
                            $hasEssay = true;
                            if ($ans->final_score === null) {
                                $pendingTutor = true;
                            }
                        }
                        
                        if ($ans->question->type === 'essay' && $ans->final_score === null) {
                            $sumScore += $ans->ai_score;
                        } else {
                            $sumScore += $ans->final_score;
                        }
                    }
                    $quizAvg = round($sumScore / $answers->count(), 1);
                    $quizScores[] = [
                        'quiz_title' => $quiz->title,
                        'score' => $quizAvg,
                        'passing_grade' => $quiz->passing_grade,
                        'has_essay' => $hasEssay,
                        'pending' => $pendingTutor,
                        'status' => $pendingTutor ? 'Menunggu Review Tutor' : ($quizAvg >= $quiz->passing_grade ? 'Lulus' : 'Tidak Lulus'),
                    ];

                    if (!$pendingTutor && $quizAvg >= $quiz->passing_grade) {
                        $quizzesPassed++;
                    }
                } else {
                    $quizScores[] = [
                        'quiz_title' => $quiz->title,
                        'score' => '-',
                        'passing_grade' => $quiz->passing_grade,
                        'has_essay' => false,
                        'pending' => false,
                        'status' => 'Belum Dikerjakan',
                    ];
                }
            }

            // Determine status for this specific course
            $status = 'belum_mulai';
            if ($totalPagesRead > 0) {
                if ($totalPagesRead >= $totalPagesInCourse && ($quizzesCount === 0 || $quizzesPassed === $quizzesCount)) {
                    $status = 'selesai';
                } else {
                    $status = 'sedang_belajar';
                }
            }

            $courseProgress[] = [
                'course' => $course,
                'completed_modules' => $completedModules,
                'total_modules' => $totalModules,
                'pages_read' => $totalPagesRead,
                'total_pages' => $totalPagesInCourse,
                'progress_percent' => $progressPercent,
                'quizzes' => $quizScores,
                'status' => $status
            ];
        }

        return view('progress.employee', compact('employee', 'courseProgress'));
    }

    /**
     * Calculate employee overall progress based on pages.
     */
    private function calculateEmployeeOverallStatus(User $employee)
    {
        $coursesQuery = Course::with('modules');
        if ($employee->company_id) {
            $coursesQuery->whereHas('companies', function ($q) use ($employee) {
                $q->where('companies.id', $employee->company_id);
            });
        }
        $courses = $coursesQuery->get();

        if ($courses->isEmpty()) {
            return [
                'average_progress' => 0,
                'status' => 'belum_mulai'
            ];
        }

        $totalProgress = 0;
        $totalCourses = $courses->count();
        $completedCourses = 0;
        $startedCourses = 0;

        foreach ($courses as $course) {
            $modules = $course->modules;
            if ($modules->isEmpty()) {
                $totalCourses--;
                continue;
            }

            $totalPagesInCourse = 0;
            $totalPagesRead = 0;

            $progressMap = DB::table('user_progress')
                ->where('user_id', $employee->id)
                ->whereIn('module_id', $modules->pluck('id'))
                ->get()
                ->keyBy('module_id');

            foreach ($modules as $mod) {
                $pCount = max(1, (int) ($mod->total_pages ?? 1));
                $totalPagesInCourse += $pCount;

                if (isset($progressMap[$mod->id])) {
                    $up = $progressMap[$mod->id];
                    if ($up->status === 'completed') {
                        $totalPagesRead += $pCount;
                    } else {
                        $totalPagesRead += max(0, min((int) ($up->current_page ?? 1), $pCount));
                    }
                }
            }

            $progressPercent = $totalPagesInCourse > 0 ? ($totalPagesRead / $totalPagesInCourse) * 100 : 0;
            $totalProgress += $progressPercent;

            if ($totalPagesRead > 0) {
                $startedCourses++;
                if ($totalPagesRead >= $totalPagesInCourse) {
                    $completedCourses++;
                }
            }
        }

        $averageProgress = $totalCourses > 0 ? round($totalProgress / $totalCourses, 1) : 0;

        $status = 'belum_mulai';
        if ($startedCourses > 0) {
            if ($completedCourses === $totalCourses) {
                $status = 'selesai';
            } else {
                $status = 'sedang_belajar';
            }
        }

        return [
            'average_progress' => $averageProgress,
            'status' => $status
        ];
    }
}
