<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function jobDesks()
    {
        return $this->hasMany(JobDesk::class);
    }

    /**
     * Get the division head user (if any)
     */
    public function divisionHead()
    {
        return $this->users()
            ->whereHas('role', function($query) {
                $query->where('name', 'kepala divisi');
            })
            ->first();
    }

    /**
     * Get all employees in this division
     */
    public function employees()
    {
        return $this->users()
            ->whereHas('role', function($query) {
                $query->where('name', 'karyawan');
            })
            ->get();
    }
}