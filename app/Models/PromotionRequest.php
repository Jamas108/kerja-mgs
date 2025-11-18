<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'requested_by',
        'reason',
        'supporting_document',
        'status',
        'director_notes',
        'certificate_file',
        'reviewed_at',
        'period',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the employee related to the promotion request
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the user who requested the promotion (head of division)
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestedBy() // Changed from requester() to requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">Pending Approval</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Get formatted period (e.g., "January 2024 - August 2024")
     */
    public function getFormattedPeriodAttribute()
    {
        return $this->period;
    }
}
