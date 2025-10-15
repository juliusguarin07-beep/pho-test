<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\OutbreakAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class ValidatorAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Date range filter (default: last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Base query with role-based filtering for validators
        $baseQuery = CaseReport::query();

        // Validators see only reports from their facility
        if ($user->hasRole('validator') && $user->facility_id) {
            $baseQuery->where('reporting_facility_id', $user->facility_id)
                      ->whereIn('status', ['submitted', 'validated', 'returned', 'approved']);
        }

        // Apply date filtering to the base query
        $baseQuery->whereDate('date_of_onset', '>=', $startDate)
                  ->whereDate('date_of_onset', '<=', $endDate);

        // 1. DEMOGRAPHICS - Age and Sex Distribution
        $ageGroups = (clone $baseQuery)
            ->select(
                DB::raw("CASE
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) < 5 THEN '0-4'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 5 AND 14 THEN '5-14'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 15 AND 24 THEN '15-24'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 25 AND 44 THEN '25-44'
                    WHEN (strftime('%Y', 'now') - strftime('%Y', date_of_birth)) BETWEEN 45 AND 64 THEN '45-64'
                    ELSE '65+'
                END as age_group"),
                'sex',
                DB::raw('count(*) as total')
            )
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
        $sexDistribution = (clone $baseQuery)
            ->select('sex', DB::raw('count(*) as total'))
            ->groupBy('sex')
            ->get()
            ->map(function ($item) {
                return [
                    'sex' => $item->sex,
                    'total' => $item->total,
                ];
            });

        // 2. TOTAL CASES IN MUNICIPALITY - Municipality-wide statistics
        $municipalityQuery = CaseReport::query();

        // Get municipality from user's facility
        $userMunicipality = $user->facility ? $user->facility->municipality_id : null;

        if ($userMunicipality) {
            $municipalityQuery->where('municipality_id', $userMunicipality)
                            ->whereDate('date_of_onset', '>=', $startDate)
                            ->whereDate('date_of_onset', '<=', $endDate);
        }

        $municipalityCases = [
            'total_cases' => $municipalityQuery->count(),
            'confirmed_cases' => (clone $municipalityQuery)->where('case_classification', 'Confirmed')->count(),
            'deaths' => (clone $municipalityQuery)->where('outcome', 'Died')->count(),
            'municipality_name' => $user->facility && $user->facility->municipality ? $user->facility->municipality->name : 'Unknown',
        ];

        // Disease breakdown for municipality
        $municipalityDiseases = (clone $municipalityQuery)
            ->select('disease_id', DB::raw('count(*) as total'))
            ->with('disease:id,name')
            ->groupBy('disease_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'disease' => $item->disease->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // 3. COMPARISON OF CASES PER BARANGAY
        $barangayComparison = (clone $municipalityQuery)
            ->select('barangay_id', DB::raw('count(*) as total_cases'))
            ->with('barangay:id,name')
            ->groupBy('barangay_id')
            ->orderByDesc('total_cases')
            ->get()
            ->map(function ($item) {
                return [
                    'barangay' => $item->barangay->name ?? 'Unknown',
                    'total_cases' => $item->total_cases,
                ];
            });

        // Disease breakdown per barangay (top 10 barangays)
        $barangayDiseaseBreakdown = (clone $municipalityQuery)
            ->select('barangay_id', 'disease_id', DB::raw('count(*) as cases'))
            ->with(['disease:id,name', 'barangay:id,name'])
            ->groupBy('barangay_id', 'disease_id')
            ->get()
            ->groupBy('barangay_id')
            ->map(function ($diseaseData, $barangayId) {
                $firstItem = $diseaseData->first();
                return [
                    'barangay' => $firstItem->barangay->name ?? 'Unknown',
                    'total_cases' => $diseaseData->sum('cases'),
                    'diseases' => $diseaseData->map(function ($item) {
                        return [
                            'disease' => $item->disease->name ?? 'Unknown',
                            'cases' => $item->cases,
                        ];
                    })->sortByDesc('cases')->values(),
                ];
            })
            ->sortByDesc('total_cases')
            ->take(10)
            ->values();

        // Summary Statistics (facility-specific)
        $totalCases = (clone $baseQuery)->count();
        $confirmedCases = (clone $baseQuery)->where('case_classification', 'Confirmed')->count();
        $deaths = (clone $baseQuery)->where('outcome', 'Died')->count();

        return Inertia::render('Analytics/ValidatorIndex', [
            'summary' => [
                'total_cases' => $totalCases,
                'confirmed_cases' => $confirmedCases,
                'deaths' => $deaths,
                'case_fatality_rate' => $totalCases > 0 ? round(($deaths / $totalCases) * 100, 2) : 0,
            ],
            // Demographics
            'ageGroups' => $ageGroups,
            'sexDistribution' => $sexDistribution,

            // Municipality totals
            'municipalityCases' => $municipalityCases,
            'municipalityDiseases' => $municipalityDiseases,

            // Barangay comparison
            'barangayComparison' => $barangayComparison,
            'barangayDiseaseBreakdown' => $barangayDiseaseBreakdown,

            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'facility' => $user->facility ? [
                'id' => $user->facility->id,
                'name' => $user->facility->name,
                'type' => $user->facility->type,
            ] : null,
        ]);
    }
}
