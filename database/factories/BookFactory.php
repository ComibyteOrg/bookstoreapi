<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13,
            'author' => Author::factory()->create()->name,
            'publisher' => $this->faker->company,
            'year' => $this->faker->year,
            'genre' => $this->faker->randomElement([
                'Fiction', 
                'Non-Fiction', 
                'Science Fiction', 
                'Fantasy', 
                'Mystery',
                'Romance',
                'Thriller',
                'Biography'
            ]),
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
