<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\OutbreakAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Cases by Disease
        $casesByDisease = CaseReport::select('disease_id', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->with('disease:id,name')
            ->groupBy('disease_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'disease' => $item->disease->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // Cases by Municipality
        $casesByMunicipality = CaseReport::select('municipality_id', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->with('municipality:id,name')
            ->groupBy('municipality_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'municipality' => $item->municipality->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // Cases by Status
        $casesByStatus = CaseReport::select('status', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'total' => $item->total,
                ];
            });

        // Cases by Classification
        $casesByClassification = CaseReport::select('case_classification', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->groupBy('case_classification')
            ->get()
            ->map(function ($item) {
                return [
                    'classification' => $item->case_classification,
                    'total' => $item->total,
                ];
            });

        // Cases by Outcome
        $casesByOutcome = CaseReport::select('outcome', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->groupBy('outcome')
            ->get()
            ->map(function ($item) {
                return [
                    'outcome' => $item->outcome,
                    'total' => $item->total,
                ];
            });

        // Monthly Trend (last 12 months) - SQLite compatible
        $monthlyTrend = CaseReport::select(
                DB::raw("strftime('%Y-%m', date_reported) as month"),
                DB::raw('count(*) as total')
            )
            ->where('date_reported', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total' => $item->total,
                ];
            });

        // Age Distribution - SQLite compatible
        $ageDistribution = CaseReport::select(
                DB::raw("CASE
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) < 1 THEN '0-1'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 1 AND 4 THEN '1-4'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 5 AND 9 THEN '5-9'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 10 AND 14 THEN '10-14'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 15 AND 19 THEN '15-19'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 20 AND 29 THEN '20-29'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 30 AND 39 THEN '30-39'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 40 AND 49 THEN '40-49'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 50 AND 59 THEN '50-59'
                    ELSE '60+'
                END as age_group"),
                DB::raw('count(*) as total')
            )
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->groupBy('age_group')
            ->get()
            ->map(function ($item) {
                return [
                    'age_group' => $item->age_group,
                    'total' => $item->total,
                ];
            });

        // Gender Distribution
        $genderDistribution = CaseReport::select('sex', DB::raw('count(*) as total'))
            ->whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->groupBy('sex')
            ->get()
            ->map(function ($item) {
                return [
                    'sex' => $item->sex,
                    'total' => $item->total,
                ];
            });

        // Summary Statistics
        $totalCases = CaseReport::whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->count();
        $confirmedCases = CaseReport::whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->where('case_classification', 'Confirmed')
            ->count();
        $deaths = CaseReport::whereDate('date_reported', '>=', $startDate)
            ->whereDate('date_reported', '<=', $endDate)
            ->where('outcome', 'Died')
            ->count();
        $activeOutbreaks = OutbreakAlert::where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            })
            ->count();

        return Inertia::render('Analytics/Index', [
            'summary' => [
                'total_cases' => $totalCases,
                'confirmed_cases' => $confirmedCases,
                'deaths' => $deaths,
                'active_outbreaks' => $activeOutbreaks,
                'case_fatality_rate' => $totalCases > 0 ? round(($deaths / $totalCases) * 100, 2) : 0,
            ],
            'casesByDisease' => $casesByDisease,
            'casesByMunicipality' => $casesByMunicipality,
            'casesByStatus' => $casesByStatus,
            'casesByClassification' => $casesByClassification,
            'casesByOutcome' => $casesByOutcome,
            'monthlyTrend' => $monthlyTrend,
            'ageDistribution' => $ageDistribution,
            'genderDistribution' => $genderDistribution,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
