<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Company;
use App\Models\ActivityLog;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with filtering.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isTutor()) {
            $query = Course::with(['tutor', 'companies'])->withCount('modules');
        } else {
            // Employee role: can access courses assigned to their company
            $query = Course::whereHas('companies', function ($q) use ($user) {
                $q->where('companies.id', $user->company_id);
            })->with(['tutor', 'companies'])->withCount('modules');
        }

        // Filter by Level
        $selectedLevel = $request->input('level');
        if (!empty($selectedLevel) && $selectedLevel !== 'all') {
            $query->where('level', $selectedLevel);
        }

        // Filter by Search Query
        $searchQuery = $request->input('search');
        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        // Filter by Type / Category (e.g. A vs B or all)
        $selectedType = $request->input('type');
        if (!empty($selectedType) && $selectedType !== 'all') {
            if ($selectedType === 'a') {
                $query->where('title', 'like', '%kursus a%');
            } elseif ($selectedType === 'b') {
                $query->where('title', 'like', '%kursus b%');
            }
        }

        // Sort by Time / Name
        $selectedSort = $request->input('sort', 'latest');
        if ($selectedSort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($selectedSort === 'title_asc') {
            $query->orderBy('title', 'asc');
        } elseif ($selectedSort === 'title_desc') {
            $query->orderBy('title', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $courses = $query->paginate(10)->withQueryString();

        return view('courses.index', compact('courses', 'selectedLevel', 'selectedSort', 'searchQuery', 'selectedType'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $this->authorizeAccess();

        $tutors = User::where('role', 'tutor')->get();
        $companies = Company::where('type', 'client')->orderBy('name')->get();
        return view('courses.create', compact('tutors', 'companies'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $this->authorizeAccess();

        DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            } else {
                $data['thumbnail'] = null;
            }

            // For tutor creating a course, enforce themselves as tutor
            if (Auth::user()->isTutor() || empty($data['tutor_id'])) {
                $data['tutor_id'] = Auth::id();
            }

            $course = Course::create($data);

            if ($request->filled('companies')) {
                $course->companies()->sync($request->input('companies'));
            }

            ActivityLog::record('course_created', "Kursus baru '{$course->title}' berhasil dibuat.");
        });

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil ditambahkan.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $user = Auth::user();

        // Check employee access restriction
        if ($user->isKaryawan()) {
            if (!$course->companies()->where('companies.id', $user->company_id)->exists()) {
                abort(403, 'Akses ditolak. Kursus ini tidak tersedia untuk perusahaan Anda.');
            }
        }

        $course->load(['tutor', 'modules', 'quizzes', 'companies']);

        // For employees, also load completed module IDs to show progress
        $completedModuleIds = [];
        if ($user->isKaryawan()) {
            $completedModuleIds = DB::table('user_progress')
                ->where('user_id', $user->id)
                ->whereIn('module_id', $course->modules->pluck('id'))
                ->pluck('module_id')
                ->toArray();
        }

        return view('courses.show', compact('course', 'completedModuleIds'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $this->authorizeAccess();

        $tutors = User::where('role', 'tutor')->get();
        $companies = Company::where('type', 'client')->orderBy('name')->get();
        $course->load('companies');

        return view('courses.edit', compact('course', 'tutors', 'companies'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorizeAccess();

        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            } else {
                unset($data['thumbnail']); // Keep existing thumbnail if not uploading a new one
            }

            if (Auth::user()->isTutor() || empty($data['tutor_id'])) {
                $data['tutor_id'] = $course->tutor_id ?? Auth::id();
            }

            $course->update($data);

            if ($request->has('companies')) {
                $course->companies()->sync($request->input('companies', []));
            } else {
                $course->companies()->detach();
            }

            ActivityLog::record('course_updated', "Kursus '{$course->title}' berhasil diperbarui.");
        });

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $this->authorizeAccess();

        DB::transaction(function () use ($course) {
            ActivityLog::record('course_deleted', "Kursus '{$course->title}' berhasil dihapus.");
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $course->delete();
        });

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * Internal authorization helper.
     */
    private function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang.');
        }
    }
}
