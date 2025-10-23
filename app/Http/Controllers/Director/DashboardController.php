<?php

// 3. Controller untuk Direktur
// app/Http/Controllers/Director/DashboardController.php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\JobDesk;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount(['users' => function($query) {
                $query->where('role_id', 1); // karyawan role
            }])
            ->get();

        $totalEmployees = User::where('role_id', 1)->count();
        $totalTasks = JobDesk::count();
        $completedTasks = EmployeeJobDesk::where('status', 'final')->count();
        $pendingReviews = EmployeeJobDesk::where('status', 'in_review_director')->count();

        return view('director.dashboard', compact(
            'divisions',
            'totalEmployees',
            'totalTasks',
            'completedTasks',
            'pendingReviews'
        ));
    }
}
