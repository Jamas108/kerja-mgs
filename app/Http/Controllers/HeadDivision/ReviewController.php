<?php
namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $pendingReviews = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'completed')
            ->with(['employee', 'jobDesk'])
            ->get();

        $rejectedByDirector = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'rejected_director')
            ->with(['employee', 'jobDesk'])
            ->get();

        return view('head_division.reviews.index', compact('pendingReviews', 'rejectedByDirector'));
    }

    public function show(EmployeeJobDesk $assignment)
    {
        $user = Auth::user();

        // Check if assignment belongs to the same division
        if ($assignment->jobDesk->division_id !== $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('head_division.reviews.show', compact('assignment'));
    }

    public function review(Request $request, EmployeeJobDesk $assignment)
    {
        $user = Auth::user();

        // Check if assignment belongs to the same division
        if ($assignment->jobDesk->division_id !== $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:4',
            'notes' => 'nullable|string',
            'decision' => 'required|in:approve,reject',
        ]);

        $status = $request->decision === 'approve' ? 'kadiv_approved' : 'rejected_kadiv';

        $assignment->update([
            'kadiv_rating' => $request->rating,
            'kadiv_notes' => $request->notes,
            'kadiv_reviewed_at' => now(),
            'status' => $status,
        ]);

        // If approved, immediately set for director review
        if ($status === 'kadiv_approved') {
            $assignment->update([
                'status' => 'in_review_director',
            ]);
        }

        return redirect()->route('head_division.reviews.index')
            ->with('success', 'Review submitted successfully');
    }
}