<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Course;
use App\Models\Module;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyCourseProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_conditional_company_routing_rule(): void
    {
        // 1. Create Startup entity and Client entity
        $startup = Company::create([
            'name' => 'TechEdu Startup Lab',
            'type' => 'startup',
            'email' => 'startup@test.com',
        ]);

        $client = Company::create([
            'name' => 'Perusahaan Klien Alpha',
            'type' => 'client',
            'email' => 'alpha@test.com',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $startup->id,
        ]);

        // 2. Add a Tutor -> system must associate to startup
        $responseTutor = $this->actingAs($admin)->post('/employees', [
            'name' => 'Budi Tutor',
            'email' => 'budi@test.com',
            'password' => 'password123',
            'role' => 'tutor',
        ]);
        $responseTutor->assertRedirect(route('companies.employees'));

        $tutorUser = User::where('email', 'budi@test.com')->first();
        $this->assertNotNull($tutorUser);
        $this->assertEquals('tutor', $tutorUser->role);
        $this->assertEquals($startup->id, $tutorUser->company_id);

        // 3. Add an Employee/Participant -> system must associate to client company
        $responseEmployee = $this->actingAs($admin)->post('/employees', [
            'name' => 'Siti Karyawan',
            'email' => 'siti@test.com',
            'password' => 'password123',
            'role' => 'karyawan',
            'company_id' => $client->id,
        ]);
        $responseEmployee->assertRedirect(route('companies.employees'));

        $employeeUser = User::where('email', 'siti@test.com')->first();
        $this->assertNotNull($employeeUser);
        $this->assertEquals('karyawan', $employeeUser->role);
        $this->assertEquals($client->id, $employeeUser->company_id);
    }

    public function test_dashboard_groups_course_per_company_with_correct_progress(): void
    {
        // 1. Create Companies
        $startup = Company::create(['name' => 'Pihak Startup Provider', 'type' => 'startup', 'email' => 'provider@test.com']);
        $companyA = Company::create(['name' => 'Perusahaan X', 'type' => 'client', 'email' => 'x@comp.com']);
        $companyB = Company::create(['name' => 'Perusahaan Y', 'type' => 'client', 'email' => 'y@comp.com']);

        // 2. Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_id' => $startup->id,
        ]);

        $tutor = User::create([
            'name' => 'Tutor User',
            'email' => 'tutor@test.com',
            'password' => bcrypt('password'),
            'role' => 'tutor',
            'company_id' => $startup->id,
        ]);

        // 2 Employees in Company X
        $empX1 = User::create([
            'name' => 'Peserta X1',
            'email' => 'x1@test.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $companyA->id,
        ]);
        $empX2 = User::create([
            'name' => 'Peserta X2',
            'email' => 'x2@test.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $companyA->id,
        ]);

        // 2 Employees in Company Y
        $empY1 = User::create([
            'name' => 'Peserta Y1',
            'email' => 'y1@test.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $companyB->id,
        ]);
        $empY2 = User::create([
            'name' => 'Peserta Y2',
            'email' => 'y2@test.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'company_id' => $companyB->id,
        ]);

        // 3. Create Courses: Kursus A & Kursus B
        $courseA = Course::create([
            'tutor_id' => $tutor->id,
            'title' => 'Kursus A',
            'level' => 'beginner',
        ]);
        $courseB = Course::create([
            'tutor_id' => $tutor->id,
            'title' => 'Kursus B',
            'level' => 'intermediate',
        ]);

        // Modules: 2 modules for Kursus A, 2 modules for Kursus B
        $mA1 = Module::create(['course_id' => $courseA->id, 'title' => 'Modul A1', 'order_number' => 1]);
        $mA2 = Module::create(['course_id' => $courseA->id, 'title' => 'Modul A2', 'order_number' => 2]);
        $mB1 = Module::create(['course_id' => $courseB->id, 'title' => 'Modul B1', 'order_number' => 1]);
        $mB2 = Module::create(['course_id' => $courseB->id, 'title' => 'Modul B2', 'order_number' => 2]);

        // 4. Assign both Kursus A & Kursus B to Perusahaan X & Perusahaan Y
        // Resulting in 4 separated mappings:
        // - "Kursus A untuk Perusahaan X"
        // - "Kursus A untuk Perusahaan Y"
        // - "Kursus B untuk Perusahaan X"
        // - "Kursus B untuk Perusahaan Y"
        $courseA->companies()->attach([$companyA->id, $companyB->id]);
        $courseB->companies()->attach([$companyA->id, $companyB->id]);

        // Scenario 1: Kursus A untuk Perusahaan X -> Semua peserta selesai (100%)
        UserProgress::create(['user_id' => $empX1->id, 'module_id' => $mA1->id, 'status' => 'completed']);
        UserProgress::create(['user_id' => $empX1->id, 'module_id' => $mA2->id, 'status' => 'completed']);
        UserProgress::create(['user_id' => $empX2->id, 'module_id' => $mA1->id, 'status' => 'completed']);
        UserProgress::create(['user_id' => $empX2->id, 'module_id' => $mA2->id, 'status' => 'completed']);

        // Scenario 2: Kursus A untuk Perusahaan Y -> Bervariasi (Y1: 100%, Y2: 0% -> Avg: 50%)
        UserProgress::create(['user_id' => $empY1->id, 'module_id' => $mA1->id, 'status' => 'completed']);
        UserProgress::create(['user_id' => $empY1->id, 'module_id' => $mA2->id, 'status' => 'completed']);

        // Scenario 3: Kursus B untuk Perusahaan X -> Bervariasi (X1: 50%, X2: 0% -> Avg: 25%)
        UserProgress::create(['user_id' => $empX1->id, 'module_id' => $mB1->id, 'status' => 'completed']);

        // Scenario 4: Kursus B untuk Perusahaan Y -> Belum mulai (0%)

        // Check Admin Dashboard response
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Kursus A');
        $response->assertSee('Kursus B');
        $response->assertSee('Perusahaan X');
        $response->assertSee('Perusahaan Y');
        $response->assertSee('100% Selesai');
        $response->assertSee('50%');
        $response->assertSee('25%');
        $response->assertSee('0% Belum Mulai');

        // Check Tutor Dashboard response
        $responseTutor = $this->actingAs($tutor)->get('/dashboard');
        $responseTutor->assertStatus(200);
        $responseTutor->assertSee('Kursus A');
        $responseTutor->assertSee('Perusahaan X');
        $responseTutor->assertSee('100% Selesai');
    }
}
