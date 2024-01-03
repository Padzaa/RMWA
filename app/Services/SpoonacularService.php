<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpoonacularService
{
    public function getRecipes($requestedIngredients, $limit)
    {
        if ($requestedIngredients->isNotEmpty()) {
            $recipes = collect($this->getRecipesByIngredients($requestedIngredients, $limit));
            $recipes = $this->getFullInformationForRecipes($recipes);
        } else {
            $recipes = $this->getRandomRecipes($limit);
        }
        return $recipes;
    }

    /**
     *  Makes an API call to Spoonacular ang returns recipes based on ingredients
     * @param $ingredients
     * @param $limit
     * @return array|mixed
     */
    public function getRecipesByIngredients($ingredients, $limit)
    {
        $ingredients = $ingredients->pluck('name')->join(',');
        $apiKey = env('SPOONACULAR_API_KEY');
        $baseUrl = "https://api.spoonacular.com/recipes/findByIngredients?ingredients=$ingredients&number=$limit&apiKey=$apiKey";
        $response = Http::get($baseUrl);
        return $response->json();
    }

    /**
     * Get all possible information about recipes (information bulk) using API call to Spoonacular
     */
    public function getFullInformationForRecipes($recipes)
    {
        $ids = $recipes->pluck('id')->join(',');
        $apiKey = env('SPOONACULAR_API_KEY');
        $baseUrl = "https://api.spoonacular.com/recipes/informationBulk?ids=$ids&apiKey=$apiKey";
        $response = Http::get($baseUrl);
        return $response->json();
    }

    /**
     * Get random recipes by calling API Spoonacular
     * @param $limit
     * @return array|mixed
     */
    public function getRandomRecipes($limit)
    {
        $apiKey = env('SPOONACULAR_API_KEY');
        $baseUrl = "https://api.spoonacular.com/recipes/random?number=$limit&apiKey=$apiKey";
        $response = Http::get($baseUrl);
        return $response->json();
    }

}
