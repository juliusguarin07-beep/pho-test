<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutbreakAlert;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicAlertController extends Controller
{
    /**
     * Get all active outbreak alerts for residents
     */
    public function index(Request $request)
    {
        $query = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            });

        // Filter by municipality
        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        // Filter by alert level
        if ($request->alert_level) {
            $query->where('alert_level', $request->alert_level);
        }

        $alerts = $query->with([
            'disease:id,name,category,description',
            'municipality:id,name'
        ])
        ->orderByRaw("CASE
            WHEN alert_level = 'Red' THEN 1
            WHEN alert_level = 'Orange' THEN 2
            WHEN alert_level = 'Yellow' THEN 3
            WHEN alert_level = 'Green' THEN 4
            ELSE 5 END")
        ->latest('alert_start_date')
        ->paginate(20);

        // Transform data to match PWA expectations
        $alerts->getCollection()->transform(function ($alert) {
            return [
                'id' => $alert->id,
                'title' => $alert->alert_title,
                'description' => $alert->alert_details,
                'alert_level' => $alert->alert_level,
                'disease_id' => $alert->disease_id,
                'disease' => $alert->disease,
                'municipality_id' => $alert->municipality_id,
                'municipality' => $alert->municipality,
                'affected_areas' => $alert->affected_areas ?? $this->formatAffectedAreas($alert->affected_barangays),
                'number_of_cases' => $this->getActualCaseCount($alert->disease_id, $alert->municipality_id),
                'alert_start_date' => $alert->alert_start_date,
                'alert_end_date' => $alert->alert_end_date,
                'status' => $alert->status,
                'created_at' => $alert->created_at,
                'updated_at' => $alert->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'message' => 'Outbreak alerts retrieved successfully'
        ]);
    }

    /**
     * Get a specific outbreak alert by ID
     */
    public function show($id)
    {
        $alert = OutbreakAlert::with([
            'disease:id,name,category,description,symptoms,prevention_measures',
            'municipality:id,name',
            'creator:id,name'
        ])->findOrFail($id);

        // Check if alert is published and active
        if ($alert->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Alert not available'
            ], 404);
        }

        // Map fields to match PWA expectations
        $mappedAlert = [
            'id' => $alert->id,
            'title' => $alert->alert_title,
            'description' => $alert->alert_details,
            'alert_level' => $alert->alert_level,
            'disease_id' => $alert->disease_id,
            'disease' => $alert->disease,
            'municipality_id' => $alert->municipality_id,
            'municipality' => $alert->municipality,
            'barangay_id' => null, // Not currently used
            'barangay' => null,
            'affected_areas' => $alert->affected_areas ?? $this->formatAffectedAreas($alert->affected_barangays),
            'number_of_cases' => $this->getActualCaseCount($alert->disease_id, $alert->municipality_id),
            'preventive_measures' => $alert->preventive_measures ?? $this->getDefaultPreventiveMeasures($alert->disease),
            'dos_and_donts' => $alert->dos_and_donts ?? $this->getDefaultDosAndDonts($alert->disease),
            'emergency_contacts' => $alert->emergency_contacts ?? $this->getDefaultEmergencyContacts(),
            'alert_start_date' => $alert->alert_start_date,
            'alert_end_date' => $alert->alert_end_date,
            'status' => $alert->status,
            'issued_by' => $alert->created_by,
            'issuedBy' => $alert->creator,
            'created_at' => $alert->created_at,
            'updated_at' => $alert->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $mappedAlert,
            'message' => 'Alert details retrieved successfully'
        ]);
    }

    /**
     * Get alert statistics for dashboard
     */
    public function statistics(Request $request)
    {
        $municipalityId = $request->municipality_id;
        $barangayId = $request->barangay_id;

        $query = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            });

        if ($municipalityId) {
            $query->where('municipality_id', $municipalityId);
        }

        $stats = [
            'total_active_alerts' => (clone $query)->count(),
            'red_alerts' => (clone $query)->where('alert_level', 'Red')->count(),
            'orange_alerts' => (clone $query)->where('alert_level', 'Orange')->count(),
            'yellow_alerts' => (clone $query)->where('alert_level', 'Yellow')->count(),
            'green_alerts' => (clone $query)->where('alert_level', 'Green')->count(),
            'diseases_tracked' => (clone $query)->distinct('disease_id')->count('disease_id'),
            'latest_update' => (clone $query)->latest('updated_at')->value('updated_at'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Get alerts for map display with coordinates
     */
    public function mapData(Request $request)
    {
        $query = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('alert_start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('alert_end_date')
                  ->orWhere('alert_end_date', '>=', now());
            });

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        $alerts = $query->with([
            'disease:id,name,category',
            'municipality:id,name,latitude,longitude'
        ])
        ->select([
            'id',
            'disease_id',
            'municipality_id',
            'alert_level',
            'alert_title',
            'alert_details',
            'affected_barangays',
            'number_of_cases',
            'alert_start_date',
            'alert_end_date',
            'updated_at'
        ])
        ->get()
        ->map(function ($alert) {
            return [
                'id' => $alert->id,
                'title' => $alert->alert_title,
                'disease' => $alert->disease->name ?? 'Unknown',
                'municipality' => $alert->municipality->name ?? 'Unknown',
                'alert_level' => $alert->alert_level,
                'affected_barangays' => $alert->affected_barangays,
                'date' => $alert->alert_start_date ? $alert->alert_start_date : now()->format('Y-m-d'),
                'updated_at' => $alert->updated_at ? $alert->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'cases' => $this->getActualCaseCount($alert->disease_id, $alert->municipality_id),
                'coordinates' => $this->getCoordinates($alert->municipality_id),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'message' => 'Map data retrieved successfully'
        ]);
    }

    /**
     * Get list of municipalities for filter
     */
    public function municipalities()
    {
        $municipalities = Municipality::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $municipalities,
            'message' => 'Municipalities retrieved successfully'
        ]);
    }

    /**
     * Get list of barangays by municipality
     */
    public function barangays(Request $request)
    {
        $query = Barangay::query();

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        $barangays = $query->orderBy('name')->get(['id', 'name', 'municipality_id']);

        return response()->json([
            'success' => true,
            'data' => $barangays,
            'message' => 'Barangays retrieved successfully'
        ]);
    }

    /**
     * Get recent alerts (last 7 days)
     */
    public function recent(Request $request)
    {
        $query = OutbreakAlert::query()
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subDays(7));

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->barangay_id) {
            $query->where('barangay_id', $request->barangay_id);
        }

        $alerts = $query->with([
            'disease:id,name,category',
            'municipality:id,name',
            'barangay:id,name'
        ])
        ->latest()
        ->limit(10)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'message' => 'Recent alerts retrieved successfully'
        ]);
    }

    /**
     * Helper function to get coordinates from municipality
     */
    private function getCoordinates($municipalityId, $barangayId = null)
    {
        // Default to Pangasinan center if no municipality
        $defaultLat = 15.8949;
        $defaultLng = 120.2863;

        if (!$municipalityId) {
            return [
                'latitude' => $defaultLat,
                'longitude' => $defaultLng
            ];
        }

        // Get coordinates from municipality
        $municipality = Municipality::find($municipalityId);

        if ($municipality && $municipality->latitude && $municipality->longitude) {
            return [
                'latitude' => (float) $municipality->latitude,
                'longitude' => (float) $municipality->longitude
            ];
        }

        // Fallback to default
        return [
            'latitude' => $defaultLat,
            'longitude' => $defaultLng
        ];
    }

    /**
     * Format affected barangays array into a readable string
     */
    private function formatAffectedAreas($affectedBarangays)
    {
        if (!$affectedBarangays || !is_array($affectedBarangays)) {
            return 'Not specified';
        }

        return implode(', ', $affectedBarangays);
    }

    /**
     * Get default preventive measures based on disease
     */
    private function getDefaultPreventiveMeasures($disease)
    {
        if (!$disease) {
            return "• Follow general health protocols\n• Maintain good hygiene\n• Seek medical attention if symptoms develop";
        }

        $defaultMeasures = [
            'Leptospirosis' => "• Avoid contact with flood water or contaminated water\n• Wear protective clothing when in contact with water\n• Properly dispose of garbage to prevent rat infestation\n• Keep surroundings clean and free from stagnant water\n• Seek immediate medical attention if fever develops after exposure",
            'Dengue' => "• Eliminate breeding sites of mosquitoes (standing water)\n• Use mosquito repellent\n• Wear long sleeves and pants\n• Install screens on windows and doors\n• Seek medical attention for fever lasting more than 2 days",
            'Chikungunya' => "• Eliminate mosquito breeding sites\n• Use mosquito nets and repellents\n• Wear protective clothing\n• Keep surroundings clean\n• Consult healthcare provider for persistent joint pain and fever",
            'default' => "• Follow general health protocols\n• Maintain good hygiene\n• Avoid crowded places if possible\n• Seek medical attention if symptoms develop"
        ];

        return $defaultMeasures[$disease->name] ?? $defaultMeasures['default'];
    }

    /**
     * Get default do's and don'ts based on disease
     */
    private function getDefaultDosAndDonts($disease)
    {
        if (!$disease) {
            return "DO:\n• Follow health protocols\n• Stay hydrated\n• Rest adequately\n\nDON'T:\n• Ignore symptoms\n• Self-medicate\n• Panic";
        }

        $defaultDosAndDonts = [
            'Leptospirosis' => "DO:\n• Boil water before drinking\n• Wear protective gear in flood areas\n• Seek immediate medical care for fever\n• Report symptoms to health authorities\n\nDON'T:\n• Wade through flood water unnecessarily\n• Ignore cuts and wounds\n• Delay medical consultation\n• Use contaminated water for drinking",
            'Dengue' => "DO:\n• Monitor fever and symptoms closely\n• Drink plenty of fluids\n• Rest and avoid strenuous activities\n• Seek medical attention immediately\n\nDON'T:\n• Take aspirin or ibuprofen\n• Ignore warning signs (vomiting, severe abdominal pain)\n• Self-medicate\n• Delay seeking medical care",
            'Chikungunya' => "DO:\n• Rest and stay hydrated\n• Use pain relievers as prescribed\n• Protect against mosquito bites\n• Report symptoms to health authorities\n\nDON'T:\n• Ignore joint pain and swelling\n• Overexert yourself\n• Stop medications without consulting doctor\n• Allow mosquito breeding around home",
            'default' => "DO:\n• Follow medical advice\n• Stay hydrated\n• Rest adequately\n• Practice good hygiene\n\nDON'T:\n• Ignore symptoms\n• Self-medicate\n• Spread misinformation\n• Panic"
        ];

        return $defaultDosAndDonts[$disease->name] ?? $defaultDosAndDonts['default'];
    }

    /**
     * Get default emergency contacts
     */
    private function getDefaultEmergencyContacts()
    {
        return "Emergency Hotlines:\n• Provincial Health Office: (075) 515-4046\n• Emergency Response: 911\n• DOH Hotline: 02-8651-7800\n• Red Cross: 143\n\nFor medical emergencies, proceed to the nearest hospital immediately.";
    }

    /**
     * Get actual case count from case_reports table
     */
    private function getActualCaseCount($diseaseId, $municipalityId)
    {
        return \App\Models\CaseReport::where('disease_id', $diseaseId)
            ->where('municipality_id', $municipalityId)
            ->whereIn('status', ['submitted', 'validated', 'approved'])
            ->count();
    }
}
