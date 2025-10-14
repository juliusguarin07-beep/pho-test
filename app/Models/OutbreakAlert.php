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
        'is_automatic',
        'case_count',
        'threshold_reached',
        'is_active',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'alert_start_date' => 'date',
        'alert_end_date' => 'date',
        'affected_barangays' => 'array',
        'published_at' => 'datetime',
        'is_automatic' => 'boolean',
        'is_active' => 'boolean',
        'resolved_at' => 'datetime',
        'threshold_reached' => 'integer',
    ];

    // Alert levels for automatic system
    const LEVEL_MODERATE = 'moderate'; // Yellow - 10+ cases
    const LEVEL_HIGH = 'high';         // Orange - 25+ cases
    const LEVEL_SEVERE = 'severe';     // Red - 50+ cases

    // Thresholds
    const THRESHOLD_MODERATE = 10;
    const THRESHOLD_HIGH = 25;
    const THRESHOLD_SEVERE = 50;

    /**
     * Get alert level based on case count
     */
    public static function getAlertLevel($caseCount)
    {
        if ($caseCount >= self::THRESHOLD_SEVERE) {
            return self::LEVEL_SEVERE;
        } elseif ($caseCount >= self::THRESHOLD_HIGH) {
            return self::LEVEL_HIGH;
        } elseif ($caseCount >= self::THRESHOLD_MODERATE) {
            return self::LEVEL_MODERATE;
        }

        return null;
    }

    /**
     * Get alert color based on level
     */
    public static function getAlertColor($level)
    {
        return match($level) {
            self::LEVEL_MODERATE => '#FFC107', // Yellow
            self::LEVEL_HIGH => '#FF9800',     // Orange
            self::LEVEL_SEVERE => '#F44336',   // Red
            default => '#6B7280'
        };
    }

    /**
     * Get alert description based on level
     */
    public static function getAlertDescription($level)
    {
        return match($level) {
            self::LEVEL_MODERATE => 'Moderate Risk - Enhanced monitoring recommended',
            self::LEVEL_HIGH => 'Higher Level of Risk - Immediate investigation required',
            self::LEVEL_SEVERE => 'Severe Alert - Urgent outbreak response needed',
            default => 'Unknown'
        };
    }

    /**
     * Get alert icon based on level
     */
    public static function getAlertIcon($level)
    {
        return match($level) {
            self::LEVEL_MODERATE => 'Moderate',
            self::LEVEL_HIGH => 'High',
            self::LEVEL_SEVERE => 'Severe',
            default => 'Info'
        };
    }

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

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
