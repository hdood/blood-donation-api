<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Question::factory()->create(["type" => "YesNoIfYes", "text" => "Have you had any symptoms of illness or infection in the past few weeks?", "data" => ['ifYes' => 'describe them.']]);
        Question::factory()->create(["type" => "YesNoIfYes", "text" => "Have you recently traveled to areas with high rates of infectious diseases?", "data" => ['ifYes' => 'what the name of the area and the name of the disease ?']]);
        Question::factory()->create(["type" => "YesNoIfYes", "text" => "Have you had any close contact with someone who has a contagious illness, such as COVID-19?", "data" => ['ifYes' => 'what illness.']]);
        Question::factory()->create(["type" => "YesNo", "text" => "Have you received any tattoos, piercings, or acupuncture in the past year?"]);
        Question::factory()->create(["type" => "YesNo", "text" => "Have you ever used intravenous drugs, or shared needles or syringes?"]);
        Question::factory()->create(["type" => "YesNoIfYes", "text" => "Have you ever had a positive test for HIV, hepatitis B or C, or syphilis?", "data" => ['ifYes' => 'what illness.']]);
        Question::factory()->create(["type" => "YesNo", "text" => "Have you ever had a blood transfusion or received an organ transplant?"]);
        Question::factory()->create(["type" => "YesNoIfYes", "text" => "Are you currently taking any medications or have you taken any medication in the past week?", "data" => ['ifYes' => 'what are those medications.']]);
        Question::factory()->create(["type" => "Standard", "text" => "what is your height(m) ?"]);
        Question::factory()->create(["type" => "Standard", "text" => "what is your weight(kg) ?"]);
    }
}
