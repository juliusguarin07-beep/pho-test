<?php

namespace App\Http\Controllers;

use App\Models\OutbreakAlert;
use App\Models\Disease;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutbreakAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alerts = OutbreakAlert::with(['disease', 'municipality'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('OutbreakAlerts/Index', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $diseases = \App\Models\Disease::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $municipalities = \App\Models\Municipality::orderBy('name')->get();

        return Inertia::render('OutbreakAlerts/Create', [
            'diseases' => $diseases,
            'municipalities' => $municipalities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disease_id' => 'required|exists:diseases,id',
            'alert_title' => 'required|string|max:255',
            'alert_details' => 'required|string',
            'alert_level' => 'required|in:Green,Yellow,Orange,Red',
            'municipality_id' => 'required|exists:municipalities,id',
            'affected_barangays' => 'nullable|array',
            'alert_start_date' => 'required|date',
            'alert_end_date' => 'nullable|date|after_or_equal:alert_start_date',
            'health_advisory' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $alert = OutbreakAlert::create([
            'disease_id' => $validated['disease_id'],
            'alert_title' => $validated['alert_title'],
            'alert_details' => $validated['alert_details'],
            'alert_level' => $validated['alert_level'],
            'municipality_id' => $validated['municipality_id'],
            'affected_barangays' => $validated['affected_barangays'] ?? [],
            'alert_start_date' => $validated['alert_start_date'],
            'alert_end_date' => $validated['alert_end_date'] ?? null,
            'health_advisory' => $validated['health_advisory'] ?? null,
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('outbreak-alerts.index')
            ->with('success', 'Outbreak alert created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OutbreakAlert $outbreakAlert)
    {
        $outbreakAlert->load(['disease', 'municipality', 'creator']);

        return inertia('OutbreakAlerts/Show', [
            'alert' => $outbreakAlert
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutbreakAlert $outbreakAlert)
    {
        $diseases = Disease::orderBy('name')->get(['id', 'name', 'category']);
        $municipalities = Municipality::orderBy('name')->get(['id', 'name']);

        return inertia('OutbreakAlerts/Edit', [
            'alert' => $outbreakAlert->load(['disease', 'municipality']),
            'diseases' => $diseases,
            'municipalities' => $municipalities
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutbreakAlert $outbreakAlert)
    {
        $validated = $request->validate([
            'disease_id' => 'required|exists:diseases,id',
            'alert_title' => 'required|string|max:255',
            'alert_details' => 'required|string',
            'alert_level' => 'required|in:Green,Yellow,Orange,Red',
            'municipality_id' => 'required|exists:municipalities,id',
            'affected_barangays' => 'nullable|array',
            'alert_start_date' => 'required|date',
            'alert_end_date' => 'nullable|date|after_or_equal:alert_start_date',
            'health_advisory' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $outbreakAlert->update($validated);

        return redirect()->route('outbreak-alerts.index')
            ->with('success', 'Outbreak alert updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutbreakAlert $outbreakAlert)
    {
        $outbreakAlert->delete();

        return redirect()->route('outbreak-alerts.index')
            ->with('success', 'Outbreak alert deleted successfully.');
    }

    /**
     * Resolve/Archive an outbreak alert (mark as finished).
     */
    public function resolve(OutbreakAlert $outbreakAlert)
    {
        $outbreakAlert->update([
            'status' => 'archived',
            'alert_end_date' => $outbreakAlert->alert_end_date ?? now()->toDateString(),
        ]);

        return redirect()->route('outbreak-alerts.index')
            ->with('success', 'Outbreak alert resolved and archived successfully.');
    }

    /**
     * Conclude an outbreak alert (mark as completed).
     */
    public function conclude(OutbreakAlert $outbreakAlert)
    {
        $outbreakAlert->update([
            'status' => 'archived',
            'alert_end_date' => $outbreakAlert->alert_end_date ?? now()->toDateString(),
        ]);

        return redirect()->route('outbreak-alerts.index')
            ->with('success', 'Outbreak alert concluded successfully.');
    }
}
