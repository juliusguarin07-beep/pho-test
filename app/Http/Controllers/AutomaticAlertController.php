<?php

namespace App\Http\Controllers;

use App\Models\OutbreakAlert;
use App\Services\AutomaticAlertService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AutomaticAlertController extends Controller
{
    /**
     * Display automatic alerts dashboard
     */
    public function index(Request $request)
    {
        $query = OutbreakAlert::with(['disease', 'municipality', 'creator'])
            ->where('is_automatic', true)
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by alert level if provided
        if ($request->has('alert_level') && $request->alert_level !== 'all') {
            $query->where('alert_level', $request->alert_level);
        }

        $alerts = $query->paginate(10);

        // Get summary statistics
        $stats = [
            'total_automatic_alerts' => OutbreakAlert::where('is_automatic', true)->count(),
            'draft_alerts' => OutbreakAlert::where('is_automatic', true)->where('status', 'draft')->count(),
            'published_alerts' => OutbreakAlert::where('is_automatic', true)->where('status', 'published')->count(),
            'severe_alerts' => OutbreakAlert::where('is_automatic', true)->where('alert_level', 'severe')->count(),
        ];

        return Inertia::render('AutomaticAlerts/Index', [
            'alerts' => $alerts,
            'stats' => $stats,
            'filters' => $request->only(['status', 'alert_level']),
        ]);
    }

    /**
     * Show specific automatic alert
     */
    public function show(OutbreakAlert $alert)
    {
        $alert->load(['disease', 'municipality', 'creator', 'resolver']);

        return Inertia::render('AutomaticAlerts/Show', [
            'alert' => $alert,
        ]);
    }

    /**
     * Publish an automatic alert
     */
    public function publish(OutbreakAlert $alert)
    {
        $alert->update([
            'status' => 'published',
            'published_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Alert published successfully');
    }

    /**
     * Archive an automatic alert
     */
    public function archive(OutbreakAlert $alert)
    {
        $alert->update([
            'status' => 'archived',
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'Alert archived successfully');
    }

    /**
     * Manually trigger automatic alert check
     */
    public function triggerCheck()
    {
        try {
            $service = new AutomaticAlertService();
            $service->checkAndCreateAutomaticAlerts();

            return redirect()->back()->with('success', 'Automatic alert check completed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during automatic alert check: ' . $e->getMessage());
        }
    }

    /**
     * Update automatic alert content
     */
    public function update(Request $request, OutbreakAlert $alert)
    {
        $request->validate([
            'alert_title' => 'required|string|max:255',
            'alert_details' => 'required|string',
            'health_advisory' => 'nullable|string',
            'preventive_measures' => 'nullable|string',
            'dos_and_donts' => 'nullable|string',
            'emergency_contacts' => 'nullable|string',
        ]);

        $alert->update($request->only([
            'alert_title',
            'alert_details',
            'health_advisory',
            'preventive_measures',
            'dos_and_donts',
            'emergency_contacts',
        ]));

        return redirect()->back()->with('success', 'Alert updated successfully');
    }
}
