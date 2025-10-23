<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Role;
use App\Models\EmployeeJobDesk;
use App\Models\JobDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pastikan relasi di-eager load untuk menghindari N+1 query problem
        $users = User::with(['role', 'division'])->get();
        $roles = Role::all();
        $divisions = Division::all();
        return view('admin.users.index', compact('users', 'roles', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua role dan division untuk dropdown
        $roles = Role::all();
        $divisions = Division::all();
        return view('admin.users.create', compact('roles', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        // Validasi tambahan - karyawan dan kepala divisi harus memiliki divisi
        $selectedRole = Role::find($request->role_id);
        if (($selectedRole->name === 'karyawan' || $selectedRole->name === 'kepala divisi') && !$request->division_id) {
            return back()
                ->withInput()
                ->withErrors(['division_id' => 'Division is required for employees and division heads.']);
        }

        // Buat user baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'division_id' => $request->division_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $divisions = Division::all();

        // Load additional statistics for user based on their role
        if ($user->isEmployee()) {
            // Get tasks statistics for employees
            $completedTasks = $user->assignedJobs()->where('status', 'final')->count();
            $pendingTasks = $user->assignedJobs()->whereNotIn('status', ['final'])->count();
            $rejectedTasks = $user->assignedJobs()->whereIn('status', ['rejected_kadiv', 'rejected_director'])->count();
            $avgRating = $user->assignedJobs()->where('status', 'final')->avg('kadiv_rating');
            $directorAvgRating = $user->assignedJobs()->where('status', 'final')->avg('director_rating');

            return view('admin.users.edit', compact(
                'user',
                'roles',
                'divisions',
                'completedTasks',
                'pendingTasks',
                'rejectedTasks',
                'avgRating',
                'directorAvgRating'
            ));
        } elseif ($user->isDivisionHead()) {
            // Get statistics for division heads
            $totalJobDesks = JobDesk::where('created_by', $user->id)->count();
            $reviewedTasks = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                $query->where('division_id', $user->division_id);
            })->whereNotNull('kadiv_rating')->count();
            $pendingReviews = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                $query->where('division_id', $user->division_id);
            })->where('status', 'completed')->count();
            $avgRating = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($user) {
                $query->where('division_id', $user->division_id);
            })->whereNotNull('kadiv_rating')->avg('kadiv_rating');

            return view('admin.users.edit', compact(
                'user',
                'roles',
                'divisions',
                'totalJobDesks',
                'reviewedTasks',
                'pendingReviews',
                'avgRating'
            ));
        } elseif ($user->isDirector()) {
            // Get statistics for directors
            $reviewedTasks = EmployeeJobDesk::whereNotNull('director_rating')->count();
            $pendingReviews = EmployeeJobDesk::where('status', 'in_review_director')->count();
            $completedTasks = EmployeeJobDesk::where('status', 'final')->count();
            $avgRating = EmployeeJobDesk::whereNotNull('director_rating')->avg('director_rating');

            return view('admin.users.edit', compact(
                'user',
                'roles',
                'divisions',
                'reviewedTasks',
                'pendingReviews',
                'completedTasks',
                'avgRating'
            ));
        } else {
            // Just basic info for admin
            return view('admin.users.edit', compact('user', 'roles', 'divisions'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        // Validasi tambahan - karyawan dan kepala divisi harus memiliki divisi
        $selectedRole = Role::find($request->role_id);
        if (($selectedRole->name === 'karyawan' || $selectedRole->name === 'kepala divisi') && !$request->division_id) {
            return back()
                ->withInput()
                ->withErrors(['division_id' => 'Division is required for employees and division heads.']);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'division_id' => $request->division_id,
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Cek jika user memiliki jobdesk atau review yang belum selesai
        $hasActiveJobs = false;

        if ($user->isEmployee()) {
            $hasActiveJobs = $user->assignedJobs()->whereNotIn('status', ['final'])->exists();
        } elseif ($user->isDivisionHead()) {
            $hasActiveJobs = JobDesk::where('created_by', $user->id)
                ->whereHas('assignments', function($query) {
                    $query->whereNotIn('status', ['final']);
                })
                ->exists();
        }

        if ($hasActiveJobs) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user with active jobs or reviews. Complete or reassign them first.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}