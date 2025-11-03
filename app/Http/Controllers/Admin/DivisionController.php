<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use App\Models\EmployeeJobDesk;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->get();
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('admin.divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        Division::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.divisions.index')->with('success', 'Division created successfully');
    }

    public function show(Division $division)
    {
        // Get division head
        $divisionHead = $division->divisionHead();

        // Get employees with task counts
        $employees = User::where('division_id', $division->id)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->withCount([
                'assignedJobs',
                'assignedJobs as completed_tasks_count' => function($query) {
                    $query->where('status', 'final');
                }
            ])
            ->get();

        // Count total employees
        $totalEmployees = $employees->count();

        // Get tasks statistics
        $totalTasks = $division->jobDesks()->count();

        $completedTasks = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
            $query->where('division_id', $division->id);
        })->where('status', 'final')->count();

        // Calculate average rating
        $avgRating = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($division) {
            $query->where('division_id', $division->id);
        })->where('status', 'final')
          ->whereNotNull('kadiv_rating')
          ->whereNotNull('director_rating')
          ->selectRaw('AVG((kadiv_rating + director_rating) / 2) as avg_rating')
          ->value('avg_rating');

        // Get recent job desks
        $recentJobDesks = $division->jobDesks()
            ->with(['creator', 'assignments'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.divisions.show', compact(
            'division',
            'divisionHead',
            'employees',
            'totalEmployees',
            'totalTasks',
            'completedTasks',
            'avgRating',
            'recentJobDesks'
        ));
    }

    public function edit(Division $division)
    {
        // Simplified edit method that only passes the division
        return view('admin.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
        ]);

        $division->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.divisions.show', $division)->with('success', 'Division updated successfully');
    }

    public function destroy(Division $division)
    {
        // Check if there are users in this division
        if ($division->users()->count() > 0) {
            return redirect()->route('admin.divisions.index')->with('error', 'Cannot delete division with associated users');
        }

        $division->delete();

        return redirect()->route('admin.divisions.index')->with('success', 'Division deleted successfully');
    }
}