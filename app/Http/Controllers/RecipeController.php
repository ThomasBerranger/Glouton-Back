<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Recipe::class, 'recipe');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();

        return RecipeResource::collection($user?->recipes()->with('productsRecipes.product')->get());
    }

    public function store(StoreRecipeRequest $request): RecipeResource
    {
        $recipe = Recipe::create($request->validated());

        return RecipeResource::make($recipe);
    }

    public function destroy(Recipe $recipe): Response
    {
        $recipe->delete();

        return response()->noContent();
    }
}
