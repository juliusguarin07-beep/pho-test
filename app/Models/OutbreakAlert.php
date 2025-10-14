<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutbreakAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_id',
        'alert_title',
        'alert_details',
        'alert_level',
        'municipality_id',
        'affected_barangays',
        'affected_areas',
        'number_of_cases',
        'alert_start_date',
        'alert_end_date',
        'health_advisory',
        'preventive_measures',
        'dos_and_donts',
        'emergency_contacts',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'alert_start_date' => 'date',
        'alert_end_date' => 'date',
        'affected_barangays' => 'array',
        'published_at' => 'datetime',
    ];

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
