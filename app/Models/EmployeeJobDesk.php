<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJobDesk extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_desk_id',
        'employee_id',
        'status',
        'evidence_file',
        'evidence_note',
        'completed_at',
        'kadiv_rating',
        'kadiv_notes',
        'kadiv_reviewed_at',
        'director_rating',
        'director_notes',
        'director_reviewed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'kadiv_reviewed_at' => 'datetime',
        'director_reviewed_at' => 'datetime',
    ];

    public function jobDesk()
    {
        return $this->belongsTo(JobDesk::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'assigned' => '<span class="badge bg-primary">Ditugaskan</span>',
            'completed' => '<span class="badge bg-info">Selesai - Menunggu Review</span>',
            'in_review_kadiv' => '<span class="badge bg-warning">Dalam Review Kadiv</span>',
            'kadiv_approved' => '<span class="badge bg-success">Disetujui Kadiv</span>',
            'in_review_director' => '<span class="badge bg-warning">Dalam Review Direktur</span>',
            'rejected_kadiv' => '<span class="badge bg-danger">Ditolak Kadiv - Perlu Revisi</span>',
            'rejected_director' => '<span class="badge bg-danger">Ditolak Direktur - Perlu Revisi</span>',
            'final' => '<span class="badge bg-success">Final</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Mendapatkan nilai rata-rata dari penilaian kadiv dan direktur
     */
    public function getAverageRatingAttribute()
    {
        if ($this->kadiv_rating && $this->director_rating) {
            return ($this->kadiv_rating + $this->director_rating) / 2;
        }

        return null;
    }

    /**
     * Mendapatkan kategori kinerja berdasarkan nilai rata-rata
     */
    public function getPerformanceCategoryAttribute()
    {
        $avgRating = $this->average_rating;

        if ($avgRating === null) {
            return 'Belum Lengkap';
        }

        if ($avgRating >= 3.7) {
            return 'Sangat Baik';
        } elseif ($avgRating >= 3) {
            return 'Baik';
        } elseif ($avgRating >= 2.5) {
            return 'Cukup';
        } elseif ($avgRating >= 2) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    /**
     * Mendapatkan warna badge berdasarkan kategori kinerja
     */
    public function getPerformanceBadgeColorAttribute()
    {
        $avgRating = $this->average_rating;

        if ($avgRating === null) {
            return 'bg-secondary';
        }

        if ($avgRating >= 3.7) {
            return 'bg-success';
        } elseif ($avgRating >= 3) {
            return 'bg-info';
        } elseif ($avgRating >= 2.5) {
            return 'bg-primary';
        } elseif ($avgRating >= 2) {
            return 'bg-warning';
        } else {
            return 'bg-danger';
        }
    }
}