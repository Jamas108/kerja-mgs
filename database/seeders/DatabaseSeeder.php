<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Division;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'karyawan'],
            ['name' => 'admin'],
            ['name' => 'direktur'],
            ['name' => 'kepala divisi'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create divisions
        $divisions = [
            ['name' => 'IT'],
            ['name' => 'Marketing'],
            ['name' => 'Finance'],
            ['name' => 'HR'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // admin role
        ]);

        // Create director
        User::create([
            'name' => 'Direktur',
            'email' => 'direktur@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3, // direktur role
        ]);

        // Create division heads
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'name' => 'Kepala ' . Division::find($i)->name,
                'email' => 'kadiv' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role_id' => 4, // kepala divisi role
                'division_id' => $i,
            ]);
        }

        // Create employees
        for ($i = 1; $i <= 4; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                User::create([
                    'name' => 'Karyawan ' . $j . ' ' . Division::find($i)->name,
                    'email' => 'karyawan' . $i . $j . '@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => 1, // karyawan role
                    'division_id' => $i,
                ]);
            }
        }
    }
}