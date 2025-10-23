<?php

// app/Http/Controllers/Employee/TaskController.php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $assignments = EmployeeJobDesk::where('employee_id', $user->id)
            ->with('jobDesk')
            ->get();

        return view('employee.tasks.index', compact('assignments'));
    }

    public function show(EmployeeJobDesk $assignment)
    {
        $user = Auth::user();

        // Check if assignment belongs to the current user
        if ($assignment->employee_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('employee.tasks.show', compact('assignment'));
    }

    public function complete(Request $request, EmployeeJobDesk $assignment)
    {
        $user = Auth::user();

        // Check if assignment belongs to the current user
        if ($assignment->employee_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if assignment is in a valid state to be completed
        if (!in_array($assignment->status, ['assigned', 'rejected_kadiv', 'rejected_director'])) {
            return redirect()->route('employee.tasks.show', $assignment)
                ->with('error', 'This task cannot be completed at this time');
        }

        $request->validate([
            'evidence_note' => 'required|string',
            'evidence_file' => 'required|file', // Max 10MB
        ]);

        // Store file
        $file = $request->file('evidence_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('evidence', $filename, 'public');

        $assignment->update([
            'evidence_file' => $filePath,
            'evidence_note' => $request->evidence_note,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        return redirect()->route('employee.tasks.index')
            ->with('success', 'Task marked as completed and submitted for review');
    }
}
