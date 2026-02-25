<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassSubjectSection>
 */
class ClassSubjectSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_subject_id' => \App\Models\ClassSubject::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'order_number' => fake()->randomDigitNotNull(),
            'is_published' => true,
        ];
    }
}
