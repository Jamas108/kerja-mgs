<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJobDesk;
use App\Models\PromotionRequest;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminEmployeePerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Base query for all employees with 'karyawan' role
        $query = User::whereHas('role', function ($q) {
            $q->where('name', 'karyawan');
        })->with('division');

        // Apply filters
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('performance_category')) {
            // This will be filtered after performance calculation
            $performanceFilter = $request->performance_category;
        }

        $employees = $query->withCount([
            'assignedJobs as total_assignments' => function ($query) {
                $query->whereIn('status', ['final', 'kadiv_approved', 'in_review_director']);
            }
        ])->withCount([
            'assignedJobs as completed_assignments' => function ($query) {
                $query->where('status', 'final');
            }
        ])->get();

        // Calculate performance for each employee
        foreach ($employees as $employee) {
            $completedAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
                ->where('status', 'final')
                ->get();

            $totalScore = 0;
            $totalRatedAssignments = $completedAssignments->count();

            if ($totalRatedAssignments > 0) {
                foreach ($completedAssignments as $assignment) {
                    $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
                    $totalScore += $avgRating;
                }
                $employee->performance_score = round($totalScore / $totalRatedAssignments, 2);
            } else {
                $employee->performance_score = null;
            }

            if ($employee->performance_score !== null) {
                $employee->performance_category = $this->getPerformanceCategory($employee->performance_score);
            } else {
                $employee->performance_category = 'Belum Ada Penilaian';
            }

            // Check pending promotion
            $employee->has_pending_promotion = PromotionRequest::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->exists();
        }

        // Apply performance category filter if specified
        if (isset($performanceFilter) && $performanceFilter !== '') {
            $employees = $employees->filter(function ($employee) use ($performanceFilter) {
                return $employee->performance_category === $performanceFilter;
            });
        }

        // Get divisions and performance categories for filters
        $divisions = Division::select('id', 'name')->orderBy('name')->get();
        $performanceCategories = [
            'Sangat Baik' => 'Sangat Baik',
            'Baik' => 'Baik',
            'Cukup' => 'Cukup',
            'Kurang' => 'Kurang',
            'Sangat Kurang' => 'Sangat Kurang',
            'Belum Ada Penilaian' => 'Belum Ada Penilaian'
        ];

        return view('admin.performances.index', compact(
            'employees',
            'divisions',
            'performanceCategories'
        ));
    }

    public function show($id)
    {
        $employee = User::where('id', $id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->with('division')
            ->firstOrFail();

        // Get all rated assignments
        $ratedAssignments = EmployeeJobDesk::where('employee_id', $employee->id)
            ->whereNotNull('kadiv_rating')
            ->with(['jobDesk', 'jobDesk.division'])
            ->get();

        // Calculate overall performance
        $totalScore = 0;
        $completedAssignments = $ratedAssignments->where('status', 'final');
        $totalRatedAssignments = $completedAssignments->count();

        foreach ($completedAssignments as $assignment) {
            $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
            $totalScore += $avgRating;
        }

        $performanceScore = $totalRatedAssignments > 0 ? round($totalScore / $totalRatedAssignments, 2) : null;
        $performanceCategory = $performanceScore !== null ? $this->getPerformanceCategory($performanceScore) : 'Belum Ada Penilaian';

        // Monthly performance data
        $monthlyPerformance = $this->getMonthlyPerformance($employee->id);

        // Promotion information
        $promotionRequest = PromotionRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->with('requestedBy')
            ->first();

        $promotionHistory = PromotionRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with('requestedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.performances.show', compact(
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
        $employee = User::where('id', $id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->with('division')
            ->firstOrFail();

        // Calculate performance for validation
        $performanceScore = $this->calculateOverallPerformance($employee->id);

        return view('admin.performances.propose_promotion', compact('employee', 'performanceScore'));
    }

    public function storePromotion(Request $request, $id)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'reason' => 'required|string|max:2000',
            'supporting_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ], [
            'period_start.required' => 'Periode awal harus diisi',
            'period_end.required' => 'Periode akhir harus diisi',
            'period_end.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal',
            'reason.required' => 'Alasan pengajuan promosi harus diisi',
            'reason.max' => 'Alasan pengajuan promosi tidak boleh lebih dari 2000 karakter',
        ]);

        $employee = User::where('id', $id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->firstOrFail();

        // Check for existing pending request
        $existingRequest = PromotionRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('admin.performances.show', $employee->id)
                ->with('error', 'Sudah ada pengajuan promosi yang menunggu persetujuan untuk karyawan ini.');
        }

        // Format period
        $periodStart = \Carbon\Carbon::parse($request->period_start);
        $periodEnd = \Carbon\Carbon::parse($request->period_end);
        $period = $periodStart->format('F Y') . ' - ' . $periodEnd->format('F Y');

        $promotionRequest = new PromotionRequest();
        $promotionRequest->employee_id = $employee->id;
        $promotionRequest->requested_by = Auth::id();
        $promotionRequest->period = $period;
        $promotionRequest->reason = $request->reason;

        // Handle file upload
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'promotion_support_' . time() . '_' . $employee->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('promotion_documents', $filename, 'public');
            $promotionRequest->supporting_document = $path;
        }

        $promotionRequest->save();

        return redirect()->route('admin.performances.show', $employee->id)
            ->with('success', 'Pengajuan promosi berhasil diajukan dan menunggu persetujuan direktur.');
    }

    public function compare(Request $request)
    {
        // Get all employees
        $allEmployees = User::whereHas('role', function ($query) {
            $query->where('name', 'karyawan');
        })->with('division')->get();

        // Get divisions for filter
        $divisions = Division::select('id', 'name')->orderBy('name')->get();

        $selectedEmployees = [];
        $performanceData = [];

        // Process comparison request
        if ($request->has('employee_ids') && is_array($request->employee_ids)) {
            $selectedEmployeeIds = $request->employee_ids;

            foreach ($selectedEmployeeIds as $employeeId) {
                $employee = $allEmployees->where('id', $employeeId)->first();

                if ($employee) {
                    $selectedEmployees[] = $employee;
                    $performanceData[$employeeId] = [
                        'monthly' => $this->getMonthlyPerformance($employeeId),
                        'overall' => $this->calculateOverallPerformance($employeeId)
                    ];
                }
            }
        }

        return view('admin.performances.compare', compact(
            'allEmployees',
            'divisions',
            'selectedEmployees',
            'performanceData'
        ));
    }

    public function report(Request $request)
    {
        // Apply division filter if provided
        $divisionFilter = $request->division_id;

        $employeesQuery = User::whereHas('role', function ($query) {
            $query->where('name', 'karyawan');
        });

        if ($divisionFilter) {
            $employeesQuery->where('division_id', $divisionFilter);
        }

        $totalEmployees = $employeesQuery->count();
        $allEmployees = $employeesQuery->with('division')->get();

        // Performance statistics
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

        // Company-wide or division monthly performance
        $monthlyPerformance = $this->getCompanyMonthlyPerformance($divisionFilter);

        // Division statistics
        $divisionStats = Division::withCount('employees as total_employees')->get();

        // Add performance data for each division using map()
        $divisionStats = $divisionStats->map(function ($division) {
            $divisionEmployees = User::where('division_id', $division->id)
                ->whereHas('role', function ($query) {
                    $query->where('name', 'karyawan');
                })
                ->get();

            $stats = [
                'excellent' => 0,
                'good' => 0,
                'average' => 0,
                'below_average' => 0,
                'poor' => 0,
                'no_rating' => 0
            ];

            foreach ($divisionEmployees as $employee) {
                $performance = $this->calculateOverallPerformance($employee->id);

                if ($performance === null) {
                    $stats['no_rating']++;
                } else if ($performance >= 3.7) {
                    $stats['excellent']++;
                } else if ($performance >= 3) {
                    $stats['good']++;
                } else if ($performance >= 2.5) {
                    $stats['average']++;
                } else if ($performance >= 2) {
                    $stats['below_average']++;
                } else {
                    $stats['poor']++;
                }
            }

            // Assign performance_stats to the division object
            $division->performance_stats = $stats;

            return $division;
        });

        // Promotion statistics
        $promotionStatsQuery = PromotionRequest::query();
        if ($divisionFilter) {
            $promotionStatsQuery->whereHas('employee', function ($query) use ($divisionFilter) {
                $query->where('division_id', $divisionFilter);
            });
        }

        $promotionStats = [
            'pending' => (clone $promotionStatsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $promotionStatsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $promotionStatsQuery)->where('status', 'rejected')->count(),
        ];

        $divisions = Division::select('id', 'name')->orderBy('name')->get();

        return view('admin.performances.report', compact(
            'totalEmployees',
            'performanceStats',
            'monthlyPerformance',
            'divisionStats',
            'promotionStats',
            'divisions'
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

    private function getCompanyMonthlyPerformance($divisionId = null)
    {
        $query = EmployeeJobDesk::where('status', 'final');

        if ($divisionId) {
            $query->whereHas('jobDesk', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $monthlyData = $query->select(
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
