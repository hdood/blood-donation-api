<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Donor;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Admin::factory(10)->create();
        Admin::factory()->create([
            'name' => 'Mahdi Bouguerzi',
            'email' => 'mahdi@test.com',
            'gender' => 'male',
            'password' => Hash::make('password'),
            "phone" => "06" . fake()->randomNumber(8),
            "address" => fake()->address()
        ]);

        Donor::factory(10)->create();





        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
