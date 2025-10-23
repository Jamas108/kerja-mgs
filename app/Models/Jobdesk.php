<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDesk extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'created_by',
        'division_id',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeJobDesk::class);
    }
}
