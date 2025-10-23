<?php
namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\JobDesk;
use App\Models\EmployeeJobDesk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobDeskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $jobDesks = JobDesk::where('division_id', $divisionId)
            ->with('assignments.employee')
            ->get();

        return view('head_division.job_desks.index', compact('jobDesks'));
    }

    public function create()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $employees = User::where('division_id', $divisionId)
            ->where('role_id', 1) // karyawan role
            ->get();

        return view('head_division.job_desks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:today',
            'employees' => 'required|array',
            'employees.*' => 'exists:users,id',
        ]);

        $user = Auth::user();

        $jobDesk = JobDesk::create([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'created_by' => $user->id,
            'division_id' => $user->division_id,
        ]);

        // Assign to selected employees
        foreach ($request->employees as $employeeId) {
            EmployeeJobDesk::create([
                'job_desk_id' => $jobDesk->id,
                'employee_id' => $employeeId,
                'status' => 'assigned',
            ]);
        }

        return redirect()->route('head_division.job_desks.index')
            ->with('success', 'Job desk created and assigned successfully');
    }

    public function edit(JobDesk $jobDesk)
    {
        $user = Auth::user();

        // Check if job desk belongs to the same division
        if ($jobDesk->division_id !== $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        $divisionId = $user->division_id;
        $employees = User::where('division_id', $divisionId)
            ->where('role_id', 1) // karyawan role
            ->get();

        $assignedEmployees = $jobDesk->assignments->pluck('employee_id')->toArray();

        return view('head_division.job_desks.edit', compact('jobDesk', 'employees', 'assignedEmployees'));
    }

    public function update(Request $request, JobDesk $jobDesk)
    {
        $user = Auth::user();

        // Check if job desk belongs to the same division
        if ($jobDesk->division_id !== $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'employees' => 'required|array',
            'employees.*' => 'exists:users,id',
        ]);

        $jobDesk->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        // Get current assignments
        $currentAssignments = $jobDesk->assignments->pluck('employee_id')->toArray();

        // Remove assignments that are no longer selected
        $toRemove = array_diff($currentAssignments, $request->employees);
        if (!empty($toRemove)) {
            EmployeeJobDesk::where('job_desk_id', $jobDesk->id)
                ->whereIn('employee_id', $toRemove)
                ->delete();
        }

        // Add new assignments
        $toAdd = array_diff($request->employees, $currentAssignments);
        foreach ($toAdd as $employeeId) {
            EmployeeJobDesk::create([
                'job_desk_id' => $jobDesk->id,
                'employee_id' => $employeeId,
                'status' => 'assigned',
            ]);
        }

        return redirect()->route('head_division.job_desks.index')
            ->with('success', 'Job desk updated successfully');
    }

    public function destroy(JobDesk $jobDesk)
    {
        $user = Auth::user();

        // Check if job desk belongs to the same division
        if ($jobDesk->division_id !== $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if any assignments are already completed or reviewed
        $nonDeletableStatuses = [
            'completed',
            'in_review_kadiv',
            'kadiv_approved',
            'in_review_director',
            'final'
        ];

        $hasNonDeletable = $jobDesk->assignments()
            ->whereIn('status', $nonDeletableStatuses)
            ->exists();

        if ($hasNonDeletable) {
            return redirect()->route('head_division.job_desks.index')
                ->with('error', 'Cannot delete job desk with completed or reviewed assignments');
        }

        $jobDesk->delete();

        return redirect()->route('head_division.job_desks.index')
            ->with('success', 'Job desk deleted successfully');
    }
}