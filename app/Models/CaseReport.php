<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CaseReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'disease_id',
        'disease_other',
        'date_reported',
        'case_classification',
        'outcome',
        'last_name',
        'first_name',
        'middle_name',
        'sex',
        'date_of_birth',
        'age',
        'civil_status',
        'occupation',
        'address',
        'barangay_id',
        'municipality_id',
        'contact_number',
        'pregnancy_status',
        'philhealth_number',
        'nationality',
        'date_of_onset',
        'date_of_consultation',
        'admitting_facility_id',
        'signs_symptoms',
        'signs_symptoms_other',
        'duration_of_illness',
        'clinical_classification',
        'complications',
        'clinical_outcome',
        'date_of_death',
        'final_diagnosis',
        'clinical_remarks',
        'specimen_collection_date',
        'specimen_type',
        'laboratory_test',
        'laboratory_result_date',
        'laboratory_result',
        'testing_laboratory',
        'laboratory_file_path',
        'place_of_exposure',
        'date_of_exposure',
        'travel_history',
        'travel_departure_date',
        'travel_return_date',
        'mode_of_exposure',
        'source_of_infection',
        'contacts_identified',
        'number_of_contacts',
        'contact_details',
        'relationship_to_case',
        'date_contacted',
        'quarantine_status',
        'reporting_facility_id',
        'facility_code',
        'reporting_health_worker',
        'health_worker_designation',
        'health_worker_contact',
        'date_reported_to_dess',
        'validated_by',
        'validation_date',
        'approved_by',
        'approval_date',
        'validation_remarks',
        'status',
        'reported_by',
        'validator_feedback',
        'returned_by',
        'returned_at',
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_of_birth' => 'date',
        'date_of_onset' => 'date',
        'date_of_consultation' => 'date',
        'date_of_death' => 'date',
        'specimen_collection_date' => 'date',
        'laboratory_result_date' => 'date',
        'date_of_exposure' => 'date',
        'travel_departure_date' => 'date',
        'travel_return_date' => 'date',
        'date_contacted' => 'date',
        'date_reported_to_dess' => 'date',
        'validation_date' => 'datetime',
        'approval_date' => 'datetime',
        'signs_symptoms' => 'array',
        'contacts_identified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($caseReport) {
            if (empty($caseReport->case_id)) {
                $caseReport->case_id = self::generateCaseId();
            }

            if ($caseReport->date_of_birth) {
                $caseReport->age = \Carbon\Carbon::parse($caseReport->date_of_birth)->age;
            }
        });
    }

    public static function generateCaseId()
    {
        return DB::transaction(function () {
            $year = date('Y');
            $prefix = 'CASE-' . $year . '-';

            // Get the latest case with the current year prefix, including soft-deleted records
            $lastCase = self::withTrashed()
                ->where('case_id', 'LIKE', $prefix . '%')
                ->orderBy('case_id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastCase) {
                $lastNumber = intval(substr($lastCase->case_id, -6));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $newCaseId = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

            // Double-check that this ID doesn't exist in both active and soft-deleted records
            while (self::withTrashed()->where('case_id', $newCaseId)->exists()) {
                $newNumber++;
                $newCaseId = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            }

            return $newCaseId;
        });
    }

    // Relationships
    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function admittingFacility()
    {
        return $this->belongsTo(Facility::class, 'admitting_facility_id');
    }

    public function reportingFacility()
    {
        return $this->belongsTo(Facility::class, 'reporting_facility_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'reporting_facility_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function attachments()
    {
        return $this->hasMany(CaseAttachment::class);
    }
}
