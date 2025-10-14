<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\OutbreakAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // 1. EPIDEMIOLOGIC SURVEILLANCE ANALYTICS

        // Weekly Case Trend Analysis
        $weeklyTrend = CaseReport::select(
                DB::raw("strftime('%Y-W%W', date_of_onset) as week"),
                DB::raw('count(*) as total'),
                'disease_id'
            )
            ->with('disease:id,name')
            ->where('date_of_onset', '>=', now()->subWeeks(12))
            ->groupBy('week', 'disease_id')
            ->orderBy('week')
            ->get()
            ->groupBy('week')
            ->map(function ($weekData, $week) {
                return [
                    'week' => $week,
                    'total' => $weekData->sum('total'),
                    'diseases' => $weekData->map(function ($item) {
                        return [
                            'disease' => $item->disease->name ?? 'Unknown',
                            'total' => $item->total,
                        ];
                    }),
                ];
            })->values();

        // Threshold Analysis (Early Warning) - 5-year mean + 2SD simulation
        $thresholdAnalysis = CaseReport::select(
                'disease_id',
                DB::raw('count(*) as current_cases')
            )
            ->with('disease:id,name')
            ->whereDate('date_of_onset', '>=', now()->subWeek())
            ->groupBy('disease_id')
            ->get()
            ->map(function ($item) {
                // Simulate historical average (in real scenario, use 5-year data)
                $historicalAvg = max(1, round($item->current_cases * 0.7)); // Simulated baseline
                $threshold = $historicalAvg + (2 * sqrt($historicalAvg)); // +2SD

                $alertLevel = 'normal';
                if ($item->current_cases >= $threshold * 2) {
                    $alertLevel = 'red';
                } elseif ($item->current_cases >= $threshold * 1.5) {
                    $alertLevel = 'orange';
                } elseif ($item->current_cases >= $threshold) {
                    $alertLevel = 'yellow';
                }

                return [
                    'disease' => $item->disease->name ?? 'Unknown',
                    'current_cases' => $item->current_cases,
                    'threshold' => round($threshold),
                    'alert_level' => $alertLevel,
                    'percentage_of_threshold' => round(($item->current_cases / $threshold) * 100),
                ];
            });

        // Epidemic Curve Generation (Date of Onset)
        $epidemicCurve = CaseReport::select(
                DB::raw("date(date_of_onset) as onset_date"),
                DB::raw('count(*) as cases')
            )
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('onset_date')
            ->orderBy('onset_date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->onset_date,
                    'cases' => $item->cases,
                ];
            });

        // Geospatial Mapping Data (Hotspots)
        $geospatialData = CaseReport::select(
                'municipality_id',
                'barangay',
                DB::raw('count(*) as case_count'),
                DB::raw('GROUP_CONCAT(DISTINCT diseases.name) as diseases')
            )
            ->join('diseases', 'case_reports.disease_id', '=', 'diseases.id')
            ->with('municipality:id,name,latitude,longitude')
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('municipality_id', 'barangay')
            ->orderByDesc('case_count')
            ->get()
            ->map(function ($item) {
                return [
                    'municipality' => $item->municipality->name ?? 'Unknown',
                    'barangay' => $item->barangay,
                    'latitude' => $item->municipality->latitude ?? 0,
                    'longitude' => $item->municipality->longitude ?? 0,
                    'case_count' => $item->case_count,
                    'diseases' => explode(',', $item->diseases),
                    'risk_level' => $item->case_count >= 10 ? 'high' : ($item->case_count >= 5 ? 'medium' : 'low'),
                ];
            });

        // Disease Ranking (Top 5)
        $diseaseRanking = CaseReport::select('disease_id', DB::raw('count(*) as total'))
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->with('disease:id,name')
            ->groupBy('disease_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'disease' => $item->disease->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // 2. DEMOGRAPHIC ANALYTICS

        // Age Distribution (Enhanced)
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
                'sex',
                DB::raw('count(*) as total')
            )
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('age_group', 'sex')
            ->get()
            ->groupBy('age_group')
            ->map(function ($ageData, $ageGroup) {
                $male = $ageData->where('sex', 'Male')->sum('total');
                $female = $ageData->where('sex', 'Female')->sum('total');
                return [
                    'age_group' => $ageGroup,
                    'male' => $male,
                    'female' => $female,
                    'total' => $male + $female,
                ];
            })->values();

        // Sex Distribution
        $sexDistribution = CaseReport::select('sex', DB::raw('count(*) as total'))
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('sex')
            ->get()
            ->map(function ($item) {
                return [
                    'sex' => $item->sex,
                    'total' => $item->total,
                ];
            });

        // High-Risk Group Identification
        $highRiskGroups = CaseReport::select(
                DB::raw("CASE
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) < 5 THEN 'Children (0-4)'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) >= 60 THEN 'Elderly (60+)'
                    WHEN sex = 'Female' AND (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 15 AND 49 THEN 'Women of Reproductive Age'
                    ELSE 'General Population'
                END as risk_group"),
                DB::raw('count(*) as total'),
                'disease_id'
            )
            ->with('disease:id,name')
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('risk_group', 'disease_id')
            ->get()
            ->groupBy('risk_group')
            ->map(function ($groupData, $group) {
                if ($group === 'General Population') return null;

                return [
                    'group' => $group,
                    'total_cases' => $groupData->sum('total'),
                    'diseases' => $groupData->map(function ($item) {
                        return [
                            'disease' => $item->disease->name ?? 'Unknown',
                            'cases' => $item->total,
                        ];
                    })->sortByDesc('cases')->values(),
                ];
            })->filter()->values();

        // 3. MUNICIPALITY ANALYTICS

        // Total Cases by Municipality
        $municipalityCases = CaseReport::select(
                'municipality_id',
                DB::raw('count(*) as total_cases'),
                DB::raw('count(DISTINCT disease_id) as disease_count')
            )
            ->with('municipality:id,name')
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('municipality_id')
            ->orderByDesc('total_cases')
            ->get()
            ->map(function ($item) {
                return [
                    'municipality' => $item->municipality->name ?? 'Unknown',
                    'total_cases' => $item->total_cases,
                    'disease_count' => $item->disease_count,
                ];
            });

        // Week-on-Week Changes
        $currentWeekCases = CaseReport::select('municipality_id', DB::raw('count(*) as cases'))
            ->whereDate('date_of_onset', '>=', now()->startOfWeek())
            ->groupBy('municipality_id')
            ->pluck('cases', 'municipality_id');

        $previousWeekCases = CaseReport::select('municipality_id', DB::raw('count(*) as cases'))
            ->whereDate('date_of_onset', '>=', now()->subWeek()->startOfWeek())
            ->whereDate('date_of_onset', '<', now()->startOfWeek())
            ->groupBy('municipality_id')
            ->pluck('cases', 'municipality_id');

        $weekOnWeekChanges = Municipality::all()->map(function ($municipality) use ($currentWeekCases, $previousWeekCases) {
            $current = $currentWeekCases->get($municipality->id, 0);
            $previous = $previousWeekCases->get($municipality->id, 0);
            $change = $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : ($current > 0 ? 100 : 0);

            return [
                'municipality' => $municipality->name,
                'current_week' => $current,
                'previous_week' => $previous,
                'percentage_change' => $change,
                'trend' => $current > $previous ? 'up' : ($current < $previous ? 'down' : 'stable'),
            ];
        })->filter(function ($item) {
            return $item['current_week'] > 0 || $item['previous_week'] > 0;
        })->sortByDesc('current_week')->values();

        // Top Diseases per Municipality
        $topDiseasesByMunicipality = CaseReport::select(
                'municipality_id',
                'disease_id',
                DB::raw('count(*) as cases')
            )
            ->with(['municipality:id,name', 'disease:id,name'])
            ->whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->groupBy('municipality_id', 'disease_id')
            ->get()
            ->groupBy('municipality_id')
            ->map(function ($municipalityData, $municipalityId) {
                $municipality = $municipalityData->first()->municipality;
                return [
                    'municipality' => $municipality->name ?? 'Unknown',
                    'diseases' => $municipalityData->sortByDesc('cases')->take(5)->map(function ($item) {
                        return [
                            'disease' => $item->disease->name ?? 'Unknown',
                            'cases' => $item->cases,
                        ];
                    })->values(),
                ];
            })->values();

        // Summary Statistics
        $totalCases = CaseReport::whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->count();
        $confirmedCases = CaseReport::whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
            ->where('case_classification', 'Confirmed')
            ->count();
        $deaths = CaseReport::whereDate('date_of_onset', '>=', $startDate)
            ->whereDate('date_of_onset', '<=', $endDate)
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
            // Epidemiologic Surveillance Analytics
            'weeklyTrend' => $weeklyTrend,
            'thresholdAnalysis' => $thresholdAnalysis,
            'epidemicCurve' => $epidemicCurve,
            'geospatialData' => $geospatialData,
            'diseaseRanking' => $diseaseRanking,

            // Demographic Analytics
            'ageDistribution' => $ageDistribution,
            'sexDistribution' => $sexDistribution,
            'highRiskGroups' => $highRiskGroups,

            // Municipality Analytics
            'municipalityCases' => $municipalityCases,
            'weekOnWeekChanges' => $weekOnWeekChanges,
            'topDiseasesByMunicipality' => $topDiseasesByMunicipality,

            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
