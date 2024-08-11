<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => str()->random(),
            'file_path' => str()->random(),
            'user_id' => \App\Models\User::factory(), // Assuming a campaign is linked to a user
            'total_contacts' => $this->faker->numberBetween(1, 100),
            'processed_contacts' => 0,
        ];
    }
}
