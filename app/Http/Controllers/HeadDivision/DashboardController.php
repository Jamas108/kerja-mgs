<?php
namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\JobDesk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $employeesCount = User::where('division_id', $divisionId)
            ->where('role_id', 1) // karyawan role
            ->count();

        $jobDesksCount = JobDesk::where('division_id', $divisionId)->count();

        $waitingForReview = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'completed')
            ->count();

        $approvedByKadiv = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->whereIn('status', ['kadiv_approved', 'in_review_director'])
            ->count();

        $rejectedTasks = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'rejected_kadiv')
            ->count();

        $finishedTasks = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'final')
            ->count();

        return view('head_division.dashboard', compact(
            'employeesCount',
            'jobDesksCount',
            'waitingForReview',
            'approvedByKadiv',
            'rejectedTasks',
            'finishedTasks'
        ));
    }
}