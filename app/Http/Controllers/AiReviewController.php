<?php

namespace App\Http\Controllers;

use App\Models\UserAnswer;
use App\Models\EssayReview;
use App\Models\Course;
use App\Models\ScoreAdjustmentLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AiReviewController extends Controller
{
    /**
     * Display a listing of essay answers requiring review.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Hanya admin dan tutor yang dapat melakukan review esai.');
        }

        $query = UserAnswer::whereHas('question', function ($q) {
            $q->where('type', 'essay');
        })->with(['user.company', 'question.quiz.course', 'essayReview', 'scoreAdjustmentLogs.tutor']);

        // Filter by status (pending or finalized)
        $status = $request->input('status', 'pending');
        if ($status === 'pending') {
            $query->whereNull('final_score');
        } else {
            $query->whereNotNull('final_score');
        }

        // Optional filter by course
        $courseId = $request->input('course_id');
        if ($courseId) {
            $query->whereHas('question.quiz.course', function ($q) use ($courseId) {
                $q->where('id', $courseId);
            });
        }

        $courses = Course::orderBy('title')->get();

        // Calculate counts per course for current status
        $baseCountQuery = UserAnswer::whereHas('question', fn($q) => $q->where('type', 'essay'));
        if ($status === 'pending') {
            $baseCountQuery->whereNull('final_score');
        } else {
            $baseCountQuery->whereNotNull('final_score');
        }

        $courseCounts = [];
        foreach ($courses as $c) {
            $courseCounts[$c->id] = (clone $baseCountQuery)->whereHas('question.quiz.course', fn($q) => $q->where('id', $c->id))->count();
        }
        $totalCourseCount = array_sum($courseCounts);

        $reviews = $query->latest()->paginate(10)->withQueryString();

        return view('reviews.index', compact('reviews', 'status', 'courses', 'courseId', 'courseCounts', 'totalCourseCount'));
    }

    /**
     * Display a specific essay review form.
     */
    public function show(UserAnswer $answer)
    {
        $this->authorizeTutorForAnswer($answer);

        $answer->load(['user.company', 'question.quiz.course', 'essayReview', 'scoreAdjustmentLogs.tutor']);
        return view('reviews.show', compact('answer'));
    }

    /**
     * Finalize the essay review by setting a tutor score and notes.
     */
    public function finalize(Request $request, UserAnswer $answer)
    {
        $this->authorizeTutorForAnswer($answer);

        $validated = $request->validate([
            'tutor_score' => 'required|numeric|min:0|max:100',
            'tutor_notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($validated, $answer, $user) {
            // Record detailed score adjustment history
            ScoreAdjustmentLog::create([
                'user_answer_id' => $answer->id,
                'tutor_id' => $user->id,
                'original_ai_score' => $answer->ai_score,
                'adjusted_score' => $validated['tutor_score'],
                'notes' => $validated['tutor_notes'] ?? null,
            ]);

            // Update essay review record
            $review = EssayReview::updateOrCreate(
                ['user_answer_id' => $answer->id],
                [
                    'ai_score' => $answer->ai_score,
                    'ai_feedback' => $answer->ai_feedback,
                    'tutor_score' => $validated['tutor_score'],
                    'tutor_notes' => $validated['tutor_notes'],
                ]
            );

            // Update user answer final score
            $answer->update([
                'final_score' => $validated['tutor_score']
            ]);

            ActivityLog::record(
                'score_adjusted',
                "Tutor '{$user->name}' menyesuaikan skor esai peserta '{$answer->user->name}' dari " . ($answer->ai_score ?? '-') . " menjadi {$validated['tutor_score']}."
            );
        });

        return redirect()->route('reviews.index')->with('success', 'Nilai esai dan catatan penyesuaian tutor berhasil disimpan.');
    }

    /**
     * Helper to check access permissions.
     */
    private function authorizeTutorForAnswer(UserAnswer $answer)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Akses ditolak. Hanya admin dan tutor yang dapat mengakses review esai.');
        }
    }
}
