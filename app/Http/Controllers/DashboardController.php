<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\OutbreakAlert;
use App\Services\OutbreakAlertService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $outbreakAlertService;

    public function __construct(OutbreakAlertService $outbreakAlertService)
    {
        $this->outbreakAlertService = $outbreakAlertService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query for case reports
        $query = CaseReport::query();

        // Role-based filtering
        if ($user->hasRole('encoder')) {
            // Encoders see only their own reports
            $query->where('reported_by', $user->id);
        } elseif ($user->hasRole('validator')) {
            // Validators see all reports from their facility (except drafts)
            if ($user->facility_id) {
                $query->where('reporting_facility_id', $user->facility_id)
                      ->whereIn('status', ['submitted', 'validated', 'returned', 'approved']);
            }
        }
        // PESU Admin sees all reports

        // Statistics
        $statistics = [
            'total_cases' => (clone $query)->count(),
            'pending_validation' => (clone $query)->where('status', 'submitted')->count(),
            'validated_cases' => (clone $query)->where('status', 'validated')->count(),
            'approved_cases' => (clone $query)->where('status', 'approved')->count(),
            'draft_cases' => (clone $query)->where('status', 'draft')->count(),
            'returned_cases' => (clone $query)->where('status', 'returned')->count(),
        ];

        // Recent cases
        $recentCases = (clone $query)
            ->with(['disease', 'municipality', 'reporter'])
            ->latest()
            ->limit(5)
            ->get();

        // Active outbreak alerts (both manual and automatic)
        $outbreakAlerts = $this->outbreakAlertService->getActiveAlerts($user);

        // Get current automatic alerts for dashboard banners
        $automaticAlerts = $this->outbreakAlertService->getCurrentAutomaticAlerts();

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'recentCases' => $recentCases,
            'outbreakAlerts' => $outbreakAlerts,
            'automaticAlerts' => $automaticAlerts,
        ]);
    }
}
