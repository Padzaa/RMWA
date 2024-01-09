<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpoonacularService
{
    /**
     * Returns API key for Spoonacular
     */
    private function getSpoonacularApiKey()
    {
        return env('SPOONACULAR_API_KEY');
    }

    /**
     * Returns recipes(with information) based on ingredients or random recipes based on parsed ingredients parameter
     * @param $requestedIngredients
     * @param $limit
     * @return array|mixed
     */
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
        $apiKey = $this->getSpoonacularApiKey();
        $url = "https://api.spoonacular.com/recipes/findByIngredients?ingredients=$ingredients&number=$limit";
        $response = Http::withHeaders([
            'x-api-key' => $apiKey
        ])->get($url);
        return $response->json();
    }

    /**
     * Get all possible information about recipes (information bulk) using API call to Spoonacular
     */
    public function getFullInformationForRecipes($recipes)
    {
        $recipesIds = $recipes->pluck('id')->join(',');
        $apiKey = $this->getSpoonacularApiKey();
        $url = "https://api.spoonacular.com/recipes/informationBulk?ids=$recipesIds";
        $response = Http::withHeaders([
            'x-api-key' => $apiKey
        ])->get($url);
        return $response->json();
    }

    /**
     * Get random recipes by calling API Spoonacular
     * @param $limit
     * @return array|mixed
     */
    public function getRandomRecipes($limit)
    {
        $apiKey = $this->getSpoonacularApiKey();
        $url = "https://api.spoonacular.com/recipes/random?number=$limit";
        $response = Http::withHeaders([
            'x-api-key' => $apiKey
        ])->get($url);
        return $response->json();
    }

}
