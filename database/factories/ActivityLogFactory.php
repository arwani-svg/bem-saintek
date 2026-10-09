<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $activity = fake()->randomElement(ActivityLog::ACTIVITIES);

        return [
            'user_id' => User::factory(),
            'activity' => $activity,
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'properties' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ];
    }
}
