<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_id' => \App\Models\ClassSubjectSection::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'content_type' => 'document',
            'content_url' => fake()->url(),
            'order_number' => fake()->randomDigitNotNull(),
            'published_at' => now(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
