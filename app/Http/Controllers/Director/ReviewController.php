<?php
namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $pendingReviews = EmployeeJobDesk::where('status', 'in_review_director')
            ->with(['employee', 'jobDesk', 'jobDesk.division'])
            ->get();

        return view('director.reviews.index', compact('pendingReviews'));
    }

    public function show(EmployeeJobDesk $assignment)
    {
        return view('director.reviews.show', compact('assignment'));
    }

    public function review(Request $request, EmployeeJobDesk $assignment)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:4',
            'notes' => 'nullable|string',
            'decision' => 'required|in:approve,reject',
        ]);

        $status = $request->decision === 'approve' ? 'final' : 'rejected_director';

        $assignment->update([
            'director_rating' => $request->rating,
            'director_notes' => $request->notes,
            'director_reviewed_at' => now(),
            'status' => $status,
        ]);

        return redirect()->route('director.reviews.index')
            ->with('success', 'Review submitted successfully');
    }
}
