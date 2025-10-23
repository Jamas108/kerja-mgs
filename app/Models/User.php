<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'division_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function createdJobDesks()
    {
        return $this->hasMany(JobDesk::class, 'created_by');
    }

    public function assignedJobs()
    {
        return $this->hasMany(EmployeeJobDesk::class, 'employee_id');
    }

    /**
     * Get all promotion requests for this user
     */
    public function promotionRequests()
    {
        return $this->hasMany(PromotionRequest::class, 'employee_id');
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isDirector()
    {
        return $this->role->name === 'direktur';
    }

    public function isDivisionHead()
    {
        return $this->role->name === 'kepala divisi';
    }

    public function isEmployee()
    {
        return $this->role->name === 'karyawan';
    }

    /**
     * Menghitung rata-rata kinerja karyawan berdasarkan tugas yang telah final
     */
    public function getPerformanceScoreAttribute()
    {
        $finalAssignments = $this->assignedJobs()
            ->where('status', 'final')
            ->get();

        $totalAssignments = $finalAssignments->count();

        if ($totalAssignments === 0) {
            return null;
        }

        $totalScore = 0;

        foreach ($finalAssignments as $assignment) {
            // Rata-rata dari nilai kadiv dan director
            $avgRating = ($assignment->kadiv_rating + $assignment->director_rating) / 2;
            $totalScore += $avgRating;
        }

        return round($totalScore / $totalAssignments, 2);
    }

    /**
     * Mendapatkan kategori kinerja berdasarkan score
     */
    public function getPerformanceCategoryAttribute()
    {
        $score = $this->performance_score;

        if ($score === null) {
            return 'Belum Ada Penilaian';
        }

        if ($score >= 3.7) {
            return 'Sangat Baik';
        } elseif ($score >= 3) {
            return 'Baik';
        } elseif ($score >= 2.5) {
            return 'Cukup';
        } elseif ($score >= 2) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    /**
     * Mendapatkan jumlah penghargaan (promosi yang disetujui)
     */
    public function getAwardsCountAttribute()
    {
        return $this->promotionRequests()
            ->where('status', 'approved')
            ->count();
    }

    /**
     * Mendapatkan daftar penghargaan yang telah diterima
     */
    public function getAwardsAttribute()
    {
        return $this->promotionRequests()
            ->where('status', 'approved')
            ->orderBy('reviewed_at', 'desc')
            ->get();
    }
}