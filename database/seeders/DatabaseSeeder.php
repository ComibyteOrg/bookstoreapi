<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\AuthorSeeder;
use Database\Seeders\BookSeeder;
use App\Models\Author;
use App\Models\Book;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Author::factory()
        ->count(10)
        ->has(Book::factory()->count(3))
        ->create();
        
        Author::factory()
        ->has(Book::factory())
        ->create();
    
        // Create a book with specific attributes
        Book::factory()
        ->for(Author::factory())
        ->create([
            'genre' => 'Science Fiction',
            'author_id' => Author::factory()->create()->id,
        ]);

        $this->call([
            AdminUserSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
        ]);
    }

}
