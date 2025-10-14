<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_report_id',
        'file_path',
        'file_name',
        'file_type',
        'attachment_type',
        'remarks',
        'uploaded_by',
    ];

    public function caseReport()
    {
        return $this->belongsTo(CaseReport::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
