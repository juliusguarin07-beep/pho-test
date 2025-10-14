<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function barangays()
    {
        return $this->hasMany(Barangay::class);
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function caseReports()
    {
        return $this->hasMany(CaseReport::class);
    }

    public function outbreakAlerts()
    {
        return $this->hasMany(OutbreakAlert::class);
    }
}
