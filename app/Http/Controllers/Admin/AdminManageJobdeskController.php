<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobDesk;
use App\Models\EmployeeJobDesk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminManageJobdeskController extends Controller
{
    public function index()
    {
        // Admin melihat semua jobdesk
        $jobDesks = JobDesk::with('assignments.employee')->get();

        return view('admin.job_desks.index', compact('jobDesks'));
    }

    public function create()
    {
        // Ambil semua karyawan dari semua divisi
        $employees = User::where('role_id', 1)->get();

        return view('admin.job_desks.create', compact('employees'));
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

        // Ambil division_id berdasarkan karyawan pertama
        $firstEmployee = User::find($request->employees[0]);

        $jobDesk = JobDesk::create([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'created_by' => $user->id,
            'division_id' => $firstEmployee->division_id ?? null,
        ]);

        // Assign ke karyawan terpilih
        foreach ($request->employees as $employeeId) {
            EmployeeJobDesk::create([
                'job_desk_id' => $jobDesk->id,
                'employee_id' => $employeeId,
                'status' => 'assigned',
            ]);
        }

        return redirect()->route('admin.manage_job_desks.index')
            ->with('success', 'Job desk created and assigned successfully');
    }

    public function edit(JobDesk $manage_job_desk)
    {
        // Admin bisa edit semua jobdesk
        $employees = User::where('role_id', 1)->get();
        $assignedEmployees = $manage_job_desk->assignments->pluck('employee_id')->toArray();

        return view('admin.job_desks.edit', compact('manage_job_desk', 'employees', 'assignedEmployees'));
    }

    public function update(Request $request, JobDesk $manage_job_desk)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'employees' => 'required|array',
            'employees.*' => 'exists:users,id',
        ]);

        $manage_job_desk->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        $currentAssignments = $manage_job_desk->assignments->pluck('employee_id')->toArray();

        // Hapus yang tidak dipilih lagi
        $toRemove = array_diff($currentAssignments, $request->employees);
        if (!empty($toRemove)) {
            EmployeeJobDesk::where('job_desk_id', $manage_job_desk->id)
                ->whereIn('employee_id', $toRemove)
                ->delete();
        }

        // Tambah yang baru dipilih
        $toAdd = array_diff($request->employees, $currentAssignments);
        foreach ($toAdd as $employeeId) {
            EmployeeJobDesk::create([
                'job_desk_id' => $manage_job_desk->id,
                'employee_id' => $employeeId,
                'status' => 'assigned',
            ]);
        }

        return redirect()->route('admin.manage_job_desks.index')
            ->with('success', 'Job desk updated successfully');
    }

    public function destroy(JobDesk $manage_job_desk)
    {
        try {
            // Hapus langsung tanpa memeriksa status
            // Terlebih dahulu hapus semua assignments yang terkait
            $manage_job_desk->assignments()->delete();

            // Kemudian hapus job desk itu sendiri
            $manage_job_desk->delete();

            return redirect()->route('admin.manage_job_desks.index')
                ->with('success', 'Tugas berhasil dihapus beserta semua data terkait');
        } catch (\Exception $e) {
            return redirect()->route('admin.manage_job_desks.index')
                ->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }
}
