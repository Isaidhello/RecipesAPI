<?php
/**
 * RESTfull API for USDA Search and food report.
 *
 * Available verbs and its current functions:
 *
 * Verb GET, route /search
 * @example /search/{term}?key=USER_API_KEY Get a list of food items from USDA.
 * @see searchByTerm
 *
 * Verb GET, route /search
 * @example /report/{usda_food_id}?key=USER_API_KEY Get complete information from given food from USDA API.
 * @see getFoodDetail
 *
 */

namespace App\Http\Controllers;

use App\USDA\USDAData;

class SearchController extends Controller {

    private $usdaData;

    public function __construct(USDAData $usda_data) {
        $this->usdaData = $usda_data;
        $this->middleware('token');
    }

    /**
     * Given a food name, check it against USDA API.
     *
     * @param string $term
     *    Food name.
     * @return JSON data
     *    USDA Food list JSON object already parsed to this APP format.
     */
    public function searchByTerm($term) {

        /** Get URL */
        $url = formatSearchURL($term);

        /** Hit the USDA Service and get the search result */
        $data = $this->usdaData->performSearch($url, $term);

        /** Return list items */
        return response()->json([$data]);
    }

    /**
     * Given a usda_food_id, check it against USDA API.
     *
     * @param string $food_id
     *    USDA Food ID.
     * @return JSON data
     *    Formatted food JSON object.
     */
    public function getFoodDetail($food_id) {
        /** Get URL */
        $url = formatFoodReportURL($food_id);

        /** Hit the USDA Service and get the report results */
        $data = $this->usdaData->getFoodData($url, $food_id);

        /** Return list items */
        return response()->json([$data]);
    }

}
