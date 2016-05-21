<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use \Validator;

class RecipesController extends Controller {

    public function __construct() {
//        $this->middleware('token');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
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
            foreach($data['ingredients'] as $food_id => $qty) {
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
    public function show($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
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
