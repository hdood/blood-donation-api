<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donor>
 */
class DonorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "gender" => fake()->randomElement(['male', "female"]),
            "email" => fake()->email(),
            "password" => Hash::make("password"),
            "phone" => fake()->randomElement(['06', '05', '07']) . fake()->randomNumber(8),
            "address" => fake()->address(),
            "bloodType" => fake()->randomElement(["a", "b", "ab", "o"]),
            "rhFactor" => fake()->boolean(),
            "dob" => fake()->dateTimeBetween("-30years", "-18years"),
        ];
    }
}
