<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Recipe;
use App\Models\User;
use App\USDA\NutritionCalculation;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use \Validator;

class RecipesController extends Controller {

    public function __construct() {
        $this->middleware('token');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $user = $this->getUserData($request);

        /** Get all recipes with its ingredients*/
        $recipes = Recipe::with('ingredients')->byUser($user->id);

        /** Return All user Recipes */
        return $recipes->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        /** validate the data */
        $data = $request->json()->all();
        $validator = Validator::make($data, Recipe::rules());
        if ($validator->fails()) {
            /** Back to user the error */
            return serviceErrorMessage($validator->messages(), 400);
        }

        DB::beginTransaction();

        try {

            $user = $this->getUserData($request);

            /** Save the Recipe */
            $recipe = new Recipe();
            $recipe->name = $data['name'];
            $recipe->description = $data['description'];
            $recipe->id_user = $user->id;
            $recipe->save();

            /** Loop each ingredient and save it to a recipe */
            foreach ($data['ingredients'] as $food_id => $qty) {
                $ingredient = new Ingredients();
                $ingredient->id_recipe = $recipe->id;
                $ingredient->food_id = $food_id;
                $ingredient->quantity = $qty;
                $ingredient->save();
            }

            DB::commit();

            return response()->json(['message' => 'ok']);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return serviceErrorMessage($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        /** Get all recipes with its ingredients*/
        $recipe = Recipe::with('ingredients')->find($id);

        /** Loop all ingredients, getting data from the cache */
        $nutrition_facts = new NutritionCalculation($recipe->ingredients);
        $totalNutrients = $nutrition_facts->calculateNutrients();
        $recipe->aggregates_nutrients = $totalNutrients;

        /** Return All user Recipes */
        return $recipe->toJson();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateRecipe(Request $request, $id) {
        /** validate the data */
        $data = $request->json()->all();
        var_dump($data);
        die;
        $validator = Validator::make($data, Recipe::rules());

        if ($validator->fails()) {
            /** Back to user the error */
            return serviceErrorMessage($validator->messages(), 400);
        }

        DB::beginTransaction();

        try {

            /** Save the Recipe */
            $recipe = Recipe::find($id);
            $recipe->name = $data['name'];
            $recipe->description = $data['description'];
            $recipe->save();

            foreach ($recipe->ingredients as $ingredient) {
                $ingredient->delete();
            }

            /** Loop each ingredient and save it to a recipe */
            foreach ($data['ingredients'] as $food_id => $qty) {
                $ingredient = new Ingredients();
                $ingredient->id_recipe = $recipe->id;
                $ingredient->food_id = $food_id;
                $ingredient->quantity = $qty;
                $ingredient->save();
            }

            DB::commit();

            return response()->json(['message' => 'ok']);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return serviceErrorMessage($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $recipe = Recipe::find($id);
        $recipe->delete();

        return response()->json(['deleted' => 'OK']);
    }

    private function getUserData($request) {

        if ($request->hasHeader('APIAuth')) {
            $token = $request->header('APIAuth');
        } else if ($request->has('key')) {
            $token = $request->get('key');
        }

        $user = User::byToken($token);

        return $user->first();

    }

}
