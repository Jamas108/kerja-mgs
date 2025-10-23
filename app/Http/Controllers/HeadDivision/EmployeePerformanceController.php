<?php
namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\PromotionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeePerformanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        // Dapatkan semua karyawan dalam divisi
        $employees = User::where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->withCount(['assignedJobs as total_assignments' => function($query) {
                $query->whereIn('status', ['final', 'kadiv_approved', 'in_review_director']);
            }])
            ->withCount(['assignedJobs as completed_assignments' => function($query) {
                $query->where('status', 'final');
            }])
            ->get();

        // Hitung nilai kinerja untuk setiap karyawan
        foreach ($employees as $employee) {
            // Dapatkan semua tugas yang sudah final (memiliki penilaian lengkap)
            $completedAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
                ->where('status', 'final')
                ->get();

            $totalScore = 0;
            $totalRatedAssignments = $completedAssignments->count();

            if ($totalRatedAssignments > 0) {
                // Hitung total nilai (rata-rata nilai kadiv dan director untuk setiap tugas)
                foreach ($completedAssignments as $assignment) {
                    $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
                    $totalScore += $avgRating;
                }

                // Hitung rata-rata keseluruhan
                $employee->performance_score = round($totalScore / $totalRatedAssignments, 2);
            } else {
                $employee->performance_score = null;
            }

            // Set kategori performa berdasarkan nilai
            if ($employee->performance_score !== null) {
                $employee->performance_category = $this->getPerformanceCategory($employee->performance_score);
            } else {
                $employee->performance_category = 'Belum Ada Penilaian';
            }

            // Check if promotion has been requested
            $employee->has_pending_promotion = PromotionRequest::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->exists();
        }

        return view('head_division.performances.index', compact('employees'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $employee = User::where('id', $id)
            ->where('division_id', $divisionId) // Pastikan karyawan berada di divisi yang sama
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->firstOrFail();

        // Dapatkan semua tugas yang sudah dinilai
        $ratedAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
            ->whereNotNull('kadiv_rating')
            ->with('jobDesk')
            ->get();

        // Hitung nilai kinerja keseluruhan
        $totalScore = 0;
        $totalRatedAssignments = 0;

        $completedAssignments = $ratedAssignments->where('status', 'final');
        $totalRatedAssignments = $completedAssignments->count();

        foreach ($completedAssignments as $assignment) {
            $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
            $totalScore += $avgRating;
        }

        $performanceScore = $totalRatedAssignments > 0 ? round($totalScore / $totalRatedAssignments, 2) : null;
        $performanceCategory = $performanceScore !== null ? $this->getPerformanceCategory($performanceScore) : 'Belum Ada Penilaian';

        // Data statistik per bulan (untuk grafik)
        $monthlyPerformance = $this->getMonthlyPerformance($employee->id);

        // Check if promotion has been requested
        $promotionRequest = PromotionRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->first();

        // Get promotion history
        $promotionHistory = PromotionRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('head_division.performances.show', compact(
            'employee',
            'ratedAssignments',
            'performanceScore',
            'performanceCategory',
            'monthlyPerformance',
            'promotionRequest',
            'promotionHistory'
        ));
    }

    public function proposePromotion($id)
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $employee = User::where('id', $id)
            ->where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->firstOrFail();

        return view('head_division.performances.propose_promotion', compact('employee'));
    }

    public function storePromotion(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:50',
            'supporting_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();
        $divisionId = $user->division_id;

        $employee = User::where('id', $id)
            ->where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->firstOrFail();

        // Check if there's already a pending request
        $existingRequest = PromotionRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('head_division.performances.show', $employee->id)
                ->with('error', 'Sudah ada pengajuan promosi yang menunggu persetujuan untuk karyawan ini.');
        }

        $promotionRequest = new PromotionRequest();
        $promotionRequest->employee_id = $employee->id;
        $promotionRequest->requested_by = $user->id;
        $promotionRequest->reason = $request->reason;

        // Handle file upload
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'promotion_support_' . time() . '_' . $employee->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('promotion_documents', $filename, 'public');
            $promotionRequest->supporting_document = $path;
        }

        $promotionRequest->save();

        return redirect()->route('head_division.performances.show', $employee->id)
            ->with('success', 'Pengajuan promosi berhasil diajukan dan menunggu persetujuan direktur.');
    }

    public function compare(Request $request)
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        $employees = User::where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->get();

        $selectedEmployees = [];
        $performanceData = [];

        // Jika ada request dengan employee_ids
        if ($request->has('employee_ids')) {
            $selectedEmployeeIds = $request->employee_ids;

            foreach ($selectedEmployeeIds as $employeeId) {
                $employee = $employees->where('id', $employeeId)->first();

                if ($employee) {
                    $selectedEmployees[] = $employee;

                    // Hitung skor performa untuk karyawan ini
                    $performanceData[$employeeId] = [
                        'monthly' => $this->getMonthlyPerformance($employeeId),
                        'overall' => $this->calculateOverallPerformance($employeeId)
                    ];
                }
            }
        }

        return view('head_division.performances.compare', compact('employees', 'selectedEmployees', 'performanceData'));
    }

    public function report()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        // Data untuk laporan divisi
        $totalEmployees = User::where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->count();

        $allEmployees = User::where('division_id', $divisionId)
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->get();

        $performanceStats = [
            'excellent' => 0,
            'good' => 0,
            'average' => 0,
            'below_average' => 0,
            'poor' => 0,
            'no_rating' => 0
        ];

        foreach ($allEmployees as $employee) {
            $overallPerformance = $this->calculateOverallPerformance($employee->id);

            if ($overallPerformance === null) {
                $performanceStats['no_rating']++;
            } else if ($overallPerformance >= 3.7) {
                $performanceStats['excellent']++;
            } else if ($overallPerformance >= 3) {
                $performanceStats['good']++;
            } else if ($overallPerformance >= 2.5) {
                $performanceStats['average']++;
            } else if ($overallPerformance >= 2) {
                $performanceStats['below_average']++;
            } else {
                $performanceStats['poor']++;
            }
        }

        // Data performa per bulan untuk divisi secara keseluruhan
        $divisionMonthlyPerformance = $this->getDivisionMonthlyPerformance($divisionId);

        // Get promotion statistics
        $promotionStats = [
            'pending' => PromotionRequest::whereHas('employee', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })->where('status', 'pending')->count(),
            'approved' => PromotionRequest::whereHas('employee', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })->where('status', 'approved')->count(),
            'rejected' => PromotionRequest::whereHas('employee', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })->where('status', 'rejected')->count(),
        ];

        return view('head_division.performances.report', compact(
            'totalEmployees',
            'performanceStats',
            'divisionMonthlyPerformance',
            'promotionStats'
        ));
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

    private function getDivisionMonthlyPerformance($divisionId)
    {
        $monthlyData = EmployeeJobDesk::whereHas('jobDesk', function($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->where('status', 'final')
            ->select(
                DB::raw('MONTH(director_reviewed_at) as month'),
                DB::raw('YEAR(director_reviewed_at) as year'),
                DB::raw('AVG((kadiv_rating + director_rating) / 2) as avg_score'),
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw('COUNT(DISTINCT employee_id) as total_employees')
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
                'total_tasks' => $data->total_tasks,
                'total_employees' => $data->total_employees
            ];
        }

        return $formattedData;
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
}