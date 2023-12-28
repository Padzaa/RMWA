<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpoonacularService
{
    public function getRecipes($requestedIngredients, $limit)
    {
        if (!empty($requestedIngredients)) {
            $recipes = $this->getRecipesByIngredients($requestedIngredients, $limit);
            $recipes = $this->getInformationBulk($recipes);
        } else {
            $recipes = $this->getRandomRecipes($limit);
        }
        return $recipes;
    }

    /**
     *  Makes an API call to Spooncular ang returns recipes based on ingredients
     * @param $ingredients
     * @param $limit
     * @return array|mixed
     */
    public function getRecipesByIngredients($ingredients, $limit)
    {
        /*Returns every name of the ingredient, joined by comma, Filter(remove null values) is used because request
        that was sending and object separated properties into different arrays*/
        $ingredients = collect($ingredients)->pluck('name')->filter()->join(',');
        $apiKey = env('SPOONACULAR_API_KEY');
        $baseUrl = "https://api.spoonacular.com/recipes/findByIngredients?ingredients=$ingredients&number=$limit&apiKey=$apiKey";
        $response = Http::get($baseUrl);
        return $response->json();
    }

    /**
     * Get all possible information about recipes (information bulk) using API call to Spooncular
     */
    public function getInformationBulk($recipes)
    {
        /*Returns every id of the returned recipes, joined by comma, Filter(remove null values if exist)*/
        $ids = collect($recipes)->pluck('id')->filter()->join(',');
        $apiKey = env('SPOONACULAR_API_KEY');
        $baseUrl = "https://api.spoonacular.com/recipes/informationBulk?ids=$ids&apiKey=$apiKey";
        $response = Http::get($baseUrl);
        return $response->json();
    }

    /**
     * Get random recipes by calling API Spooncular
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
