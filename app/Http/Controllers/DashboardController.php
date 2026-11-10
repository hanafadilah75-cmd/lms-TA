<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Course;
use App\Models\UserAnswer;
use App\Models\UserProgress;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Render role-based dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTutor()) {
            return $this->tutorDashboard($user);
        } else {
            return $this->employeeDashboard($user);
        }
    }

    /**
     * Admin dashboard.
     */
    private function adminDashboard()
    {
        $totalCompanies = Company::count();
        $totalEmployees = User::where('role', 'karyawan')->count();
        $totalCourses = Course::count();

        // Calculate average completion rate of all employees
        $employees = User::where('role', 'karyawan')->get();
        $completionSum = 0;
        $activeEmployees = 0;

        foreach ($employees as $employee) {
            $status = $this->calculateEmployeeAverageProgress($employee->id);
            $completionSum += $status;
            $activeEmployees++;
        }

        $avgCompletionRate = $activeEmployees > 0 ? round($completionSum / $activeEmployees, 2) : 0;

        // Compile company progress data for chart
        $companies = Company::all();
        $chartData = [];

        foreach ($companies as $comp) {
            $compEmployees = User::where('company_id', $comp->id)->where('role', 'karyawan')->get();
            $compSum = 0;
            $compCount = $compEmployees->count();

            foreach ($compEmployees as $employee) {
                $compSum += $this->calculateEmployeeAverageProgress($employee->id);
            }

            $chartData[] = [
                'name' => $comp->name,
                'rate' => $compCount > 0 ? round($compSum / $compCount, 2) : 0
            ];
        }

        // Grouped course-client progress tracking ("Kursus A untuk Perusahaan X", etc.)
        $allCourses = Course::with(['companies', 'modules', 'tutor'])->get();
        $courseData = $this->buildCompanyCourseProgressData($allCourses);

        // Recent activity logs for timeline
        $recentActivities = ActivityLog::with('user')->latest()->take(6)->get();

        return view('dashboard.admin', compact(
            'totalCompanies',
            'totalEmployees',
            'totalCourses',
            'avgCompletionRate',
            'chartData',
            'courseData',
            'recentActivities'
        ));
    }

    /**
     * Tutor dashboard.
     */
    private function tutorDashboard($user)
    {
        // Get all courses accessible to tutors
        $courses = Course::with(['companies', 'modules', 'tutor'])->get();
        $coursesCount = $courses->count();

        // Grouped course-client progress tracking
        $courseData = $this->buildCompanyCourseProgressData($courses);

        // Pending reviews count across all courses
        $pendingReviewsCount = UserAnswer::whereHas('question', function ($q) {
            $q->where('type', 'essay');
        })
        ->whereNull('final_score')
        ->count();

        // Quiz score statistics across all courses
        $allAnswers = UserAnswer::whereHas('question.quiz.course')->get();
        $totalAnswersCount = $allAnswers->count();
        $totalScoreSum = 0;

        foreach ($allAnswers as $ans) {
            $totalScoreSum += ($ans->final_score ?? $ans->ai_score ?? 0);
        }

        $avgScore = $totalAnswersCount > 0 ? round($totalScoreSum / $totalAnswersCount, 1) : 0;

        // Recent activity logs
        $recentActivities = ActivityLog::with('user')->latest()->take(6)->get();

        return view('dashboard.tutor', compact(
            'courseData',
            'coursesCount',
            'pendingReviewsCount',
            'avgScore',
            'recentActivities'
        ));
    }

    /**
     * Employee dashboard.
     */
    private function employeeDashboard($user)
    {
        // Only courses assigned to the employee's client company
        $courses = Course::whereHas('companies', function ($q) use ($user) {
            $q->where('companies.id', $user->company_id);
        })->with(['modules', 'tutor'])->get();

        $activeCourses = [];
        $latestScores = [];
        $totalProgressSum = 0;
        $coursesCount = $courses->count();

        foreach ($courses as $course) {
            $modules = $course->modules;
            $moduleCount = $modules->count();
            $totalPagesInCourse = 0;
            $totalPagesRead = 0;
            $completedModulesCount = 0;

            $progressMap = DB::table('user_progress')
                ->where('user_id', $user->id)
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
                        $completedModulesCount++;
                    } else {
                        $totalPagesRead += max(0, min((int) ($up->current_page ?? 1), $pCount));
                    }
                }
            }

            $progress = $totalPagesInCourse > 0 ? round(($totalPagesRead / $totalPagesInCourse) * 100, 1) : 0;
            $totalProgressSum += $progress;

            $activeCourses[] = [
                'course' => $course,
                'progress' => $progress,
                'completed' => $completedModulesCount,
                'total' => $moduleCount,
                'pages_read' => $totalPagesRead,
                'total_pages' => $totalPagesInCourse,
            ];
        }

        $overallProgressPercent = $coursesCount > 0 ? round($totalProgressSum / $coursesCount, 1) : 0;

        // Get latest answers
        $quizAnswers = UserAnswer::where('user_id', $user->id)
            ->with(['question.quiz.course'])
            ->latest()
            ->get()
            ->groupBy(function ($ans) {
                return $ans->question->quiz_id;
            })
            ->take(5);

        foreach ($quizAnswers as $quizId => $answers) {
            $quiz = $answers->first()->question->quiz;
            $sumScore = 0;
            $pending = false;
            $hasEssay = false;
            foreach ($answers as $ans) {
                if ($ans->question->type === 'essay') {
                    $hasEssay = true;
                    if ($ans->final_score === null) {
                        $pending = true;
                    }
                }
                
                if ($ans->question->type === 'essay' && $ans->final_score === null) {
                    $sumScore += $ans->ai_score;
                } else {
                    $sumScore += $ans->final_score;
                }
            }
            $latestScores[] = [
                'quiz_title' => $quiz->title,
                'course_title' => $quiz->course->title,
                'score' => round($sumScore / $answers->count(), 1),
                'passing_grade' => $quiz->passing_grade,
                'pending' => $pending,
                'has_essay' => $hasEssay,
                'date' => $answers->first()->created_at->format('d M Y H:i'),
            ];
        }

        // Recent activity logs of this employee
        $recentActivities = ActivityLog::where('user_id', $user->id)->latest()->take(5)->get();

        return view('dashboard.karyawan', compact(
            'activeCourses',
            'latestScores',
            'overallProgressPercent',
            'recentActivities'
        ));
    }

    /**
     * Helper progress calculator based on pages.
     */
    private function calculateEmployeeAverageProgress($employeeId)
    {
        $employee = User::find($employeeId);
        if (!$employee) return 0;
        $companyId = $employee->company_id;
        if (!$companyId) return 0;

        $courses = Course::whereHas('companies', function ($q) use ($companyId) {
            $q->where('companies.id', $companyId);
        })->with('modules')->get();

        if ($courses->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;
        $totalCourses = $courses->count();

        foreach ($courses as $course) {
            $modules = $course->modules;
            if ($modules->isEmpty()) {
                $totalCourses--;
                continue;
            }

            $totalPagesInCourse = 0;
            $totalPagesRead = 0;

            $progresses = DB::table('user_progress')
                ->where('user_id', $employeeId)
                ->whereIn('module_id', $modules->pluck('id'))
                ->get()
                ->keyBy('module_id');

            foreach ($modules as $mod) {
                $pCount = max(1, (int) ($mod->total_pages ?? 1));
                $totalPagesInCourse += $pCount;

                if (isset($progresses[$mod->id])) {
                    $p = $progresses[$mod->id];
                    if ($p->status === 'completed') {
                        $totalPagesRead += $pCount;
                    } else {
                        $totalPagesRead += max(0, min((int) ($p->current_page ?? 1), $pCount));
                    }
                }
            }

            $totalProgress += ($totalPagesInCourse > 0 ? ($totalPagesRead / $totalPagesInCourse) * 100 : 0);
        }

        return $totalCourses > 0 ? round($totalProgress / $totalCourses, 1) : 0;
    }

    /**
     * Build grouped course-company progress tracking data.
     */
    private function buildCompanyCourseProgressData($courses)
    {
        $courseData = [];

        foreach ($courses as $course) {
            $modules = $course->modules;
            $moduleCount = $modules->count();

            foreach ($course->companies as $company) {
                $employees = User::where('company_id', $company->id)
                    ->where('role', 'karyawan')
                    ->get();
                $employeeCount = $employees->count();

                $employeeDetails = [];
                $totalProgress = 0;
                $completedCount = 0;
                $inProgressCount = 0;
                $notStartedCount = 0;

                foreach ($employees as $emp) {
                    $totalPagesInCourse = 0;
                    $totalPagesRead = 0;
                    $completedModules = 0;

                    $empProgresses = DB::table('user_progress')
                        ->where('user_id', $emp->id)
                        ->whereIn('module_id', $modules->pluck('id'))
                        ->get()
                        ->keyBy('module_id');

                    foreach ($modules as $mod) {
                        $pCount = max(1, (int) ($mod->total_pages ?? 1));
                        $totalPagesInCourse += $pCount;

                        if (isset($empProgresses[$mod->id])) {
                            $ep = $empProgresses[$mod->id];
                            if ($ep->status === 'completed') {
                                $totalPagesRead += $pCount;
                                $completedModules++;
                            } else {
                                $totalPagesRead += max(0, min((int) ($ep->current_page ?? 1), $pCount));
                            }
                        }
                    }

                    $empPercent = $totalPagesInCourse > 0 ? round(($totalPagesRead / $totalPagesInCourse) * 100, 1) : 0;
                    $totalProgress += $empPercent;

                    if ($empPercent >= 100) {
                        $completedCount++;
                        $empStatus = 'selesai';
                    } elseif ($empPercent > 0) {
                        $inProgressCount++;
                        $empStatus = 'sedang_belajar';
                    } else {
                        $notStartedCount++;
                        $empStatus = 'belum_mulai';
                    }

                    $employeeDetails[] = [
                        'id' => $emp->id,
                        'name' => $emp->name,
                        'email' => $emp->email,
                        'completed_modules' => $completedModules,
                        'total_modules' => $moduleCount,
                        'pages_read' => $totalPagesRead,
                        'total_pages' => $totalPagesInCourse,
                        'percent' => $empPercent,
                        'status' => $empStatus,
                    ];
                }

                // Calculate average progress
                $avgProgress = $employeeCount > 0 ? round($totalProgress / $employeeCount, 1) : 0;

                // Status classification
                $status = 'belum_mulai';
                if ($employeeCount > 0) {
                    if ($completedCount === $employeeCount) {
                        $status = 'selesai';
                    } elseif ($totalProgress > 0) {
                        $status = 'bervariasi';
                    }
                }

                $courseData[] = [
                    'course' => $course,
                    'company' => $company,
                    'label' => "{$course->title} untuk {$company->name}",
                    'employees_count' => $employeeCount,
                    'module_count' => $moduleCount,
                    'average_progress' => $avgProgress,
                    'completed_count' => $completedCount,
                    'in_progress_count' => $inProgressCount,
                    'not_started_count' => $notStartedCount,
                    'status' => $status,
                    'employee_details' => $employeeDetails,
                ];
            }
        }

        return collect($courseData);
    }
}
