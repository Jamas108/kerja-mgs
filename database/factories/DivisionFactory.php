<?php

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Division>
 */
class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'IT Division', 'HR Division', 'Finance Division', 'Marketing Division',
                'Operations Division', 'Sales Division', 'Legal Division'
            ]),
        ];
    }
}