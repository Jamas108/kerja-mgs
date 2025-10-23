<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\JobDesk;
use App\Models\User;
use App\Models\Division;
use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $finalTasks = EmployeeJobDesk::where('status', 'final')->get();
        $users = User::count();
        $employees = User::where('role_id', 1)->count();
        $pendingTasks = EmployeeJobDesk::whereNotIn('status', ['final'])->count();

        return view('admin.dashboard', compact('finalTasks', 'users', 'employees', 'pendingTasks'));
    }
}
