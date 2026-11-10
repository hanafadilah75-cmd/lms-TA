<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\UserAnswer;
use App\Models\EssayReview;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DetailProgressAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $tutor;
    protected $karyawanA;
    protected $karyawanB;
    protected $companyA;
    protected $companyB;
    protected $course;
    protected $quiz;
    protected $module1;
    protected $module2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'PT Alpha Solusi', 'type' => 'client', 'email' => 'alpha@test.com']);
        $this->companyB = Company::create(['name' => 'PT Beta Inovasi', 'type' => 'client', 'email' => 'beta@test.com']);
        $startup = Company::create(['name' => 'EduTech Startup', 'type' => 'startup', 'email' => 'startup@test.com']);

        $this->admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@lms.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $startup->id,
        ]);

        $this->tutor = User::create([
            'name' => 'Tutor Hebat',
            'email' => 'tutor@lms.com',
            'password' => bcrypt('password'),
            'role' => 'tutor',
            'company_id' => $startup->id,
        ]);

        $this->karyawanA = User::create([
            'name' => 'Karyawan Alpha',
            'email' => 'karyawan.a@alpha.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $this->companyA->id,
        ]);

        $this->karyawanB = User::create([
            'name' => 'Karyawan Beta',
            'email' => 'karyawan.b@beta.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $this->companyB->id,
        ]);

        $this->course = Course::create([
            'tutor_id' => $this->tutor->id,
            'title' => 'Onboarding Engineer',
            'level' => 'beginner',
        ]);
        $this->course->companies()->attach([$this->companyA->id, $this->companyB->id]);

        $this->module1 = Module::create(['course_id' => $this->course->id, 'title' => 'Pengenalan Budaya', 'order_number' => 1]);
        $this->module2 = Module::create(['course_id' => $this->course->id, 'title' => 'Teknis Tooling', 'order_number' => 2]);

        $this->quiz = Quiz::create([
            'course_id' => $this->course->id,
            'title' => 'Kuis Orientasi',
            'passing_grade' => 70,
        ]);

        $q1 = Question::create([
            'quiz_id' => $this->quiz->id,
            'question' => 'Apa nilai utama perusahaan?',
            'type' => 'multiple_choice',
        ]);
        Option::create(['question_id' => $q1->id, 'option_text' => 'Inovasi', 'is_correct' => true]);
        Option::create(['question_id' => $q1->id, 'option_text' => 'Malas', 'is_correct' => false]);

        $q2 = Question::create([
            'quiz_id' => $this->quiz->id,
            'question' => 'Jelaskan tujuan Anda dalam tim!',
            'type' => 'essay',
        ]);
    }

    /**
     * Test bug report: Admin viewing employee detail progress when quiz has NOT been answered.
     * Previously threw 500 error: Undefined array key "has_essay".
     */
    public function test_admin_view_employee_progress_detail_without_answers(): void
    {
        $response = $this->actingAs($this->admin)->get(route('progress.employee', $this->karyawanA->id));
        $response->assertStatus(200);
        $response->assertSee('Karyawan Alpha');
        $response->assertSee('Onboarding Engineer');
        $response->assertSee('Belum Dikerjakan');
    }

    /**
     * Test admin viewing employee detail progress with essay pending review.
     */
    public function test_admin_view_employee_progress_detail_with_pending_essay(): void
    {
        $q1 = $this->quiz->questions->where('type', 'multiple_choice')->first();
        $q2 = $this->quiz->questions->where('type', 'essay')->first();

        UserAnswer::create([
            'user_id' => $this->karyawanA->id,
            'question_id' => $q1->id,
            'answer' => 'Inovasi',
            'ai_score' => 100,
            'ai_feedback' => 'Benar',
            'final_score' => 100,
        ]);

        UserAnswer::create([
            'user_id' => $this->karyawanA->id,
            'question_id' => $q2->id,
            'answer' => 'Saya ingin berkontribusi maksimal.',
            'ai_score' => 85,
            'ai_feedback' => 'Bagus',
            'final_score' => null, // Pending review
        ]);

        $response = $this->actingAs($this->admin)->get(route('progress.employee', $this->karyawanA->id));
        $response->assertStatus(200);
        $response->assertSee('Menunggu Review');
    }

    /**
     * Test employee can view their own progress but cannot view another employee's progress.
     */
    public function test_employee_access_authorization_on_progress_detail(): void
    {
        // Own progress -> 200
        $response = $this->actingAs($this->karyawanA)->get(route('progress.employee', $this->karyawanA->id));
        $response->assertStatus(200);

        // Other employee's progress -> 403
        $responseOther = $this->actingAs($this->karyawanA)->get(route('progress.employee', $this->karyawanB->id));
        $responseOther->assertStatus(403);
    }

    /**
     * Test dashboards render for all 3 roles.
     */
    public function test_dashboards_render_for_all_roles(): void
    {
        $resAdmin = $this->actingAs($this->admin)->get(route('dashboard'));
        $resAdmin->assertStatus(200);

        $resTutor = $this->actingAs($this->tutor)->get(route('dashboard'));
        $resTutor->assertStatus(200);

        $resKaryawan = $this->actingAs($this->karyawanA)->get(route('dashboard'));
        $resKaryawan->assertStatus(200);
    }

    /**
     * Test company employee search and filter.
     */
    public function test_company_employee_search_and_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('companies.employees', ['search' => 'Alpha']));
        $response->assertStatus(200);
        $response->assertSee('Karyawan Alpha');
        $response->assertDontSee('Karyawan Beta');
    }

    /**
     * Test all course & module views render properly.
     */
    public function test_course_and_module_views(): void
    {
        // Courses index
        $this->actingAs($this->admin)->get(route('courses.index'))->assertStatus(200);
        $this->actingAs($this->tutor)->get(route('courses.index'))->assertStatus(200);
        $this->actingAs($this->karyawanA)->get(route('courses.index'))->assertStatus(200);

        // Course show
        $this->actingAs($this->admin)->get(route('courses.show', $this->course->id))->assertStatus(200);
        $this->actingAs($this->tutor)->get(route('courses.show', $this->course->id))->assertStatus(200);
        $this->actingAs($this->karyawanA)->get(route('courses.show', $this->course->id))->assertStatus(200);

        // Module show
        $this->actingAs($this->karyawanA)->get(route('modules.show', [$this->course->id, $this->module1->id]))->assertStatus(200);
    }

    /**
     * Test reviews index and detail render properly.
     */
    public function test_reviews_views(): void
    {
        $q2 = $this->quiz->questions->where('type', 'essay')->first();
        $answer = UserAnswer::create([
            'user_id' => $this->karyawanA->id,
            'question_id' => $q2->id,
            'answer' => 'Saya berkomitmen memberikan hasil terbaik.',
            'ai_score' => 90,
            'ai_feedback' => 'Sangat baik',
            'final_score' => null,
        ]);

        $this->actingAs($this->admin)->get(route('reviews.index'))->assertStatus(200)->assertSee('Karyawan Alpha');
        $this->actingAs($this->tutor)->get(route('reviews.index'))->assertStatus(200)->assertSee('Karyawan Alpha');
        $this->actingAs($this->admin)->get(route('reviews.show', $answer->id))->assertStatus(200)->assertSee('Finalisasi Nilai');
        $this->actingAs($this->tutor)->get(route('reviews.show', $answer->id))->assertStatus(200)->assertSee('Finalisasi Nilai');
    }

    /**
     * Test that Tutor 2 is fully synchronized with Tutor 1 across Courses, Reviews, Progress, and Dashboard.
     */
    public function test_tutor1_and_tutor2_are_fully_synchronized(): void
    {
        $tutor2 = User::create([
            'name' => 'Tutor 2',
            'email' => 'tutor2@lms.com',
            'password' => bcrypt('password'),
            'role' => 'tutor',
            'company_id' => $this->tutor->company_id,
        ]);

        // 1. Both tutors see all courses in course listing
        $resTutor1Courses = $this->actingAs($this->tutor)->get(route('courses.index'));
        $resTutor1Courses->assertStatus(200)->assertSee($this->course->title);

        $resTutor2Courses = $this->actingAs($tutor2)->get(route('courses.index'));
        $resTutor2Courses->assertStatus(200)->assertSee($this->course->title);

        // 2. Both tutors can view and edit courses (even if owned by another tutor)
        $this->actingAs($tutor2)->get(route('courses.show', $this->course->id))
            ->assertStatus(200)
            ->assertSee('Edit Kursus')
            ->assertSee('Tambah Modul');

        $this->actingAs($tutor2)->get(route('courses.edit', $this->course->id))
            ->assertStatus(200);

        // 3. Both tutors can view modules and quizzes
        $this->actingAs($tutor2)->get(route('modules.show', [$this->course->id, $this->module1->id]))
            ->assertStatus(200)
            ->assertSee('Edit Modul');

        // 4. Pending essay review is visible and actionable for both tutors
        $q2 = $this->quiz->questions->where('type', 'essay')->first();
        $answer = UserAnswer::create([
            'user_id' => $this->karyawanA->id,
            'question_id' => $q2->id,
            'answer' => 'Tutor 2 bisa review ini juga.',
            'ai_score' => 88,
            'ai_feedback' => 'Bagus sekali',
            'final_score' => null,
        ]);

        // Tutor 2 sees the pending review
        $resReviewTutor2 = $this->actingAs($tutor2)->get(route('reviews.index'));
        $resReviewTutor2->assertStatus(200)->assertSee('Karyawan Alpha');

        // Tutor 2 can view and finalize the review
        $this->actingAs($tutor2)->get(route('reviews.show', $answer->id))
            ->assertStatus(200)
            ->assertSee('Finalisasi Nilai');

        $postReview = $this->actingAs($tutor2)->post(route('reviews.finalize', $answer->id), [
            'tutor_score' => 95,
            'tutor_notes' => 'Kerja bagus!',
        ]);
        $postReview->assertRedirect(route('reviews.index'));
        $this->assertDatabaseHas('user_answers', ['id' => $answer->id, 'final_score' => 95]);

        // 5. Both tutors can view Progress across client companies & employees
        $this->actingAs($tutor2)->get(route('progress.index'))
            ->assertStatus(200)
            ->assertSee($this->companyA->name)
            ->assertSee($this->companyB->name);

        $this->actingAs($tutor2)->get(route('progress.company', $this->companyA->id))
            ->assertStatus(200)
            ->assertSee('Karyawan Alpha');

        $this->actingAs($tutor2)->get(route('progress.employee', $this->karyawanA->id))
            ->assertStatus(200)
            ->assertSee('Karyawan Alpha');

        // 6. Both tutors dashboard are populated
        $resTutor2Dash = $this->actingAs($tutor2)->get(route('dashboard'));
        $resTutor2Dash->assertStatus(200)->assertSee($this->course->title);

        // 7. Security: Employee cannot access reviews
        $this->actingAs($this->karyawanA)->get(route('reviews.index'))->assertStatus(403);
    }

    /**
     * Test admin and tutor can customize question answers (multiple choice and essay).
     */
    public function test_admin_and_tutor_can_customize_question_answers(): void
    {
        $qMcq = $this->quiz->questions->where('type', 'multiple_choice')->first();
        $qEssay = $this->quiz->questions->where('type', 'essay')->first();

        // 1. Admin edits MCQ question and customizes options & correct answer
        $resAdminEdit = $this->actingAs($this->admin)->get(route('quizzes.questions.edit', [$this->quiz->id, $qMcq->id]));
        $resAdminEdit->assertStatus(200)->assertSee('Kustomisasi Soal & Kunci Jawaban');

        $resAdminUpdate = $this->actingAs($this->admin)->put(route('quizzes.questions.update', [$this->quiz->id, $qMcq->id]), [
            'question' => 'Pertanyaan Kustom Admin',
            'type' => 'multiple_choice',
            'options' => ['Opsi 1', 'Opsi 2 Benar', 'Opsi 3', 'Opsi 4'],
            'correct_option' => 1,
        ]);
        $resAdminUpdate->assertRedirect(route('courses.quizzes.show', [$this->course->id, $this->quiz->id]));
        $this->assertDatabaseHas('questions', ['id' => $qMcq->id, 'question' => 'Pertanyaan Kustom Admin']);
        $this->assertDatabaseHas('options', ['question_id' => $qMcq->id, 'option_text' => 'Opsi 2 Benar', 'is_correct' => 1]);

        // 2. Tutor edits Essay question and customizes ideal answer key
        $resTutorEdit = $this->actingAs($this->tutor)->get(route('quizzes.questions.edit', [$this->quiz->id, $qEssay->id]));
        $resTutorEdit->assertStatus(200);

        $resTutorUpdate = $this->actingAs($this->tutor)->put(route('quizzes.questions.update', [$this->quiz->id, $qEssay->id]), [
            'question' => $qEssay->question,
            'type' => 'essay',
            'ideal_answer' => 'Kunci Jawaban Acuan Esai oleh Tutor',
        ]);
        $resTutorUpdate->assertRedirect(route('courses.quizzes.show', [$this->course->id, $this->quiz->id]));
        $this->assertDatabaseHas('questions', ['id' => $qEssay->id, 'ideal_answer' => 'Kunci Jawaban Acuan Esai oleh Tutor']);

        // 3. Security: Karyawan cannot access question edit
        $this->actingAs($this->karyawanA)->get(route('quizzes.questions.edit', [$this->quiz->id, $qMcq->id]))
            ->assertStatus(403);
    }

    /**
     * Test login view.
     */
    public function test_login_page_renders(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Sistem E-Training LMS');
    }
}
