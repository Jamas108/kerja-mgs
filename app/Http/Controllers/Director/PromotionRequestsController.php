<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\PromotionRequest;
use App\Models\EmployeeJobDesk;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PromotionRequestsController extends Controller
{
    public function approve(Request $request, PromotionRequest $promotionRequest)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'signature_id' => 'required|exists:signatures,id'
        ]);

        try {
            DB::beginTransaction();

            // Pastikan signature milik user yang sedang login
            $signature = Signature::where('id', $request->signature_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            Log::info("Starting certificate generation for promotion request #{$promotionRequest->id}");
            $startTime = microtime(true);

            // Generate PDF dengan data signature
            $pdf = PDF::loadView('templates.promotion_certificate', [
                'certificateNumber' => 'PROM/' . date('Y') . '/' . str_pad($promotionRequest->id, 4, '0', STR_PAD_LEFT),
                'employeeName' => strtoupper($promotionRequest->employee->name),
                'employeePosition' => $promotionRequest->employee->position ?? '-',
                'divisionName' => $promotionRequest->employee->division->name ?? '-',
                'yearsOfService' => $promotionRequest->period ?? 'Periode penilaian terkini',
                'currentDate' => now()->isoFormat('D MMMM YYYY'),
                'signatureImageUrl' => $this->getSignatureUrl($signature),
                'directorName' => Auth::user()->name,
                'directorTitle' => Auth::user()->position ?? 'Direktur',
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            // Save PDF
            $filename = 'certificates/promotion_' . $promotionRequest->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            $duration = round(microtime(true) - $startTime, 2);
            Log::info("Certificate generated in {$duration} seconds");

            // Update promotion request
            $promotionRequest->update([
                'status' => 'approved',
                'director_notes' => $request->notes,
                'certificate_file' => $filename,
                'reviewed_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('director.promotion_requests.index')
                ->with('success', "Pengajuan promosi berhasil disetujui. Sertifikat telah digenerate dalam {$duration} detik.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Certificate generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function previewCertificate(Request $request)
    {
        $request->validate([
            'promotion_request_id' => 'required|exists:promotion_requests,id',
            'signature_id' => 'required|exists:signatures,id'
        ]);

        try {
            $promotionRequest = PromotionRequest::findOrFail($request->promotion_request_id);

            // Pastikan signature milik user yang sedang login
            $signature = Signature::where('id', $request->signature_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $employee = $promotionRequest->employee;

            // Prepare data untuk preview
            $data = [
                'certificateNumber' => 'PROM/' . date('Y') . '/' . str_pad($promotionRequest->id, 4, '0', STR_PAD_LEFT),
                'employeeName' => strtoupper($employee->name),
                'employeePosition' => $employee->position ?? '-',
                'divisionName' => $employee->division->name ?? '-',
                'yearsOfService' => $promotionRequest->period ?? 'Periode penilaian terkini',
                'currentDate' => $this->getCurrentDate(),
                'signatureImageUrl' => $this->getSignatureUrl($signature),
                'directorName' => Auth::user()->name,
                'directorTitle' => Auth::user()->position ?? 'Direktur',
            ];

            // Return view langsung (HTML preview)
            return view('templates.promotion_certificate', $data);

        } catch (\Exception $e) {
            Log::error('Preview generation failed: ' . $e->getMessage());

            return response()
                ->json(['error' => 'Gagal memuat preview: ' . $e->getMessage()], 500);
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get formatted current date in Indonesian
     */
    private function getCurrentDate()
    {
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $day = date('d');
        $month = $months[(int)date('m')];
        $year = date('Y');

        return "$day $month $year";
    }

    /**
     * Get signature URL/path untuk PDF
     */
    private function getSignatureUrl($signature)
    {
        // Ambil dari kolom image_path (sesuai dengan SignatureController)
        $imagePath = $signature->image_path;

        // Jika sudah full URL, return langsung
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Jika path storage, convert ke public path untuk PDF
        if (Storage::disk('public')->exists($imagePath)) {
            return public_path('storage/' . $imagePath);
        }

        // Fallback
        return public_path($imagePath);
    }

    // ========================================
    // METHODS LAINNYA (TETAP SAMA)
    // ========================================

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
        $performanceScore = $this->calculateOverallPerformance($employee->id);
        $performanceCategory = $performanceScore ? $this->getPerformanceCategory($performanceScore) : 'Belum Ada Penilaian';
        $recentAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
            ->where('status', 'final')
            ->with('jobDesk')
            ->latest('director_reviewed_at')
            ->take(5)
            ->get();
        $monthlyPerformance = $this->getMonthlyPerformance($employee->id);

        // Ambil signature milik user yang sedang login
        $activeSignatures = Signature::where('user_id', Auth::id())
            ->where('is_active', true)
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

    public function reject(Request $request, PromotionRequest $promotionRequest)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        $promotionRequest->update([
            'status' => 'rejected',
            'director_notes' => $request->notes,
            'reviewed_at' => now()
        ]);

        return redirect()
            ->route('director.promotion_requests.index')
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

    private function calculateOverallPerformance($employeeId)
    {
        $completedAssignments = EmployeeJobDesk::where('employee_id', $employeeId)
            ->where('status', 'final')
            ->get();

        $totalRatedAssignments = $completedAssignments->count();

        if ($totalRatedAssignments > 0) {
            $totalScore = $completedAssignments->sum(function($assignment) {
                return ($assignment->kadiv_rating + $assignment->director_rating) / 2;
            });

            return round($totalScore / $totalRatedAssignments, 2);
        }

        return null;
    }

    private function getPerformanceCategory($score)
    {
        if ($score >= 3.7) return 'Sangat Baik';
        if ($score >= 3) return 'Baik';
        if ($score >= 2.5) return 'Cukup';
        if ($score >= 2) return 'Kurang';
        return 'Sangat Kurang';
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

        return $monthlyData->map(function($data) {
            $monthName = date('F', mktime(0, 0, 0, $data->month, 1));
            return [
                'period' => $monthName . ' ' . $data->year,
                'average_score' => round($data->avg_score, 2),
                'total_tasks' => $data->total_tasks
            ];
        })->toArray();
    }
}