<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Faker;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for($i = 0; $i <= 50; $i++)
        {
            $recipe = Recipe::create([
                "title" => "this is $i. recipe",
                "description" => "this is $i. description",
                "instructions" => "this is $i. instruction",
                "is_favorite" => 0,
                "user_id" => 1
            ]);
        }
    }
}
