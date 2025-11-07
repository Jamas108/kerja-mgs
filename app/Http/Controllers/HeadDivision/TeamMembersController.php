<?php

namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PromotionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamMembersController extends Controller
{
    /**
     * Display a listing of team members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        // Dapatkan semua karyawan dalam divisi dengan metrik kinerja
        $teamMembers = User::where('division_id', $divisionId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->withCount(['assignedJobs as total_assignments' => function ($query) {
                $query->whereIn('status', ['final', 'kadiv_approved', 'in_review_director']);
            }])
            ->withCount(['assignedJobs as completed_assignments' => function ($query) {
                $query->where('status', 'final');
            }])
            ->withCount(['promotionRequests as approved_promotions' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->get();

        // Hitung skor kinerja untuk setiap anggota tim
        foreach ($teamMembers as $member) {
            // Skor kinerja sudah dihitung melalui accessor di model User

            // Dapatkan penghargaan terbaru jika ada
            $latestAward = PromotionRequest::where('employee_id', $member->id)
                ->where('status', 'approved')
                ->orderBy('reviewed_at', 'desc')
                ->first();

            $member->latest_award = $latestAward;
        }

        // Dapatkan statistik divisi
        $stats = [
            'total_members' => $teamMembers->count(),
            'total_awards' => $teamMembers->sum('approved_promotions'),
            'avg_performance' => $teamMembers->avg('performance_score'),
            'high_performers' => $teamMembers->where('performance_score', '>=', 3)->count(),
            'pending_promotions' => PromotionRequest::whereIn('employee_id', $teamMembers->pluck('id'))
                ->where('status', 'pending')
                ->count()
        ];

        return view('head_division.team_members.index', compact('teamMembers', 'stats'));
    }

    /**
     * Display detailed information about a team member.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $divisionId = $user->division_id;

        // Cari anggota tim dan pastikan mereka berada dalam divisi yang sama
        $teamMember = User::where('id', $id)
            ->where('division_id', $divisionId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->withCount(['promotionRequests as awards_count' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->firstOrFail();

        // Dapatkan penghargaan (promosi yang disetujui)
        $awards = PromotionRequest::where('employee_id', $teamMember->id)
            ->where('status', 'approved')
            ->with('requester')
            ->orderBy('reviewed_at', 'desc')
            ->get();

        // Dapatkan riwayat kinerja
        $performanceHistory = $this->getMonthlyPerformance($teamMember->id);

        // Dapatkan tugas terbaru yang sudah selesai
        $recentAssignments = $teamMember->assignedJobs()
            ->with('jobDesk')
            ->where('status', 'final')
            ->whereNotNull('director_reviewed_at') // Pastikan ada tanggal review
            ->orderBy('director_reviewed_at', 'desc')
            ->take(5)
            ->get();

        return view('head_division.team_members.show', compact(
            'teamMember',
            'awards',
            'performanceHistory',
            'recentAssignments'
        ));
    }

    /**
     * Get monthly performance data for a team member.
     *
     * @param  int  $employeeId
     * @return array
     */
    private function getMonthlyPerformance($employeeId)
    {
        try {
            $monthlyData = DB::table('employee_job_desks')
                ->where('employee_id', $employeeId)
                ->where('status', 'final')
                ->whereNotNull('director_reviewed_at') // Pastikan ada tanggal review
                ->whereNotNull('kadiv_rating') // Pastikan ada rating kadiv
                ->whereNotNull('director_rating') // Pastikan ada rating direktur
                ->select(
                    DB::raw('MONTH(director_reviewed_at) as month'),
                    DB::raw('YEAR(director_reviewed_at) as year'),
                    DB::raw('AVG((kadiv_rating + director_rating) / 2) as avg_score'),
                    DB::raw('COUNT(*) as total_tasks')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            $formattedData = [];

            // Mapping nama bulan dalam Bahasa Indonesia
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($monthlyData as $data) {
                $monthName = $monthNames[$data->month] ?? 'Unknown';
                $formattedData[] = [
                    'period' => $monthName . ' ' . $data->year,
                    'average_score' => round($data->avg_score, 2),
                    'total_tasks' => (int) $data->total_tasks
                ];
            }

            return $formattedData;
        } catch (\Exception $e) {
            // Log error jika diperlukan
            \Log::error('Error getting monthly performance: ' . $e->getMessage());
            return [];
        }
    }
}