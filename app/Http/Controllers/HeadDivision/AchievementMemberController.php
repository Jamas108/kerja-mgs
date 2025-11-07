<?php

namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AchievementMemberController extends Controller
{
    /**
     * Display a list of achievements (certificates) requested by the head division
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get current authenticated head division ID
        $headDivisionId = Auth::id();

        // Get promotion requests made by this head division
        $query = PromotionRequest::where('requested_by', $headDivisionId)
            ->with(['employee', 'employee.division'])
            ->orderBy('reviewed_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Get achievements with pagination
        $achievements = $query->paginate(9);

        // Get count by status for the filter tabs
        $statusCounts = [
            'all' => PromotionRequest::where('requested_by', $headDivisionId)->count(),
            'pending' => PromotionRequest::where('requested_by', $headDivisionId)->where('status', 'pending')->count(),
            'approved' => PromotionRequest::where('requested_by', $headDivisionId)->where('status', 'approved')->count(),
            'rejected' => PromotionRequest::where('requested_by', $headDivisionId)->where('status', 'rejected')->count(),
        ];

        return view('head_division.achievements.index', compact('achievements', 'statusCounts'));
    }

    /**
     * Display a specific certificate
     *
     * @param PromotionRequest $achievement
     * @return \Illuminate\View\View
     */
    public function show(PromotionRequest $achievement)
    {
        // Make sure the certificate was requested by the authenticated head division
        if ($achievement->requested_by !== Auth::id()) {
            return redirect()->route('head_division.achievements.index')
                ->with('error', 'You do not have permission to view this certificate.');
        }

        // Load employee and director relationship
        $achievement->load(['employee', 'employee.division']);

        // Get the director (approver) details
        $director = null;
        if ($achievement->reviewed_at) {
            $director = User::where('role_id', 'direktur')->first();
        }

        return view('head_division.achievements.show', compact('achievement', 'director'));
    }

    /**
     * Download a certificate
     *
     * @param PromotionRequest $achievement
     * @return \Illuminate\Http\Response
     */
    public function download(PromotionRequest $achievement)
    {
        // Make sure the certificate was requested by the authenticated head division
        if ($achievement->requested_by !== Auth::id()) {
            return redirect()->route('head_division.achievements.index')
                ->with('error', 'You do not have permission to download this certificate.');
        }

        // Make sure the promotion is approved and has a certificate
        if ($achievement->status !== 'approved' || !$achievement->certificate_file) {
            return redirect()->back()->with('error', 'Certificate not available.');
        }

        if (Storage::disk('public')->exists($achievement->certificate_file)) {
            return Storage::disk('public')->download(
                $achievement->certificate_file,
                'Certificate_' . $achievement->employee->name . '_' . date('Y-m-d') . '.pdf'
            );
        }

        return redirect()->back()->with('error', 'Certificate file not found.');
    }
}