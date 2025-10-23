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

        // Get all employees in the division with performance metrics
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

        // Calculate performance scores for each team member
        foreach ($teamMembers as $member) {
            // Performance score is already calculated via the accessor in the User model

            // Get latest award if exists
            $latestAward = PromotionRequest::where('employee_id', $member->id)
                ->where('status', 'approved')
                ->orderBy('reviewed_at', 'desc')
                ->first();

            $member->latest_award = $latestAward;
        }

        // Get division stats
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

        // Find the team member and ensure they belong to the same division
        $teamMember = User::where('id', $id)
            ->where('division_id', $divisionId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'karyawan');
            })
            ->firstOrFail();

        // Get awards (approved promotions)
        $awards = PromotionRequest::where('employee_id', $teamMember->id)
            ->where('status', 'approved')
            ->with('requester')
            ->orderBy('reviewed_at', 'desc')
            ->get();

        // Get performance history
        $performanceHistory = $this->getMonthlyPerformance($teamMember->id);

        // Get recent assignments
        $recentAssignments = $teamMember->assignedJobs()
            ->with('jobDesk')
            ->where('status', 'final')
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
        $monthlyData = DB::table('employee_job_desks')
            ->where('employee_id', $employeeId)
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