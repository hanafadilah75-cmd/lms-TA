<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity log listings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ActivityLog::with('user');

        if ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        } else {
            // Admin & Tutor can filter by user or action
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
        }

        // Filter by Month and Year (for Calendar Rekap)
        $selectedYear = $request->input('year');
        $selectedMonth = $request->input('month');

        if (!empty($selectedYear)) {
            $query->whereYear('created_at', $selectedYear);
        }
        if (!empty($selectedMonth)) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        $allUsers = ($user->isAdmin() || $user->isTutor()) ? User::orderBy('name')->get() : collect();

        return view('activities.index', compact('logs', 'allUsers', 'selectedYear', 'selectedMonth'));
    }

    /**
     * Return JSON calendar recap data for a specific month and year.
     */
    public function calendarRecap(Request $request)
    {
        $user = Auth::user();
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        $query = ActivityLog::whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($user && $user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        $totalActivities = (clone $query)->count();
        $completedModules = (clone $query)->where('action', 'module_completed')->count();
        $submittedQuizzes = (clone $query)->where('action', 'quiz_submitted')->count();
        $activeUsers = (clone $query)->distinct('user_id')->count('user_id');

        // Active day numbers in that month
        $activeDays = (clone $query)->selectRaw('DAY(created_at) as day')
            ->distinct()
            ->pluck('day')
            ->toArray();

        $date = Carbon::createFromDate($year, $month, 1);

        return response()->json([
            'year' => $year,
            'month' => $month,
            'month_name' => $date->format('M'),
            'month_full' => $date->translatedFormat('F'),
            'days_in_month' => $date->daysInMonth,
            'first_day_of_week' => $date->dayOfWeek, // 0 = Sunday, 1 = Monday, etc.
            'total_activities' => $totalActivities,
            'completed_modules' => $completedModules,
            'submitted_quizzes' => $submittedQuizzes,
            'active_users' => $activeUsers,
            'active_days' => $activeDays,
        ]);
    }
}
