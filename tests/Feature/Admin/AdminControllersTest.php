<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Division;
use App\Models\Role;
use App\Models\JobDesk;
use App\Models\EmployeeJobDesk;
use App\Models\PromotionRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminControllersTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected $adminUser;
    protected $directorUser;
    protected $divisionHeadUser;
    protected $employeeUser;
    protected $division;
    protected $role;
    protected $jobDesk;
    protected $employeeJobDesk;

    protected function setUp(): void
    {
        parent::setUp();

        // Get or create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $directorRole = Role::firstOrCreate(['name' => 'direktur']);
        $divisionHeadRole = Role::firstOrCreate(['name' => 'kepala divisi']);
        $employeeRole = Role::firstOrCreate(['name' => 'karyawan']);

        // Get or create division
        $this->division = Division::firstOrCreate(
            ['name' => 'IT Division Test'],
            ['name' => 'IT Division Test']
        );

        // Get or create admin user
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin.test@example.com'],
            [
                'name' => 'Admin Test',
                'email' => 'admin.test@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'division_id' => null,
            ]
        );

        // Get or create director user
        $this->directorUser = User::firstOrCreate(
            ['email' => 'director.test@example.com'],
            [
                'name' => 'Director Test',
                'email' => 'director.test@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $directorRole->id,
                'division_id' => null,
            ]
        );

        // Get or create division head user
        $this->divisionHeadUser = User::firstOrCreate(
            ['email' => 'divhead.test@example.com'],
            [
                'name' => 'Division Head Test',
                'email' => 'divhead.test@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $divisionHeadRole->id,
                'division_id' => $this->division->id,
            ]
        );

        // Get or create employee user
        $this->employeeUser = User::firstOrCreate(
            ['email' => 'employee.test@example.com'],
            [
                'name' => 'Employee Test',
                'email' => 'employee.test@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $employeeRole->id,
                'division_id' => $this->division->id,
            ]
        );

        // Get or create job desk
        $this->jobDesk = JobDesk::firstOrCreate(
            ['title' => 'Test Job Desk For Testing'],
            [
                'title' => 'Test Job Desk For Testing',
                'description' => 'Test Description',
                'deadline' => Carbon::now()->addDays(7),
                'created_by' => $this->divisionHeadUser->id,
                'division_id' => $this->division->id,
            ]
        );

        // Get or create employee job desk assignment
        $this->employeeJobDesk = EmployeeJobDesk::firstOrCreate(
            [
                'job_desk_id' => $this->jobDesk->id,
                'employee_id' => $this->employeeUser->id,
            ],
            [
                'job_desk_id' => $this->jobDesk->id,
                'employee_id' => $this->employeeUser->id,
                'status' => 'assigned',
            ]
        );

        $this->role = $employeeRole;
    }

    // ============================================
    // USER CONTROLLER TESTS
    // ============================================

    public function test_admin_dapat_melihat_daftar_user()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.users.index', $response->getName());
        $this->assertArrayHasKey('users', $response->getData());
        $this->assertArrayHasKey('roles', $response->getData());
        $this->assertArrayHasKey('divisions', $response->getData());
    }

    public function test_admin_dapat_melihat_form_create_user()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.users.create', $response->getName());
        $this->assertArrayHasKey('roles', $response->getData());
        $this->assertArrayHasKey('divisions', $response->getData());
    }

    public function test_admin_dapat_membuat_user_baru()
    {
        $this->actingAs($this->adminUser);

        $uniqueEmail = 'newuser.test.' . time() . '@example.com';

        $request = new Request();
        $request->replace([
            'name' => 'New User Test',
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->role->id,
            'division_id' => $this->division->id,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->store($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'name' => 'New User Test',
        ]);
    }

    public function test_admin_dapat_melihat_detail_user()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->show($this->employeeUser);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.users.show', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());
    }

    public function test_admin_dapat_melihat_form_edit_user()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->edit($this->employeeUser);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.users.edit', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());
    }

    public function test_admin_dapat_update_user()
    {
        $this->actingAs($this->adminUser);

        $originalName = $this->employeeUser->name;

        $request = new Request();
        $request->replace([
            'name' => 'Updated Employee Name Test',
            'email' => $this->employeeUser->email,
            'role_id' => $this->employeeUser->role_id,
            'division_id' => $this->employeeUser->division_id,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->update($request, $this->employeeUser);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('users', [
            'id' => $this->employeeUser->id,
            'name' => 'Updated Employee Name Test',
        ]);

        // Kembalikan ke nama awal
        $this->employeeUser->update(['name' => $originalName]);
    }

    public function test_admin_dapat_hapus_user_tanpa_tugas_aktif()
    {
        $this->actingAs($this->adminUser);

        // Create temporary user for deletion
        $userToDelete = User::create([
            'name' => 'User To Delete Test',
            'email' => 'delete.test.' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->role->id,
            'division_id' => $this->division->id,
        ]);

        $userId = $userToDelete->id;

        $controller = app()->make('App\Http\Controllers\Admin\UserController');
        $response = $controller->destroy($userToDelete);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);
    }

    // ============================================
    // DIVISION CONTROLLER TESTS
    // ============================================

    public function test_admin_dapat_melihat_daftar_division()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\DivisionController');
        $response = $controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.divisions.index', $response->getName());
        $this->assertArrayHasKey('divisions', $response->getData());
    }

    public function test_admin_dapat_melihat_form_create_division()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\DivisionController');
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.divisions.create', $response->getName());
    }

    public function test_admin_dapat_membuat_division_baru()
    {
        $this->actingAs($this->adminUser);

        $divisionName = 'Marketing Division Test ' . time();

        $request = new Request();
        $request->replace([
            'name' => $divisionName,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\DivisionController');
        $response = $controller->store($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('divisions', [
            'name' => $divisionName,
        ]);
    }

    public function test_admin_dapat_melihat_detail_division()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\DivisionController');
        $response = $controller->show($this->division);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.divisions.show', $response->getName());
        $this->assertArrayHasKey('division', $response->getData());
    }

    public function test_admin_dapat_update_division()
    {
        $this->actingAs($this->adminUser);

        $originalName = $this->division->name;
        $newName = 'Updated IT Division Test ' . time();

        $request = new Request();
        $request->replace([
            'name' => $newName,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\DivisionController');
        $response = $controller->update($request, $this->division);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('divisions', [
            'id' => $this->division->id,
            'name' => $newName,
        ]);

        // Kembalikan ke nama awal
        $this->division->update(['name' => $originalName]);
    }

    // ============================================
    // ROLE CONTROLLER TESTS
    // ============================================

    public function test_admin_dapat_melihat_daftar_role()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\RoleController');
        $response = $controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.roles.index', $response->getName());
        $this->assertArrayHasKey('roles', $response->getData());
    }

    public function test_admin_dapat_membuat_role_baru()
    {
        $this->actingAs($this->adminUser);

        $roleName = 'manager_test_' . time();

        $request = new Request();
        $request->replace([
            'name' => $roleName,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\RoleController');
        $response = $controller->store($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('roles', [
            'name' => $roleName,
        ]);
    }

    public function test_admin_dapat_update_role()
    {
        $this->actingAs($this->adminUser);

        $testRole = Role::create(['name' => 'test_role_' . time()]);
        $updatedName = 'updated_role_' . time();

        $request = new Request();
        $request->replace([
            'name' => $updatedName,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\RoleController');
        $response = $controller->update($request, $testRole);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('roles', [
            'id' => $testRole->id,
            'name' => $updatedName,
        ]);
    }

    // ============================================
    // MANAGE JOB DESK CONTROLLER TESTS
    // ============================================

    public function test_admin_dapat_melihat_daftar_job_desk()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\AdminManageJobdeskController');
        $response = $controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.job_desks.index', $response->getName());
        $this->assertArrayHasKey('jobDesks', $response->getData());
    }

    public function test_admin_dapat_membuat_job_desk_baru()
    {
        $this->actingAs($this->adminUser);

        $jobTitle = 'New Job Desk Test ' . time();

        $request = new Request();
        $request->replace([
            'title' => $jobTitle,
            'description' => 'New Job Description Test',
            'deadline' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'employees' => [$this->employeeUser->id],
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminManageJobdeskController');
        $response = $controller->store($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('job_desks', [
            'title' => $jobTitle,
        ]);
    }

    public function test_admin_dapat_update_job_desk()
    {
        $this->actingAs($this->adminUser);

        $originalTitle = $this->jobDesk->title;
        $updatedTitle = 'Updated Job Desk Test ' . time();

        $request = new Request();
        $request->replace([
            'title' => $updatedTitle,
            'description' => 'Updated Description Test',
            'deadline' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'employees' => [$this->employeeUser->id],
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminManageJobdeskController');
        $response = $controller->update($request, $this->jobDesk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('job_desks', [
            'id' => $this->jobDesk->id,
            'title' => $updatedTitle,
        ]);

        // Kembalikan ke judul awal
        $this->jobDesk->update(['title' => $originalTitle]);
    }

    public function test_admin_dapat_hapus_job_desk()
    {
        $this->actingAs($this->adminUser);

        $jobDeskToDelete = JobDesk::create([
            'title' => 'Job Desk To Delete Test ' . time(),
            'description' => 'Description Test',
            'deadline' => Carbon::now()->addDays(7),
            'created_by' => $this->divisionHeadUser->id,
            'division_id' => $this->division->id,
        ]);

        $jobDeskId = $jobDeskToDelete->id;

        $controller = app()->make('App\Http\Controllers\Admin\AdminManageJobdeskController');
        $response = $controller->destroy($jobDeskToDelete);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseMissing('job_desks', [
            'id' => $jobDeskId,
        ]);
    }

    // ============================================
    // REVIEW CONTROLLER TESTS
    // ============================================

    public function test_director_dapat_melihat_pending_reviews()
    {
        $this->actingAs($this->directorUser);

        // Simpan status awal
        $originalStatus = $this->employeeJobDesk->status;
        $originalKadivRating = $this->employeeJobDesk->kadiv_rating;

        // Update assignment status to pending review
        $this->employeeJobDesk->update([
            'status' => 'in_review_director',
            'completed_at' => now(),
            'kadiv_rating' => 3,
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminReviewController');
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.reviews.index', $response->getName());
        $this->assertArrayHasKey('pendingReviews', $response->getData());

        // Kembalikan ke status awal
        $this->employeeJobDesk->update([
            'status' => $originalStatus,
            'kadiv_rating' => $originalKadivRating,
        ]);
    }

    public function test_director_dapat_approve_assignment()
    {
        $this->actingAs($this->directorUser);

        // Create temporary assignment for approval
        $tempJobDesk = JobDesk::create([
            'title' => 'Temp Job Desk ' . time(),
            'description' => 'Temp Description',
            'deadline' => Carbon::now()->addDays(7),
            'created_by' => $this->divisionHeadUser->id,
            'division_id' => $this->division->id,
        ]);

        $tempAssignment = EmployeeJobDesk::create([
            'job_desk_id' => $tempJobDesk->id,
            'employee_id' => $this->employeeUser->id,
            'status' => 'in_review_director',
            'kadiv_rating' => 3,
        ]);

        $request = new Request();
        $request->replace([
            'rating' => 4,
            'notes' => 'Good work test',
            'decision' => 'approve',
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminReviewController');
        $response = $controller->review($request, $tempAssignment);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('employee_job_desks', [
            'id' => $tempAssignment->id,
            'status' => 'final',
            'director_rating' => 4,
        ]);
    }

    public function test_director_dapat_reject_assignment()
    {
        $this->actingAs($this->directorUser);

        // Create temporary assignment for rejection
        $tempJobDesk = JobDesk::create([
            'title' => 'Temp Job Desk Reject ' . time(),
            'description' => 'Temp Description',
            'deadline' => Carbon::now()->addDays(7),
            'created_by' => $this->divisionHeadUser->id,
            'division_id' => $this->division->id,
        ]);

        $tempAssignment = EmployeeJobDesk::create([
            'job_desk_id' => $tempJobDesk->id,
            'employee_id' => $this->employeeUser->id,
            'status' => 'in_review_director',
            'kadiv_rating' => 3,
        ]);

        $request = new Request();
        $request->replace([
            'rating' => 2,
            'notes' => 'Needs improvement test',
            'decision' => 'reject',
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminReviewController');
        $response = $controller->review($request, $tempAssignment);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('employee_job_desks', [
            'id' => $tempAssignment->id,
            'status' => 'rejected_director',
        ]);
    }

    public function test_director_dapat_bulk_approve_assignments()
    {
        $this->actingAs($this->directorUser);

        // Create temporary assignments
        $tempJobDesk = JobDesk::create([
            'title' => 'Temp Job Desk Bulk ' . time(),
            'description' => 'Temp Description',
            'deadline' => Carbon::now()->addDays(7),
            'created_by' => $this->divisionHeadUser->id,
            'division_id' => $this->division->id,
        ]);

        $assignment1 = EmployeeJobDesk::create([
            'job_desk_id' => $tempJobDesk->id,
            'employee_id' => $this->employeeUser->id,
            'status' => 'in_review_director',
            'kadiv_rating' => 3,
        ]);

        $assignment2 = EmployeeJobDesk::create([
            'job_desk_id' => $tempJobDesk->id,
            'employee_id' => $this->employeeUser->id,
            'status' => 'in_review_director',
            'kadiv_rating' => 3,
        ]);

        $request = new Request();
        $request->replace([
            'assignment_ids' => [$assignment1->id, $assignment2->id],
            'bulk_rating' => 3,
            'bulk_notes' => 'Bulk approval test',
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminReviewController');
        $response = $controller->bulkApprove($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('employee_job_desks', [
            'id' => $assignment1->id,
            'status' => 'final',
        ]);
        $this->assertDatabaseHas('employee_job_desks', [
            'id' => $assignment2->id,
            'status' => 'final',
        ]);
    }

    // ============================================
    // PERFORMANCE CONTROLLER TESTS
    // ============================================

    public function test_admin_dapat_melihat_employee_performance()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\AdminEmployeePerformanceController');
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.performances.index', $response->getName());
        $this->assertArrayHasKey('employees', $response->getData());
    }

    public function test_admin_dapat_melihat_detail_performance_employee()
    {
        $this->actingAs($this->adminUser);

        // Simpan status awal
        $originalStatus = $this->employeeJobDesk->status;
        $originalKadivRating = $this->employeeJobDesk->kadiv_rating;
        $originalDirectorRating = $this->employeeJobDesk->director_rating;

        // Create completed assignment with ratings
        $this->employeeJobDesk->update([
            'status' => 'final',
            'kadiv_rating' => 3,
            'director_rating' => 4,
            'director_reviewed_at' => now(),
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminEmployeePerformanceController');
        $response = $controller->show($this->employeeUser->id);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.performances.show', $response->getName());
        $this->assertArrayHasKey('employee', $response->getData());
        $this->assertArrayHasKey('performanceScore', $response->getData());

        // Kembalikan ke status awal
        $this->employeeJobDesk->update([
            'status' => $originalStatus,
            'kadiv_rating' => $originalKadivRating,
            'director_rating' => $originalDirectorRating,
        ]);
    }

    public function test_admin_dapat_propose_promotion()
    {
        $this->actingAs($this->adminUser);

        // Simpan status awal
        $originalStatus = $this->employeeJobDesk->status;
        $originalKadivRating = $this->employeeJobDesk->kadiv_rating;
        $originalDirectorRating = $this->employeeJobDesk->director_rating;

        $this->employeeJobDesk->update([
            'status' => 'final',
            'kadiv_rating' => 4,
            'director_rating' => 4,
            'director_reviewed_at' => now(),
        ]);

        $request = new Request();
        $request->replace([
            'period_start' => Carbon::now()->subMonths(6)->format('Y-m-d'),
            'period_end' => Carbon::now()->format('Y-m-d'),
            'reason' => 'Excellent performance over the past 6 months test',
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminEmployeePerformanceController');
        $response = $controller->storePromotion($request, $this->employeeUser->id);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('promotion_requests', [
            'employee_id' => $this->employeeUser->id,
            'status' => 'pending',
        ]);

        // Kembalikan ke status awal
        $this->employeeJobDesk->update([
            'status' => $originalStatus,
            'kadiv_rating' => $originalKadivRating,
            'director_rating' => $originalDirectorRating,
        ]);
    }

    public function test_admin_dapat_melihat_performance_report()
    {
        $this->actingAs($this->adminUser);

        $controller = app()->make('App\Http\Controllers\Admin\AdminEmployeePerformanceController');
        $response = $controller->report(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.performances.report', $response->getName());
        $this->assertArrayHasKey('performanceStats', $response->getData());
    }

    public function test_admin_dapat_compare_employee_performance()
    {
        $this->actingAs($this->adminUser);

        $request = new Request();
        $request->replace([
            'employee_ids' => [$this->employeeUser->id],
        ]);

        $controller = app()->make('App\Http\Controllers\Admin\AdminEmployeePerformanceController');
        $response = $controller->compare($request);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('admin.performances.compare', $response->getName());
        $this->assertArrayHasKey('selectedEmployees', $response->getData());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}