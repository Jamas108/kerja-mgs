<?php

namespace Database\Factories;

use App\Models\EmployeeJobDesk;
use App\Models\JobDesk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeJobDesk>
 */
class EmployeeJobDeskFactory extends Factory
{
    protected $model = EmployeeJobDesk::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement([
            'assigned', 'in_progress', 'completed', 'in_review_kadiv',
            'kadiv_approved', 'in_review_director', 'final', 'rejected_kadiv', 'rejected_director'
        ]);

        $data = [
            'job_desk_id' => JobDesk::factory(),
            'employee_id' => User::factory(),
            'status' => $status,
            'assigned_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];

        // Add ratings and timestamps based on status
        if (in_array($status, ['kadiv_approved', 'in_review_director', 'final', 'rejected_kadiv'])) {
            $data['started_at'] = $this->faker->dateTimeBetween($data['assigned_at'], 'now');
            $data['completed_at'] = $this->faker->dateTimeBetween($data['started_at'], 'now');
            $data['kadiv_rating'] = $this->faker->numberBetween(1, 4);
            $data['kadiv_notes'] = $this->faker->sentence();
            $data['kadiv_reviewed_at'] = $this->faker->dateTimeBetween($data['completed_at'], 'now');
        }

        if (in_array($status, ['final', 'rejected_director'])) {
            $data['director_rating'] = $this->faker->numberBetween(1, 4);
            $data['director_notes'] = $this->faker->sentence();
            $data['director_reviewed_at'] = $this->faker->dateTimeBetween($data['kadiv_reviewed_at'], 'now');
        }

        if ($status === 'in_progress') {
            $data['started_at'] = $this->faker->dateTimeBetween($data['assigned_at'], 'now');
        }

        if (in_array($status, ['completed', 'in_review_kadiv', 'kadiv_approved', 'in_review_director', 'final', 'rejected_kadiv', 'rejected_director'])) {
            $data['started_at'] = $this->faker->dateTimeBetween($data['assigned_at'], 'now');
            $data['completed_at'] = $this->faker->dateTimeBetween($data['started_at'], 'now');
        }

        return $data;
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'started_at' => $this->faker->dateTimeBetween($attributes['assigned_at'] ?? '-1 day', 'now'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'started_at' => $this->faker->dateTimeBetween($attributes['assigned_at'] ?? '-2 days', '-1 day'),
            'completed_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ]);
    }

    public function final(): static
    {
        return $this->state(function (array $attributes) {
            $assignedAt = $attributes['assigned_at'] ?? $this->faker->dateTimeBetween('-30 days', '-7 days');
            $startedAt = $this->faker->dateTimeBetween($assignedAt, '-5 days');
            $completedAt = $this->faker->dateTimeBetween($startedAt, '-3 days');
            $kadivReviewedAt = $this->faker->dateTimeBetween($completedAt, '-1 day');

            return [
                'status' => 'final',
                'assigned_at' => $assignedAt,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'kadiv_rating' => $this->faker->numberBetween(1, 4),
                'kadiv_notes' => $this->faker->sentence(),
                'kadiv_reviewed_at' => $kadivReviewedAt,
                'director_rating' => $this->faker->numberBetween(1, 4),
                'director_notes' => $this->faker->sentence(),
                'director_reviewed_at' => $this->faker->dateTimeBetween($kadivReviewedAt, 'now'),
            ];
        });
    }

    public function inReviewDirector(): static
    {
        return $this->state(function (array $attributes) {
            $assignedAt = $attributes['assigned_at'] ?? $this->faker->dateTimeBetween('-30 days', '-7 days');
            $startedAt = $this->faker->dateTimeBetween($assignedAt, '-5 days');
            $completedAt = $this->faker->dateTimeBetween($startedAt, '-3 days');

            return [
                'status' => 'in_review_director',
                'assigned_at' => $assignedAt,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'kadiv_rating' => $this->faker->numberBetween(1, 4),
                'kadiv_notes' => $this->faker->sentence(),
                'kadiv_reviewed_at' => $this->faker->dateTimeBetween($completedAt, 'now'),
            ];
        });
    }
}
