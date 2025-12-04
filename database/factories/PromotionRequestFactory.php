<?php

namespace Database\Factories;

use App\Models\PromotionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromotionRequest>
 */
class PromotionRequestFactory extends Factory
{
    protected $model = PromotionRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', '-1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, 'now');

        return [
            'employee_id' => User::factory(),
            'requested_by' => User::factory(),
            'period' => $startDate->format('F Y') . ' - ' . $endDate->format('F Y'),
            'reason' => $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'supporting_document' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function withDocument(): static
    {
        return $this->state(fn (array $attributes) => [
            'supporting_document' => 'promotion_documents/document_' . $this->faker->uuid . '.pdf',
        ]);
    }
}
