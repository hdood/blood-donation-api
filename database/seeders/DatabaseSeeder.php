<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Admin::factory()->create([
            'name' => 'Mahdi Bouguerzi',
            'email' => 'mahdi@test.com',
            'password' => Hash::make('password'),
            "phone" => "06" . fake()->randomNumber(8),
            "address" => fake()->address()
        ]);

        Donor::factory(['active' => 1])->count(10)->create();

        Donor::factory(10)->create();

        Patient::factory(10)->create();

        $this->call(QuestionSeeder::class);
    }
}
