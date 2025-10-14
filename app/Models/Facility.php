<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'municipality_id',
        'contact_number',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function reportingCases()
    {
        return $this->hasMany(CaseReport::class, 'reporting_facility_id');
    }

    public function admittingCases()
    {
        return $this->hasMany(CaseReport::class, 'admitting_facility_id');
    }
}
