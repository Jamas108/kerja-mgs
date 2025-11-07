<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    /**
     * Display a list of employee achievements (certificates)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get current authenticated employee ID
        $employeeId = Auth::id();

        // Get approved promotion requests with certificates
        $achievements = PromotionRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereNotNull('certificate_file')
            ->with('requester')  // Include the relationship to get requester details
            ->orderBy('reviewed_at', 'desc')
            ->get();

        return view('employee.achievements.index', compact('achievements'));
    }

    /**
     * Display a specific certificate
     *
     * @param PromotionRequest $promotionRequest
     * @return \Illuminate\View\View
     */
    public function show(PromotionRequest $achievement)
    {
        // Make sure the certificate belongs to the authenticated user
        if ($achievement->employee_id !== Auth::id()) {
            return redirect()->route('employee.achievements.index')
                ->with('error', 'You do not have permission to view this certificate.');
        }

        // Make sure the promotion is approved and has a certificate
        if ($achievement->status !== 'approved' || !$achievement->certificate_file) {
            return redirect()->route('employee.achievements.index')
                ->with('error', 'Certificate not found.');
        }

        return view('employee.achievements.show', compact('achievement'));
    }

    /**
     * Download a certificate
     *
     * @param PromotionRequest $promotionRequest
     * @return \Illuminate\Http\Response
     */
    public function download(PromotionRequest $achievement)
    {
        // Make sure the certificate belongs to the authenticated user
        if ($achievement->employee_id !== Auth::id()) {
            return redirect()->route('employee.achievements.index')
                ->with('error', 'You do not have permission to download this certificate.');
        }

        // Make sure the promotion is approved and has a certificate
        if ($achievement->status !== 'approved' || !$achievement->certificate_file) {
            return redirect()->back()->with('error', 'Certificate not found.');
        }

        if (Storage::disk('public')->exists($achievement->certificate_file)) {
            return Storage::disk('public')->download(
                $achievement->certificate_file,
                'Certificate_' . Auth::user()->name . '_' . date('Y-m-d') . '.pdf'
            );
        }

        return redirect()->back()->with('error', 'Certificate file not found.');
    }
}