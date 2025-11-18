<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\Division;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        // Get pending reviews for director
        $pendingReviewsQuery = EmployeeJobDesk::where('status', 'in_review_director')
            ->with(['employee', 'jobDesk', 'jobDesk.division']);

        // Get rejected by director
        $rejectedByDirectorQuery = EmployeeJobDesk::where('status', 'rejected_director')
            ->with(['employee', 'jobDesk', 'jobDesk.division']);

        // Apply division filter if provided
        if ($request->filled('division_id')) {
            $pendingReviewsQuery->whereHas('jobDesk', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });

            $rejectedByDirectorQuery->whereHas('jobDesk', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Apply employee search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $pendingReviewsQuery->where(function ($q) use ($searchTerm) {
                $q->whereHas('employee', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhereHas('jobDesk', function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%');
                });
            });

            $rejectedByDirectorQuery->where(function ($q) use ($searchTerm) {
                $q->whereHas('employee', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhereHas('jobDesk', function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $pendingReviews = $pendingReviewsQuery->orderBy('completed_at', 'asc')->get();
        $rejectedByDirector = $rejectedByDirectorQuery->orderBy('updated_at', 'desc')->get();

        // Get divisions for filter
        $divisions = Division::select('id', 'name')->orderBy('name')->get();

        return view('admin.reviews.index', compact(
            'pendingReviews',
            'rejectedByDirector',
            'divisions'
        ));
    }

    public function show(EmployeeJobDesk $assignment)
    {
        $assignment->load(['employee', 'jobDesk', 'jobDesk.division']);
        return view('admin.reviews.show', compact('assignment'));
    }

    public function review(Request $request, EmployeeJobDesk $assignment)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:4',
            'notes' => 'nullable|string|max:1000',
            'decision' => 'required|in:approve,reject',
        ]);

        $status = $request->decision === 'approve' ? 'final' : 'rejected_director';

        $assignment->update([
            'director_rating' => $request->rating,
            'director_notes' => $request->notes,
            'director_reviewed_at' => now(),
            'status' => $status,
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review submitted successfully');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:employee_job_desks,id',
            'bulk_rating' => 'required|integer|min:1|max:4',
            'bulk_notes' => 'nullable|string|max:1000',
        ]);

        EmployeeJobDesk::whereIn('id', $request->assignment_ids)
            ->where('status', 'in_review_director')
            ->update([
                'director_rating' => $request->bulk_rating,
                'director_notes' => $request->bulk_notes,
                'director_reviewed_at' => now(),
                'status' => 'final'
            ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', count($request->assignment_ids) . ' assignments approved successfully');
    }

    public function statistics(Request $request)
    {
        $query = EmployeeJobDesk::with(['jobDesk.division']);

        // Apply division filter if provided
        if ($request->filled('division_id')) {
            $query->whereHas('jobDesk', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalAssignments = $query->count();

        // Status statistics
        $statusStats = [
            'total' => $totalAssignments,
            'assigned' => (clone $query)->where('status', 'assigned')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'in_review_kadiv' => (clone $query)->where('status', 'in_review_kadiv')->count(),
            'in_review_director' => (clone $query)->where('status', 'in_review_director')->count(),
            'rejected_kadiv' => (clone $query)->where('status', 'rejected_kadiv')->count(),
            'rejected_director' => (clone $query)->where('status', 'rejected_director')->count(),
            'final' => (clone $query)->where('status', 'final')->count(),
        ];

        // Division statistics
        $divisionStats = Division::withCount([
            'jobDesks as total_assignments' => function ($query) use ($request) {
                $query->join('employee_job_desks', 'job_desks.id', '=', 'employee_job_desks.job_desk_id');
                if ($request->filled('date_from')) {
                    $query->whereDate('employee_job_desks.created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('employee_job_desks.created_at', '<=', $request->date_to);
                }
            },
            'jobDesks as completed_assignments' => function ($query) use ($request) {
                $query->join('employee_job_desks', 'job_desks.id', '=', 'employee_job_desks.job_desk_id')
                      ->where('employee_job_desks.status', 'final');
                if ($request->filled('date_from')) {
                    $query->whereDate('employee_job_desks.created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('employee_job_desks.created_at', '<=', $request->date_to);
                }
            }
        ])->get();

        // Monthly statistics for the last 12 months
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthQuery = EmployeeJobDesk::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            if ($request->filled('division_id')) {
                $monthQuery->whereHas('jobDesk', function ($q) use ($request) {
                    $q->where('division_id', $request->division_id);
                });
            }

            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'created' => (clone $monthQuery)->count(),
                'completed' => (clone $monthQuery)->where('status', 'final')->count(),
                'pending' => (clone $monthQuery)->whereIn('status', ['assigned', 'in_progress', 'completed', 'in_review_kadiv', 'in_review_director'])->count()
            ];
        }

        // Rating distribution
        $ratingStats = [
            'kadiv_avg' => EmployeeJobDesk::whereNotNull('kadiv_rating')->avg('kadiv_rating'),
            'director_avg' => EmployeeJobDesk::whereNotNull('director_rating')->avg('director_rating'),
            'rating_distribution' => [
                '1' => EmployeeJobDesk::where('director_rating', 1)->count(),
                '2' => EmployeeJobDesk::where('director_rating', 2)->count(),
                '3' => EmployeeJobDesk::where('director_rating', 3)->count(),
                '4' => EmployeeJobDesk::where('director_rating', 4)->count(),
            ]
        ];

        $divisions = Division::select('id', 'name')->orderBy('name')->get();

        return view('admin.reviews.statistics', compact(
            'statusStats',
            'divisionStats',
            'monthlyStats',
            'ratingStats',
            'divisions'
        ));
    }
}