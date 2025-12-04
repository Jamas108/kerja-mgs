<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\JobDesk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobDesk>
 */
class JobDeskFactory extends Factory
{
    protected $model = JobDesk::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(2, true),
            'deadline' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'created_by' => User::factory(),
            'division_id' => Division::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
