<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\UserAnswer;
use App\Models\EssayReview;
use App\Models\ActivityLog;
use App\Services\GroqService;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    protected $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    /**
     * Show form to create a quiz.
     */
    public function create(Course $course)
    {
        $this->authorizeTutorForCourse($course);
        return view('quizzes.create', compact('course'));
    }

    /**
     * Store a quiz.
     */
    public function store(StoreQuizRequest $request, Course $course)
    {
        $this->authorizeTutorForCourse($course);

        $quiz = DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            $data['course_id'] = $course->id;
            return Quiz::create($data);
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Kuis berhasil dibuat. Silakan tambahkan pertanyaan.');
    }

    /**
     * Show a quiz with questions.
     */
    public function show(Course $course, Quiz $quiz)
    {
        $quiz->load(['questions.options']);
        return view('quizzes.show', compact('course', 'quiz'));
    }

    /**
     * Show edit form for quiz.
     */
    public function edit(Quiz $quiz)
    {
        $course = $quiz->course;
        $this->authorizeTutorForCourse($course);

        return view('quizzes.edit', compact('quiz', 'course'));
    }

    /**
     * Update quiz.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $course = $quiz->course;
        $this->authorizeTutorForCourse($course);

        DB::transaction(function () use ($request, $quiz) {
            $quiz->update($request->validated());
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Kuis berhasil diperbarui.');
    }

    /**
     * Delete quiz.
     */
    public function destroy(Quiz $quiz)
    {
        $course = $quiz->course;
        $this->authorizeTutorForCourse($course);

        DB::transaction(function () use ($quiz) {
            $quiz->delete();
        });

        return redirect()->route('courses.show', $course->id)->with('success', 'Kuis berhasil dihapus.');
    }

    // Questions & options management methods

    /**
     * Add question form.
     */
    public function addQuestion(Quiz $quiz)
    {
        $this->authorizeTutorForCourse($quiz->course);
        return view('quizzes.add_question', compact('quiz'));
    }

    /**
     * Store question and options.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeTutorForCourse($quiz->course);

        $rules = [
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'ideal_answer' => 'nullable|string',
            'keywords' => 'nullable|string',
        ];

        if ($request->type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $request, $quiz) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question' => $validated['question'],
                'type' => $validated['type'],
                'ideal_answer' => $validated['ideal_answer'] ?? null,
                'keywords' => $validated['keywords'] ?? null,
            ]);

            if ($validated['type'] === 'multiple_choice') {
                foreach ($validated['options'] as $index => $optionText) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $validated['correct_option']),
                    ]);
                }
            }
        });

        return redirect()->route('courses.quizzes.show', [$quiz->course_id, $quiz->id])
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Show form to edit/customize question and answers.
     */
    public function editQuestion(Quiz $quiz, Question $question)
    {
        $this->authorizeTutorForCourse($quiz->course);
        $question->load('options');
        return view('quizzes.edit_question', compact('quiz', 'question'));
    }

    /**
     * Update/customize question, options, and answer keys.
     */
    public function updateQuestion(Request $request, Quiz $quiz, Question $question)
    {
        $this->authorizeTutorForCourse($quiz->course);

        $rules = [
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'ideal_answer' => 'nullable|string',
            'keywords' => 'nullable|string',
        ];

        if ($request->type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $request, $question) {
            $question->update([
                'question' => $validated['question'],
                'type' => $validated['type'],
                'ideal_answer' => $validated['ideal_answer'] ?? null,
                'keywords' => $validated['keywords'] ?? null,
            ]);

            if ($validated['type'] === 'multiple_choice') {
                // Delete existing options and recreate with custom ones
                $question->options()->delete();

                foreach ($validated['options'] as $index => $optionText) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $validated['correct_option']),
                    ]);
                }
            } else {
                $question->options()->delete();
            }
        });

        return redirect()->route('courses.quizzes.show', [$quiz->course_id, $quiz->id])
            ->with('success', 'Soal dan kunci jawaban berhasil diperbarui.');
    }

    /**
     * Delete question.
     */
    public function destroyQuestion(Quiz $quiz, Question $question)
    {
        $this->authorizeTutorForCourse($quiz->course);

        DB::transaction(function () use ($question) {
            $question->delete();
        });

        return redirect()->route('courses.quizzes.show', [$quiz->course_id, $quiz->id])
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    // Quiz taking & grading for employees

    /**
     * Render take quiz page.
     */
    public function take(Quiz $quiz)
    {
        $user = Auth::user();
        if (!$user->isKaryawan()) {
            abort(403, 'Hanya karyawan yang dapat mengambil kuis.');
        }

        // Validate course company access
        if (!$quiz->course->companies()->where('companies.id', $user->company_id)->exists()) {
            abort(403, 'Akses ditolak. Kuis ini tidak tersedia untuk perusahaan Anda.');
        }

        // Check if quiz has questions
        if ($quiz->questions()->count() === 0) {
            return redirect()->route('courses.show', $quiz->course_id)
                ->with('error', 'Kuis ini belum memiliki pertanyaan.');
        }

        // Validate that user completed all modules of the course
        $totalModules = $quiz->course->modules()->count();
        $completedModules = DB::table('user_progress')
            ->where('user_id', $user->id)
            ->whereIn('module_id', $quiz->course->modules()->pluck('id'))
            ->count();

        if ($totalModules > 0 && $completedModules < $totalModules) {
            return redirect()->route('courses.show', $quiz->course_id)
                ->with('error', 'Anda harus menyelesaikan seluruh modul materi sebelum mengerjakan kuis.');
        }

        // Check if user has already answered this quiz
        $quizQuestionIds = $quiz->questions()->pluck('id');
        $hasTaken = UserAnswer::where('user_id', $user->id)
            ->whereIn('question_id', $quizQuestionIds)
            ->exists();

        if ($hasTaken) {
            return redirect()->route('quizzes.history', $quiz->id)
                ->with('info', 'Anda sudah mengerjakan kuis ini sebelumnya.');
        }

        $quiz->load(['questions.options']);
        return view('quizzes.take', compact('quiz'));
    }

    /**
     * Submit and grade quiz responses.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        if (!$user->isKaryawan()) {
            abort(403);
        }

        // Validate course company access
        if (!$quiz->course->companies()->where('companies.id', $user->company_id)->exists()) {
            abort(403, 'Akses ditolak. Kuis ini tidak tersedia untuk perusahaan Anda.');
        }

        $questions = $quiz->questions()->with('options')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('courses.show', $quiz->course_id)
                ->with('error', 'Kuis tidak memiliki pertanyaan.');
        }

        // Prevent duplicate submission
        $hasTaken = UserAnswer::where('user_id', $user->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->exists();

        if ($hasTaken) {
            return redirect()->route('quizzes.history', $quiz->id)
                ->with('info', 'Anda sudah mengerjakan kuis ini sebelumnya.');
        }

        $correctCount = 0;
        $incorrectCount = 0;
        $sumScore = 0;
        $gradedQuestionsCount = 0;
        $hasEssay = false;

        DB::transaction(function () use ($request, $questions, $user, &$correctCount, &$incorrectCount, &$sumScore, &$gradedQuestionsCount, &$hasEssay) {
            foreach ($questions as $question) {
                $answerVal = $request->input('answers.' . $question->id);

                if ($question->type === 'multiple_choice') {
                    // Check if answer matches correct option
                    $correctOption = $question->options->firstWhere('is_correct', true);
                    $selectedOption = $question->options->firstWhere('id', $answerVal);

                    $isCorrect = ($correctOption && $selectedOption && $correctOption->id == $selectedOption->id);
                    $score = $isCorrect ? 100.0 : 0.0;

                    if ($isCorrect) {
                        $correctCount++;
                    } else {
                        $incorrectCount++;
                    }
                    $sumScore += $score;
                    $gradedQuestionsCount++;

                    UserAnswer::create([
                        'user_id' => $user->id,
                        'question_id' => $question->id,
                        'answer' => $selectedOption ? $selectedOption->option_text : 'Tidak menjawab',
                        'ai_score' => $score,
                        'ai_feedback' => $isCorrect ? 'Jawaban Benar' : 'Jawaban Salah. Jawaban yang benar adalah: ' . ($correctOption ? $correctOption->option_text : ''),
                        'final_score' => $score,
                    ]);
                } else {
                    $hasEssay = true;
                    // Essay question -> Call AI Review
                    $essayAnswer = $answerVal ?? '';
                    $aiResponse = $this->groqService->reviewEssay($question->question, $essayAnswer, $question->ideal_answer ?? null, $question->keywords ?? null);

                    $sumScore += $aiResponse['score'];
                    $gradedQuestionsCount++;

                    $userAnswer = UserAnswer::create([
                        'user_id' => $user->id,
                        'question_id' => $question->id,
                        'answer' => $essayAnswer,
                        'ai_score' => $aiResponse['score'],
                        'ai_feedback' => $aiResponse['feedback'] . "\nSaran: " . $aiResponse['suggestion'],
                        'final_score' => null, // Tutor needs to review and finalize
                    ]);

                    EssayReview::create([
                        'user_answer_id' => $userAnswer->id,
                        'ai_score' => $aiResponse['score'],
                        'ai_feedback' => $aiResponse['feedback'] . "\nSaran: " . $aiResponse['suggestion'],
                        'tutor_score' => null,
                        'tutor_notes' => null,
                    ]);
                }
            }
        });

        ActivityLog::record('quiz_submitted', "Karyawan '{$user->name}' mengumpulkan jawaban kuis '{$quiz->title}'.");

        $finalScore = $gradedQuestionsCount > 0 ? round($sumScore / $gradedQuestionsCount, 2) : 0;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil dikirim.',
                'redirect_url' => route('quizzes.history', $quiz->id),
                'stats' => [
                    'correct' => $correctCount,
                    'incorrect' => $incorrectCount,
                    'score' => $finalScore,
                    'has_essay' => $hasEssay
                ]
            ]);
        }

        return redirect()->route('quizzes.history', $quiz->id)->with('success', 'Kuis berhasil diselesaikan.');
    }

    /**
     * Show quiz history / results details.
     */
    public function history(Quiz $quiz)
    {
        $user = Auth::user();
        $questions = $quiz->questions()->get();
        $questionIds = $questions->pluck('id');

        $userAnswers = UserAnswer::where('user_id', $user->id)
            ->whereIn('question_id', $questionIds)
            ->with(['question', 'essayReview'])
            ->get();

        if ($userAnswers->isEmpty()) {
            return redirect()->route('courses.show', $quiz->course_id)
                ->with('error', 'Anda belum mengerjakan kuis ini.');
        }

        // Calculate score summary
        $totalQuestions = $questions->count();
        $gradedQuestionsCount = 0;
        $sumScore = 0;
        $isPendingTutor = false;

        foreach ($userAnswers as $ans) {
            if ($ans->question->type === 'essay') {
                if ($ans->final_score !== null) {
                    $sumScore += $ans->final_score;
                    $gradedQuestionsCount++;
                } else {
                    // Still waiting for tutor finalization
                    // Show AI score as temporary indicator but mark pending
                    $sumScore += $ans->ai_score;
                    $gradedQuestionsCount++;
                    $isPendingTutor = true;
                }
            } else {
                $sumScore += $ans->final_score;
                $gradedQuestionsCount++;
            }
        }

        $finalScore = $gradedQuestionsCount > 0 ? round($sumScore / $gradedQuestionsCount, 2) : 0;
        $passed = $finalScore >= $quiz->passing_grade;

        return view('quizzes.history', compact('quiz', 'userAnswers', 'finalScore', 'passed', 'isPendingTutor'));
    }

    /**
     * Helper to verify tutor owns the course.
     */
    private function authorizeTutorForCourse(Course $course)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isTutor()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang.');
        }
    }
}
