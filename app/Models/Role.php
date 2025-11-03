<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Helper method to check if required roles exist in the database
     * This can be called from a seeder or command to ensure all required roles exist
     */
    public static function ensureDefaultRolesExist()
    {
        $requiredRoles = ['admin', 'direktur', 'kepala divisi', 'karyawan'];

        foreach ($requiredRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}