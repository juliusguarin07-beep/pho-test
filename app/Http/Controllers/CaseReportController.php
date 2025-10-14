<?php

namespace App\Http\Controllers;

use App\Models\CaseReport;
use App\Models\Disease;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CaseReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query
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

        // Apply filters
        if ($request->disease_id) {
            $query->where('disease_id', $request->disease_id);
        }

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        // Get paginated results
        $caseReports = $query->with(['disease', 'municipality', 'barangay', 'reporter'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $diseases = Disease::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();

        return Inertia::render('CaseReports/Index', [
            'caseReports' => $caseReports,
            'diseases' => $diseases,
            'municipalities' => $municipalities,
            'filters' => $request->only(['disease_id', 'municipality_id', 'status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $diseases = Disease::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();
        $barangays = Barangay::orderBy('name')->get();
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('CaseReports/Create', [
            'diseases' => $diseases,
            'municipalities' => $municipalities,
            'barangays' => $barangays,
            'facilities' => $facilities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if this is a draft submission
        $isDraft = $request->input('submitAction') === 'draft';

        // Define conditional validation rules
        $requiredRule = $isDraft ? 'nullable' : 'required';

        // Validate the request
        $validated = $request->validate([
            // Section A - Case Identification
            'disease_id' => $requiredRule . '|exists:diseases,id',
            'disease_other' => 'nullable|string|max:255',
            'date_reported' => $requiredRule . '|date',
            'case_classification' => $requiredRule . '|in:Suspect,Probable,Confirmed',
            'outcome' => $requiredRule . '|in:Alive,Died,Ongoing Treatment',

            // Section B - Patient Information
            'last_name' => $requiredRule . '|string|max:255',
            'first_name' => $requiredRule . '|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'sex' => $requiredRule . '|in:Male,Female,Other',
            'date_of_birth' => $requiredRule . '|date',
            'civil_status' => $requiredRule . '|string',
            'occupation' => 'nullable|string|max:255',
            'address' => $requiredRule . '|string',
            'barangay_id' => $requiredRule . '|exists:barangays,id',
            'municipality_id' => $requiredRule . '|exists:municipalities,id',
            'contact_number' => 'nullable|string|max:20',
            'pregnancy_status' => 'nullable|string',
            'philhealth_number' => 'nullable|string|max:50',
            'nationality' => $requiredRule . '|string|max:100',

            // Section C - Clinical Information
            'date_of_onset' => $requiredRule . '|date',
            'date_of_consultation' => $requiredRule . '|date',
            'admitting_facility_id' => 'nullable|exists:facilities,id',
            'signs_symptoms' => 'nullable|array',
            'signs_symptoms_other' => 'nullable|string',
            'clinical_classification' => 'nullable|string',
            'clinical_outcome' => $requiredRule . '|in:Alive,Died,Unknown',
            'date_of_death' => 'nullable|date',
            'complications' => 'nullable|string',
            'final_diagnosis' => 'nullable|string',
            'clinical_remarks' => 'nullable|string',

            // Section D - Laboratory
            'specimen_collection_date' => 'nullable|date',
            'specimen_type' => 'nullable|string',
            'laboratory_test' => 'nullable|string',
            'laboratory_result_date' => 'nullable|date',
            'laboratory_result' => 'nullable|string',
            'testing_laboratory' => 'nullable|string',
            'laboratory_file' => 'nullable|file|max:10240', // 10MB max

            // Section E - Exposure & Travel
            'place_of_exposure' => 'nullable|string',
            'date_of_exposure' => 'nullable|date',
            'travel_history' => 'nullable|string',
            'travel_departure_date' => 'nullable|date',
            'travel_return_date' => 'nullable|date',
            'mode_of_exposure' => 'nullable|string',
            'source_of_infection' => 'nullable|string',

            // Section F - Contact Tracing
            'contacts_identified' => 'nullable|boolean',
            'number_of_contacts' => 'nullable|integer|min:0',
            'contact_details' => 'nullable|string',
            'relationship_to_case' => 'nullable|string',
            'date_contacted' => 'nullable|date',
            'quarantine_status' => 'nullable|string',

            // Section G - Reporting Information
            'reporting_health_worker' => $requiredRule . '|string|max:255',
            'health_worker_designation' => $requiredRule . '|string',
            'health_worker_contact' => 'nullable|string|max:20',

            // Meta
            'submitAction' => 'required|in:draft,submit',
        ]);

        // Handle file upload
        $laboratoryFilePath = null;
        if ($request->hasFile('laboratory_file')) {
            $laboratoryFilePath = $request->file('laboratory_file')->store('laboratory-results', 'public');
        }

        // Determine status
        $status = $validated['submitAction'] === 'submit' ? 'submitted' : 'draft';

        // Get default IDs for drafts if needed
        $defaultDiseaseId = $validated['disease_id'] ?? \App\Models\Disease::first()?->id ?? 1;
        $defaultMunicipalityId = $validated['municipality_id'] ?? \App\Models\Municipality::first()?->id ?? 1;
        $defaultBarangayId = $validated['barangay_id'] ?? \App\Models\Barangay::where('municipality_id', $defaultMunicipalityId)->first()?->id ?? 1;

        // Create case report with proper defaults for drafts
        $caseReport = CaseReport::create([
            // Section A
            'disease_id' => $validated['disease_id'] ?? $defaultDiseaseId,
            'disease_other' => $validated['disease_other'] ?? null,
            'date_reported' => $validated['date_reported'] ?? now()->toDateString(),
            'case_classification' => $validated['case_classification'] ?? 'Suspect',
            'outcome' => $validated['outcome'] ?? 'Alive',

            // Section B
            'last_name' => $validated['last_name'] ?? 'Draft',
            'first_name' => $validated['first_name'] ?? 'Draft',
            'middle_name' => $validated['middle_name'] ?? null,
            'sex' => $validated['sex'] ?? 'Male',
            'date_of_birth' => $validated['date_of_birth'] ?? '1990-01-01',
            'civil_status' => $validated['civil_status'] ?? 'Single',
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'] ?? 'Draft Address',
            'barangay_id' => $validated['barangay_id'] ?? $defaultBarangayId,
            'municipality_id' => $validated['municipality_id'] ?? $defaultMunicipalityId,
            'contact_number' => $validated['contact_number'] ?? null,
            'pregnancy_status' => $validated['pregnancy_status'] ?? 'N/A',
            'philhealth_number' => $validated['philhealth_number'] ?? null,
            'nationality' => $validated['nationality'] ?? 'Filipino',

            // Section C
            'date_of_onset' => $validated['date_of_onset'] ?? now()->toDateString(),
            'date_of_consultation' => $validated['date_of_consultation'] ?? null,
            'admitting_facility_id' => $validated['admitting_facility_id'] ?? null,
            'signs_symptoms' => $validated['signs_symptoms'] ?? [],
            'signs_symptoms_other' => $validated['signs_symptoms_other'] ?? null,
            'clinical_classification' => $validated['clinical_classification'] ?? null,
            'clinical_outcome' => $validated['clinical_outcome'] ?? 'Alive',
            'date_of_death' => $validated['date_of_death'] ?? null,
            'complications' => $validated['complications'] ?? null,
            'final_diagnosis' => $validated['final_diagnosis'] ?? null,
            'clinical_remarks' => $validated['clinical_remarks'] ?? null,

            // Section D
            'specimen_collection_date' => $validated['specimen_collection_date'] ?? null,
            'specimen_type' => $validated['specimen_type'] ?? null,
            'laboratory_test' => $validated['laboratory_test'] ?? null,
            'laboratory_result_date' => $validated['laboratory_result_date'] ?? null,
            'laboratory_result' => $validated['laboratory_result'] ?? null,
            'testing_laboratory' => $validated['testing_laboratory'] ?? null,
            'laboratory_file_path' => $laboratoryFilePath,

            // Section E
            'place_of_exposure' => $validated['place_of_exposure'] ?? null,
            'date_of_exposure' => $validated['date_of_exposure'] ?? null,
            'travel_history' => $validated['travel_history'] ?? null,
            'travel_departure_date' => $validated['travel_departure_date'] ?? null,
            'travel_return_date' => $validated['travel_return_date'] ?? null,
            'mode_of_exposure' => $validated['mode_of_exposure'] ?? null,
            'source_of_infection' => $validated['source_of_infection'] ?? null,

            // Section F
            'contacts_identified' => $validated['contacts_identified'] ?? false,
            'number_of_contacts' => $validated['number_of_contacts'] ?? 0,
            'contact_details' => $validated['contact_details'] ?? null,
            'relationship_to_case' => $validated['relationship_to_case'] ?? null,
            'date_contacted' => $validated['date_contacted'] ?? null,
            'quarantine_status' => $validated['quarantine_status'] ?? null,

            // Section G
            'reporting_facility_id' => $user->facility_id,
            'reporting_health_worker' => $validated['reporting_health_worker'] ?? 'Draft Health Worker',
            'health_worker_designation' => $validated['health_worker_designation'] ?? 'Draft Designation',
            'health_worker_contact' => $validated['health_worker_contact'] ?? null,

            // Meta
            'reported_by' => $user->id,
            'status' => $status,
        ]);

        // Create audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => $status === 'submitted' ? 'Case Report Submitted' : 'Case Report Draft Created',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => "Case Report {$caseReport->case_id} " . ($status === 'submitted' ? 'submitted for validation' : 'saved as draft'),
            'new_values' => $caseReport->toArray(),
        ]);

        // Redirect with success message
        $message = $status === 'submitted'
            ? 'Case report submitted successfully. Case ID: ' . $caseReport->case_id
            : 'Case report saved as draft. You can continue editing it later.';

        return redirect()->route('case-reports.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $caseReport = CaseReport::with([
            'disease',
            'municipality',
            'barangay',
            'reporter',
            'admittingFacility',
            'reportingFacility'
        ])->findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($user->hasRole('encoder') && $caseReport->reported_by !== $user->id) {
            abort(403, 'Unauthorized access to this case report.');
        }

        return Inertia::render('CaseReports/Show', [
            'caseReport' => $caseReport,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $caseReport = CaseReport::findOrFail($id);

        // Check permissions
        if ($user->hasRole('validator')) {
            abort(403, 'Validators cannot edit case reports.');
        }

        if ($user->hasRole('encoder') && $caseReport->reported_by !== $user->id) {
            abort(403, 'You can only edit your own case reports.');
        }

        // Load the case report with relationships
        $caseReport->load(['disease', 'municipality', 'barangay', 'admittingFacility']);

        // Get dropdown data
        $diseases = \App\Models\Disease::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $municipalities = \App\Models\Municipality::orderBy('name')->get();
        $barangays = \App\Models\Barangay::orderBy('name')->get();
        $facilities = \App\Models\Facility::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('CaseReports/Edit', [
            'caseReport' => $caseReport,
            'diseases' => $diseases,
            'municipalities' => $municipalities,
            'barangays' => $barangays,
            'facilities' => $facilities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $caseReport = CaseReport::findOrFail($id);

        // Check permissions
        if ($user->hasRole('validator')) {
            abort(403, 'Validators cannot edit case reports.');
        }

        if ($user->hasRole('encoder') && $caseReport->reported_by !== $user->id) {
            abort(403, 'You can only edit your own case reports.');
        }

        // Check if this is a draft submission
        $isDraft = $request->input('submitAction') === 'draft';

        // Define conditional validation rules
        $requiredRule = $isDraft ? 'nullable' : 'required';

        // Validate the request with the same rules as store method
        $validated = $request->validate([
            // Section A - Case Identification
            'disease_id' => $requiredRule . '|exists:diseases,id',
            'disease_other' => 'nullable|string|max:255',
            'date_reported' => $requiredRule . '|date',
            'case_classification' => $requiredRule . '|in:Suspect,Probable,Confirmed',
            'outcome' => $requiredRule . '|in:Alive,Died,Ongoing Treatment',

            // Section B - Patient Information
            'last_name' => $requiredRule . '|string|max:255',
            'first_name' => $requiredRule . '|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'sex' => $requiredRule . '|in:Male,Female,Other',
            'date_of_birth' => $requiredRule . '|date',
            'civil_status' => $requiredRule . '|string',
            'occupation' => 'nullable|string|max:255',
            'address' => $requiredRule . '|string',
            'barangay_id' => $requiredRule . '|exists:barangays,id',
            'municipality_id' => $requiredRule . '|exists:municipalities,id',
            'contact_number' => 'nullable|string|max:20',
            'pregnancy_status' => 'nullable|string',
            'philhealth_number' => 'nullable|string|max:50',
            'nationality' => $requiredRule . '|string|max:100',

            // Section C - Clinical Information
            'date_of_onset' => $requiredRule . '|date',
            'date_of_consultation' => $requiredRule . '|date',
            'admitting_facility_id' => 'nullable|exists:facilities,id',
            'signs_symptoms' => 'nullable|array',
            'signs_symptoms_other' => 'nullable|string',
            'clinical_classification' => 'nullable|string',
            'clinical_outcome' => $requiredRule . '|in:Alive,Died,Unknown',
            'complications' => 'nullable|string',

            // Section G - Reporting Information
            'reporting_health_worker' => $requiredRule . '|string|max:255',
            'health_worker_designation' => $requiredRule . '|string',
            'health_worker_contact' => 'nullable|string|max:20',

            // Meta
            'submitAction' => 'required|in:draft,submit',
        ]);

        // Determine new status
        $status = $validated['submitAction'] === 'submit' ? 'submitted' : 'draft';

        // Get default IDs for drafts if needed (same logic as store)
        $defaultDiseaseId = $validated['disease_id'] ?? \App\Models\Disease::first()?->id ?? $caseReport->disease_id;
        $defaultMunicipalityId = $validated['municipality_id'] ?? \App\Models\Municipality::first()?->id ?? $caseReport->municipality_id;
        $defaultBarangayId = $validated['barangay_id'] ?? \App\Models\Barangay::where('municipality_id', $defaultMunicipalityId)->first()?->id ?? $caseReport->barangay_id;

        // Update case report
        $caseReport->update([
            // Section A
            'disease_id' => $validated['disease_id'] ?? $defaultDiseaseId,
            'disease_other' => $validated['disease_other'] ?? null,
            'date_reported' => $validated['date_reported'] ?? $caseReport->date_reported,
            'case_classification' => $validated['case_classification'] ?? $caseReport->case_classification,
            'outcome' => $validated['outcome'] ?? $caseReport->outcome,

            // Section B
            'last_name' => $validated['last_name'] ?? $caseReport->last_name,
            'first_name' => $validated['first_name'] ?? $caseReport->first_name,
            'middle_name' => $validated['middle_name'] ?? $caseReport->middle_name,
            'sex' => $validated['sex'] ?? $caseReport->sex,
            'date_of_birth' => $validated['date_of_birth'] ?? $caseReport->date_of_birth,
            'civil_status' => $validated['civil_status'] ?? $caseReport->civil_status,
            'occupation' => $validated['occupation'] ?? $caseReport->occupation,
            'address' => $validated['address'] ?? $caseReport->address,
            'barangay_id' => $validated['barangay_id'] ?? $defaultBarangayId,
            'municipality_id' => $validated['municipality_id'] ?? $defaultMunicipalityId,
            'contact_number' => $validated['contact_number'] ?? $caseReport->contact_number,
            'pregnancy_status' => $validated['pregnancy_status'] ?? $caseReport->pregnancy_status,
            'philhealth_number' => $validated['philhealth_number'] ?? $caseReport->philhealth_number,
            'nationality' => $validated['nationality'] ?? $caseReport->nationality,

            // Section C
            'date_of_onset' => $validated['date_of_onset'] ?? $caseReport->date_of_onset,
            'date_of_consultation' => $validated['date_of_consultation'] ?? $caseReport->date_of_consultation,
            'admitting_facility_id' => $validated['admitting_facility_id'] ?? $caseReport->admitting_facility_id,
            'signs_symptoms' => $validated['signs_symptoms'] ?? $caseReport->signs_symptoms,
            'signs_symptoms_other' => $validated['signs_symptoms_other'] ?? $caseReport->signs_symptoms_other,
            'clinical_classification' => $validated['clinical_classification'] ?? $caseReport->clinical_classification,
            'clinical_outcome' => $validated['clinical_outcome'] ?? $caseReport->clinical_outcome,
            'complications' => $validated['complications'] ?? $caseReport->complications,

            // Section G
            'reporting_health_worker' => $validated['reporting_health_worker'] ?? $caseReport->reporting_health_worker,
            'health_worker_designation' => $validated['health_worker_designation'] ?? $caseReport->health_worker_designation,
            'health_worker_contact' => $validated['health_worker_contact'] ?? $caseReport->health_worker_contact,

            // Meta
            'status' => $status,
        ]);

        // Create audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => $status === 'submitted' ? 'Case Report Updated & Submitted' : 'Case Report Updated',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => "Case Report {$caseReport->case_id} " . ($status === 'submitted' ? 'updated and submitted for validation' : 'updated and saved as draft'),
            'new_values' => $caseReport->fresh()->toArray(),
        ]);

        // Redirect with success message
        $message = $status === 'submitted'
            ? 'Case report updated and submitted successfully.'
            : 'Case report updated and saved as draft.';

        return redirect()->route('case-reports.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $caseReport = CaseReport::findOrFail($id);

        // Check if user is a PESU admin
        if (!$user->hasRole('pesu_admin')) {
            return redirect()->back()->with('error', 'Unauthorized action. Only PESU administrators can delete case reports.');
        }

        // Create audit log before deletion
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Case Report Deleted',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => "Case Report {$caseReport->case_id} deleted by PESU admin {$user->name}. Patient: {$caseReport->last_name}, {$caseReport->first_name}. Disease: {$caseReport->disease->name}. Status was: {$caseReport->status}",
        ]);

        // Store case ID for success message
        $caseId = $caseReport->case_id;

        // Delete the case report
        $caseReport->delete();

        return redirect()->route('case-reports.index')->with('success', "Case Report {$caseId} has been permanently deleted.");
    }

    /**
     * Validate a case report (Validator role)
     */
    public function validate(Request $request, CaseReport $caseReport)
    {
        $user = Auth::user();

        // Check if user is a validator
        if (!$user->hasRole('validator')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Check if the case is from the validator's facility
        if ($user->facility_id && $caseReport->reporting_facility_id !== $user->facility_id) {
            return redirect()->back()->with('error', 'You can only validate reports from your facility.');
        }

        // Check if status is submitted
        if ($caseReport->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted reports can be validated.');
        }

        // Store original classification for audit log
        $originalClassification = $caseReport->case_classification;

        // Prepare update data
        $updateData = [
            'status' => 'validated',
            'validated_by' => $user->id,
            'validated_at' => now(),
        ];

        // If case classification is "Suspect", automatically change to "Confirmed" upon validation
        if ($originalClassification === 'Suspect') {
            $updateData['case_classification'] = 'Confirmed';
        }

        // Update the case report
        $caseReport->update($updateData);

        // Create detailed audit log
        $auditDescription = "Case Report {$caseReport->case_id} validated by {$user->name}";
        if ($originalClassification === 'Suspect' && $caseReport->case_classification === 'Confirmed') {
            $auditDescription .= ". Case classification automatically updated from Suspect to Confirmed.";
        }

        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Case Report Validated',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => $auditDescription,
        ]);

        // Prepare success message
        $successMessage = "Case Report {$caseReport->case_id} has been validated and sent to PESU for approval.";
        if ($originalClassification === 'Suspect') {
            $successMessage .= " Case classification has been automatically updated from Suspect to Confirmed.";
        }

        return redirect()->route('case-reports.index')->with('success', $successMessage);
    }

    /**
     * Approve a case report (PESU Admin role)
     */
    public function approve(Request $request, CaseReport $caseReport)
    {
        $user = Auth::user();

        // Check if user is PESU admin
        if (!$user->hasRole('pesu_admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Check if status is validated
        if ($caseReport->status !== 'validated') {
            return redirect()->back()->with('error', 'Only validated reports can be approved.');
        }

        // Update status to approved
        $caseReport->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Create audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Case Report Approved',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => "Case Report {$caseReport->case_id} approved by {$user->name}",
        ]);

        return redirect()->route('case-reports.index')->with('success', "Case Report {$caseReport->case_id} has been approved.");
    }

    /**
     * Return a case report to encoder with feedback (Validator role)
     */
    public function returnReport(Request $request, CaseReport $caseReport)
    {
        $user = Auth::user();

        // Validate request
        $request->validate([
            'feedback' => 'required|string|min:10|max:1000',
        ]);

        // Check if user is a validator
        if (!$user->hasRole('validator')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Check if the case is from the validator's facility
        if ($user->facility_id && $caseReport->reporting_facility_id !== $user->facility_id) {
            return redirect()->back()->with('error', 'You can only return reports from your facility.');
        }

        // Check if status is submitted
        if ($caseReport->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted reports can be returned.');
        }

        // Update status to returned with feedback
        $caseReport->update([
            'status' => 'returned',
            'validator_feedback' => $request->feedback,
            'returned_by' => $user->id,
            'returned_at' => now(),
        ]);

        // Create audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Case Report Returned',
            'model_type' => 'CaseReport',
            'model_id' => $caseReport->id,
            'description' => "Case Report {$caseReport->case_id} returned to encoder by {$user->name} with feedback: " . substr($request->feedback, 0, 100) . (strlen($request->feedback) > 100 ? '...' : ''),
        ]);

        return redirect()->route('case-reports.index')->with('success', "Case Report {$caseReport->case_id} has been returned to the encoder for corrections.");
    }

    /**
     * Export case reports to Excel
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        // Base query
        $query = CaseReport::with(['disease', 'municipality', 'barangay', 'facility', 'reportedBy']);

        // Role-based filtering
        if ($user->hasRole('encoder')) {
            // Encoders export only their own reports
            $query->where('reported_by', $user->id);
        } elseif ($user->hasRole('validator')) {
            // Validators export reports from their facility
            if ($user->facility_id) {
                $query->where('reporting_facility_id', $user->facility_id);
            }
        }
        // PESU Admin exports all reports

        // Apply filters if provided
        if ($request->disease_id) {
            $query->where('disease_id', $request->disease_id);
        }

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        $caseReports = $query->orderBy('date_reported', 'desc')->get();

        // Create CSV content
        $filename = 'case_reports_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($caseReports) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Case ID',
                'Disease',
                'Patient Name',
                'Age',
                'Sex',
                'Barangay',
                'Municipality',
                'Reporting Facility',
                'Case Classification',
                'Status',
                'Date Reported',
                'Date of Onset',
                'Outcome',
                'Reported By',
            ]);

            // Add data rows
            foreach ($caseReports as $report) {
                fputcsv($file, [
                    $report->case_id,
                    $report->disease->name ?? '',
                    $report->last_name . ', ' . $report->first_name . ' ' . ($report->middle_name ?? ''),
                    $report->age ?? '',
                    $report->sex ?? '',
                    $report->barangay->name ?? '',
                    $report->municipality->name ?? '',
                    $report->facility->name ?? '',
                    $report->case_classification ?? '',
                    ucfirst($report->status),
                    $report->date_reported ?? '',
                    $report->date_of_onset ?? '',
                    $report->outcome ?? '',
                    $report->reportedBy->name ?? '',
                ]);
            }

            fclose($file);
        };

        // Create audit log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Case Reports Exported',
            'model_type' => 'CaseReport',
            'description' => "Exported {$caseReports->count()} case reports to CSV",
        ]);

        return response()->stream($callback, 200, $headers);
    }
}
