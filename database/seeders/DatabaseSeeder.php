<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(33)->create()->each(function ($book) {

            $numberOfReviews = rand(0, 10);

            Review::factory()->count($numberOfReviews)
                ->good()
                ->for($book)
                ->create();
        });

        Book::factory(33)->create()->each(function ($book) {

            $numberOfReviews = rand(0, 10);

            Review::factory()->count($numberOfReviews)
                ->average()
                ->for($book)
                ->create();
        });

        Book::factory(34)->create()->each(function ($book) {

            $numberOfReviews = rand(0, 10);

            Review::factory()->count($numberOfReviews)
                ->bad()
                ->for($book)
                ->create();
        });
    }
}
