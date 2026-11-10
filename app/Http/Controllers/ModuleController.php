<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\UserProgress;
use App\Models\ActivityLog;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Show the form for creating a new module.
     */
    public function create(Course $course)
    {
        $this->authorizeTutorForCourse($course);
        return view('modules.create', compact('course'));
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(StoreModuleRequest $request, Course $course)
    {
        $this->authorizeTutorForCourse($course);

        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $data['total_pages'] = max(1, (int) ($data['total_pages'] ?? 1));

            $module = Module::create($data);
            ActivityLog::record('module_created', "Modul baru '{$module->title}' ditambahkan pada kursus '{$course->title}'.");
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Modul berhasil ditambahkan.');
    }

    /**
     * Display the specified module.
     */
    public function show(Request $request, Course $course, Module $module)
    {
        $user = Auth::user();

        // Enforce employee company assignment restriction
        if ($user->isKaryawan()) {
            if (!$course->companies()->where('companies.id', $user->company_id)->exists()) {
                abort(403, 'Akses ditolak. Modul ini tidak tersedia untuk perusahaan Anda.');
            }
        }

        // Ensure module belongs to course
        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $totalPages = max(1, (int) $module->total_pages);
        $currentPage = max(1, min((int) $request->input('page', 1), $totalPages));

        // Format YouTube url to embed url
        $embedUrl = $this->getYoutubeEmbedUrl($module->video_url);

        // Check or update employee progress
        $isCompleted = false;
        $userProgress = null;

        if ($user->isKaryawan()) {
            $userProgress = UserProgress::firstOrNew([
                'user_id' => $user->id,
                'module_id' => $module->id,
            ]);

            $isCompleted = ($userProgress->status === 'completed');

            // Automatically record page reading progress
            $highestPage = max($userProgress->current_page ?? 1, $currentPage);
            $userProgress->current_page = $highestPage;

            if ($highestPage >= $totalPages && !$isCompleted) {
                $userProgress->status = 'completed';
                $userProgress->completed_at = now();
                $isCompleted = true;
                ActivityLog::record('module_completed', "Karyawan '{$user->name}' menyelesaikan semua {$totalPages} halaman modul '{$module->title}'.");
            } else {
                if (!$userProgress->exists || $currentPage > ($userProgress->getOriginal('current_page') ?? 0)) {
                    ActivityLog::record('page_read', "Karyawan '{$user->name}' membaca halaman {$currentPage} dari {$totalPages} pada modul '{$module->title}'.");
                }
            }
            $userProgress->save();
        }

        // Split content if pagebreak markers exist
        $rawPages = preg_split('/<!--\s*pagebreak\s*-->|\[pagebreak\]/i', (string) $module->content);
        if (count($rawPages) > 1) {
            $totalPages = count($rawPages);
            $displayContent = $rawPages[$currentPage - 1] ?? $module->content;
        } else {
            $displayContent = $module->content;
        }

        // Calculate page completion percentage for this module
        $highestRead = $userProgress ? ($userProgress->status === 'completed' ? $totalPages : max(1, $userProgress->current_page)) : 1;
        $modulePagePercent = round(($highestRead / $totalPages) * 100, 1);

        // Adjacent modules navigation
        $previousModule = Module::where('course_id', $course->id)
            ->where('order_number', '<', $module->order_number)
            ->orderBy('order_number', 'desc')
            ->first();

        $nextModule = Module::where('course_id', $course->id)
            ->where('order_number', '>', $module->order_number)
            ->orderBy('order_number', 'asc')
            ->first();

        return view('modules.show', compact(
            'course',
            'module',
            'embedUrl',
            'isCompleted',
            'previousModule',
            'nextModule',
            'currentPage',
            'totalPages',
            'displayContent',
            'highestRead',
            'modulePagePercent'
        ));
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        $course = $module->course;
        $this->authorizeTutorForCourse($course);

        return view('modules.edit', compact('module', 'course'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $course = $module->course;
        $this->authorizeTutorForCourse($course);

        DB::transaction(function () use ($request, $module, $course) {
            $data = $request->validated();
            $data['total_pages'] = max(1, (int) ($data['total_pages'] ?? 1));
            $module->update($data);
            ActivityLog::record('module_updated', "Modul '{$module->title}' diperbarui pada kursus '{$course->title}'.");
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Modul berhasil diperbarui.');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $course = $module->course;
        $this->authorizeTutorForCourse($course);

        DB::transaction(function () use ($module, $course) {
            ActivityLog::record('module_deleted', "Modul '{$module->title}' dihapus dari kursus '{$course->title}'.");
            $module->delete();
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Modul berhasil dihapus.');
    }

    /**
     * Mark module as completed by Karyawan.
     */
    public function complete(Module $module)
    {
        $user = Auth::user();
        if (!$user->isKaryawan()) {
            abort(403, 'Hanya karyawan yang dapat menandai modul selesai.');
        }

        $totalPages = max(1, (int) $module->total_pages);

        DB::transaction(function () use ($module, $user, $totalPages) {
            $progress = UserProgress::firstOrNew([
                'user_id' => $user->id,
                'module_id' => $module->id,
            ]);
            $progress->current_page = $totalPages;
            $progress->status = 'completed';
            $progress->completed_at = now();
            $progress->save();

            ActivityLog::record('module_completed', "Karyawan '{$user->name}' menyelesaikan semua {$totalPages} halaman modul '{$module->title}'.");
        });

        // Find next module
        $nextModule = Module::where('course_id', $module->course_id)
            ->where('order_number', '>', $module->order_number)
            ->orderBy('order_number', 'asc')
            ->first();

        if ($nextModule) {
            return redirect()->route('modules.show', [$module->course_id, $nextModule->id])
                ->with('success', 'Modul selesai! Melanjutkan ke modul berikutnya.');
        }

        return redirect()->route('courses.show', $module->course_id)
            ->with('success', 'Selamat! Anda telah menyelesaikan semua modul dalam kursus ini. Silakan kerjakan kuis evaluasi jika tersedia.');
    }

    /**
     * Helper to parse YouTube Watch URL to Embed URL.
     */
    private function getYoutubeEmbedUrl($url)
    {
        if (empty($url)) return null;
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return isset($match[1]) ? 'https://www.youtube.com/embed/' . $match[1] : null;
    }

    /**
     * Enforce tutor course assignment.
     */
    private function authorizeTutorForCourse(Course $course)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang.');
        }
    }
}
