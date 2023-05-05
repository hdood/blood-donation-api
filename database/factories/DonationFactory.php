<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "amount" => fake()->numberBetween(200, 450),
            "type" => fake()->randomElement([1, 2, 3, 4]),
            "location" => "Setif",
            "date" => Carbon::now()
        ];
    }
}
