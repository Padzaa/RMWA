<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;
class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //List of ingredients($ingredients)
        $ingredients = [
            "Tomato",
            "Onion",
            "Garlic",
            "Lettuce",
            "Cheese",
            "Chicken",
            "Beef",
            "Fish",
            "Pasta",
            "Rice",
            "Carrot",
            "Bell Pepper",
            "Broccoli",
            "Mushroom",
            "Spinach",
            "Potato",
            "Egg",
            "Bread",
            "Avocado",
            "Cucumber",
            "Cilantro",
            "Lime",
            "Chili",
            "Soy Sauce",
            "Olive Oil",
            "Balsamic Vinegar",
            "Salt",
            "Pepper",
            "Cumin",
            "Paprika",
            "Rosemary",
            "Thyme",
            "Basil",
            "Oregano",
            "Parmesan Cheese",
            "Honey",
            "Mustard",
            "Mayonnaise",
            "Ketchup",
            "Salsa",
            "Guacamole",
            "Sour Cream",
            "Yogurt",
            "Tofu",
            "Quinoa",
            "Black Beans",
            "Chickpeas",
            "Lentils",
            "Walnuts",
            "Almonds",
            "Peanuts",
            "Cashews",
            "Sunflower Seeds",
            "Chia Seeds",
            "Flax Seeds",
            "Pineapple",
            "Mango",
            "Strawberries",
            "Blueberries",
            "Raspberry",
            "Banana",
            "Orange",
            "Apple",
            "Grapes",
            "Watermelon",
            "Cantaloupe",
            "Kiwi",
            "Peach",
            "Plum",
            "Fig",
            "Dates",
            "Chocolate",
            "Cinnamon",
            "Vanilla Extract",
            "Maple Syrup",
            "Coconut Milk",
            "Sesame Oil",
            "Coriander",
            "Dill",
            "Ginger",
            "Turmeric",
            "Nutmeg",
            "Pine Nuts",
            "Pumpkin Seeds",
            "Goat Cheese",
            "Feta Cheese",
            "Ricotta Cheese",
            "Brie Cheese",
            "Salmon",
            "Shrimp",
            "Scallops",
            "Clams",
            "Crab",
            "Tuna",
            "Lobster"
        ];
        for($i = 0; $i < count($ingredients);$i++) {
            Ingredient::create([
                "name" => "$ingredients[$i]",

            ]);
        }
    }
}
