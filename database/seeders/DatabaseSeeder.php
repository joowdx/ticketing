<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Office;
use App\Models\Subcategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::query()->root()->doesntExist()) {
            User::factory()->root()->create();
        }

        $users = User::factory()->times(rand(10, 25));

        $categories = Category::factory()->times(rand(5, 12));

        $subcategories = Subcategory::factory()->times(rand(3, 15));

        Office::factory(2)
            ->has($users)
            ->has($categories->has($subcategories))
            ->create();
    }
}
