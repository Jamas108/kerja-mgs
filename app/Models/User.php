<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

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
        try {
            return $this->role && $this->role->name === 'admin';
        } catch (\Exception $e) {
            Log::error('Error in isAdmin() method: ' . $e->getMessage());
            return false;
        }
    }

    public function isDirector()
    {
        try {
            return $this->role && $this->role->name === 'direktur';
        } catch (\Exception $e) {
            Log::error('Error in isDirector() method: ' . $e->getMessage());
            return false;
        }
    }

    public function isDivisionHead()
    {
        try {
            return $this->role && $this->role->name === 'kepala divisi';
        } catch (\Exception $e) {
            Log::error('Error in isDivisionHead() method: ' . $e->getMessage());
            return false;
        }
    }

    public function isEmployee()
    {
        try {
            return $this->role && $this->role->name === 'karyawan';
        } catch (\Exception $e) {
            Log::error('Error in isEmployee() method: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung rata-rata kinerja karyawan berdasarkan tugas yang telah final
     */
    public function getPerformanceScoreAttribute()
    {
        try {
            $finalAssignments = $this->assignedJobs()
                ->where('status', 'final')
                ->get();

            $totalAssignments = $finalAssignments->count();

            if ($totalAssignments === 0) {
                return null;
            }

            $totalScore = 0;

            foreach ($finalAssignments as $assignment) {
                // Ensure both ratings exist and are numeric
                $kadivRating = is_numeric($assignment->kadiv_rating) ? $assignment->kadiv_rating : 0;
                $directorRating = is_numeric($assignment->director_rating) ? $assignment->director_rating : 0;

                // Rata-rata dari nilai kadiv dan director
                $avgRating = ($kadivRating + $directorRating) / 2;
                $totalScore += $avgRating;
            }

            return round($totalScore / $totalAssignments, 2);
        } catch (\Exception $e) {
            Log::error('Error in getPerformanceScoreAttribute(): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mendapatkan kategori kinerja berdasarkan score
     */
    public function getPerformanceCategoryAttribute()
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in getPerformanceCategoryAttribute(): ' . $e->getMessage());
            return 'Tidak Tersedia';
        }
    }

    /**
     * Mendapatkan jumlah penghargaan (promosi yang disetujui)
     */
    public function getAwardsCountAttribute()
    {
        try {
            return $this->promotionRequests()
                ->where('status', 'approved')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error in getAwardsCountAttribute(): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mendapatkan daftar penghargaan yang telah diterima
     */
    public function getAwardsAttribute()
    {
        try {
            return $this->promotionRequests()
                ->where('status', 'approved')
                ->orderBy('reviewed_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error in getAwardsAttribute(): ' . $e->getMessage());
            return collect();
        }
    }
}