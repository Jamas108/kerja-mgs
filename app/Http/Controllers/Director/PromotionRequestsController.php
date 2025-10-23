<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use App\Models\EmployeeJobDesk;
use App\Models\User;
use App\Models\Signature;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PromotionRequestsController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index()
    {
        $pendingRequests = PromotionRequest::where('status', 'pending')
            ->with(['employee', 'employee.division', 'requester'])
            ->latest()
            ->get();

        $processedRequests = PromotionRequest::whereIn('status', ['approved', 'rejected'])
            ->with(['employee', 'employee.division', 'requester'])
            ->latest()
            ->take(10)
            ->get();

        return view('director.promotion_requests.index', compact('pendingRequests', 'processedRequests'));
    }

    public function show(PromotionRequest $promotionRequest)
    {
        $employee = $promotionRequest->employee;

        // Get employee performance data
        $performanceScore = $this->calculateOverallPerformance($employee->id);
        $performanceCategory = $performanceScore ? $this->getPerformanceCategory($performanceScore) : 'Belum Ada Penilaian';

        // Get recent assignments
        $recentAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
            ->where('status', 'final')
            ->with('jobDesk')
            ->latest('director_reviewed_at')
            ->take(5)
            ->get();

        // Monthly performance
        $monthlyPerformance = $this->getMonthlyPerformance($employee->id);

        // Get active signatures for preview
        $activeSignatures = Signature::active()
            ->directors()
            ->with('user')
            ->get();

        return view('director.promotion_requests.show', compact(
            'promotionRequest',
            'employee',
            'performanceScore',
            'performanceCategory',
            'recentAssignments',
            'monthlyPerformance',
            'activeSignatures'
        ));
    }

    public function approve(Request $request, PromotionRequest $promotionRequest)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'signature_id' => 'required|exists:signatures,id'
        ]);

        try {
            DB::beginTransaction();

            // Get the selected signature
            $signature = Signature::findOrFail($request->signature_id);

            // Generate certificate PDF with selected signature
            $certificatePath = $this->certificateService->generatePromotionCertificateWithSignature(
                $promotionRequest,
                $signature
            );

            if (!$certificatePath) {
                throw new \Exception('Gagal menghasilkan sertifikat. Silakan coba lagi.');
            }

            // Update promotion request
            $promotionRequest->status = 'approved';
            $promotionRequest->director_notes = $request->notes;
            $promotionRequest->certificate_file = $certificatePath;
            $promotionRequest->reviewed_at = now();
            $promotionRequest->save();

            DB::commit();

            return redirect()->route('director.promotion_requests.index')
                ->with('success', 'Pengajuan promosi berhasil disetujui. Sertifikat telah digenerate otomatis.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PromotionRequest $promotionRequest)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        $promotionRequest->status = 'rejected';
        $promotionRequest->director_notes = $request->notes;
        $promotionRequest->reviewed_at = now();
        $promotionRequest->save();

        return redirect()->route('director.promotion_requests.index')
            ->with('success', 'Pengajuan promosi telah ditolak.');
    }

    public function downloadCertificate(PromotionRequest $promotionRequest)
    {
        if ($promotionRequest->status !== 'approved' || !$promotionRequest->certificate_file) {
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan.');
        }

        if (Storage::disk('public')->exists($promotionRequest->certificate_file)) {
            return Storage::disk('public')->download(
                $promotionRequest->certificate_file,
                'Sertifikat_Promosi_' . $promotionRequest->employee->name . '.pdf'
            );
        }

        return redirect()->back()->with('error', 'File sertifikat tidak ditemukan.');
    }

    public function previewCertificate(Request $request)
    {
        $request->validate([
            'promotion_request_id' => 'required|exists:promotion_requests,id',
            'signature_id' => 'required|exists:signatures,id'
        ]);

        $promotionRequest = PromotionRequest::findOrFail($request->promotion_request_id);
        $signature = Signature::findOrFail($request->signature_id);

        // Generate certificate HTML preview
        $certificateHtml = $this->certificateService->generateCertificateHtmlPreview($promotionRequest, $signature);

        return response($certificateHtml);
    }

    private function calculateOverallPerformance($employeeId)
    {
        $completedAssignments = EmployeeJobDesk::where('employee_id', $employeeId)
            ->where('status', 'final')
            ->get();

        $totalScore = 0;
        $totalRatedAssignments = $completedAssignments->count();

        if ($totalRatedAssignments > 0) {
            foreach ($completedAssignments as $assignment) {
                $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
                $totalScore += $avgRating;
            }

            return round($totalScore / $totalRatedAssignments, 2);
        }

        return null;
    }

    private function getPerformanceCategory($score)
    {
        if ($score >= 3.7) {
            return 'Sangat Baik';
        } else if ($score >= 3) {
            return 'Baik';
        } else if ($score >= 2.5) {
            return 'Cukup';
        } else if ($score >= 2) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    private function getMonthlyPerformance($employeeId)
    {
        $monthlyData = EmployeeJobDesk::where('employee_id', $employeeId)
            ->where('status', 'final')
            ->select(
                DB::raw('MONTH(director_reviewed_at) as month'),
                DB::raw('YEAR(director_reviewed_at) as year'),
                DB::raw('AVG((kadiv_rating + director_rating) / 2) as avg_score'),
                DB::raw('COUNT(*) as total_tasks')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $formattedData = [];

        foreach ($monthlyData as $data) {
            $monthName = date('F', mktime(0, 0, 0, $data->month, 1));
            $formattedData[] = [
                'period' => $monthName . ' ' . $data->year,
                'average_score' => round($data->avg_score, 2),
                'total_tasks' => $data->total_tasks
            ];
        }

        return $formattedData;
    }
}