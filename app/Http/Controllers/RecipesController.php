<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Recipe;
use App\USDA\NutritionCalculation;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use \Validator;

class RecipesController extends Controller {

    public function __construct() {
        /** Set the middleware for this controller */
        $this->middleware('token');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        /** Get user by his token */
        $user = getUserModel($request);

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
        /** @var string $data
         * Get the JSON from the Payload
         * */
        $data = $request->json()->all();

        /** validate the data */
        $validator = Validator::make($data, Recipe::rules());
        if ($validator->fails()) {
            /** Back to user the error */
            return serviceErrorMessage($validator->messages(), 400);
        }

        /** Begin a transaction on database */
        DB::beginTransaction();

        try {
            /** Get user by his token */
            $user = getUserModel($request);

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

            /** If everything went right, commit the transaction */
            DB::commit();

            /** return the success message */
            return response()->json(['message' => 'ok']);

        } catch (Exception $e) {
            /** If something went wrong, log on Laravel */
            Log::error($e->getMessage());

            /** Rollback the Transaction */
            DB::rollback();

            /** Return the error message */
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

        /** Calculate all nutrients */
        $recipe->aggregates_nutrients = $nutrition_facts->calculateNutrients();

        /** Remove the nutrient_id */
        $recipe->aggregates_nutrients = array_values($recipe->aggregates_nutrients);

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
    public function update(Request $request, $id) {
        /** @var array $data
         * Get JSON from Payload
         * */
        $data = $request->json()->all();
        /** Validate the data */
        $validator = Validator::make($data, Recipe::rules());

        if ($validator->fails()) {
            /** Back to user the error */
            return serviceErrorMessage($validator->messages(), 400);
        }

        /** Begin a transaction in database */
        DB::beginTransaction();

        try {
            /** Save the Recipe */
            $recipe = Recipe::find($id);
            $recipe->name = $data['name'];
            $recipe->description = $data['description'];
            $recipe->save();

            /** Instead update every ingredient
             * Remove them all, and add again
             * */
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

            /** If everything went right, commit the transaction */
            DB::commit();

            /** return the success message */
            return response()->json(['update' => 'ok']);

        } catch (Exception $e) {
            /** if something went wrong, log on Laravel */
            Log::error($e->getMessage());

            /** Rollback the transaction on Laravel */
            DB::rollback();

            /** return the Error message */
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
        /** @var object $recipe
         * Find the Model
         * */
        $recipe = Recipe::find($id);

        /** And remove */
        $recipe->delete();

        /** return the success message */
        return response()->json(['deleted' => 'OK']);
    }
}
