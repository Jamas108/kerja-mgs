<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pendingTasks = EmployeeJobDesk::where('employee_id', $user->id)
            ->where('status', 'assigned')
            ->with('jobDesk')
            ->get();

        $completedTasks = EmployeeJobDesk::where('employee_id', $user->id)
            ->whereIn('status', ['completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director'])
            ->with('jobDesk')
            ->get();

        $rejectedTasks = EmployeeJobDesk::where('employee_id', $user->id)
            ->whereIn('status', ['rejected_kadiv', 'rejected_director'])
            ->with('jobDesk')
            ->get();

        $finishedTasks = EmployeeJobDesk::where('employee_id', $user->id)
            ->where('status', 'final')
            ->with('jobDesk')
            ->get();

        return view('employee.dashboard', compact(
            'pendingTasks',
            'completedTasks',
            'rejectedTasks',
            'finishedTasks'
        ));
    }
}
