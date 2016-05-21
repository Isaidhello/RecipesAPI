<?php

namespace App\Http\Controllers;

use App\USDA\USDAData;

class SearchController extends Controller {

    public function __construct() {
//        $this
    }

    public function searchByTerm($term) {

        /** Get URL */
        $url = formatSearchURL($term);

        /** Hit the USDA Service and get the search */
        $usda = new USDAData();
        $data = $usda->performSearch($url, $term);

        /** Return list items */
        return response()->json([$data]);
    }

    public function getFoodDetail($food_id) {
        /** Get URL */
        $url = formatFoodReportURL($food_id);

        /** Hit the USDA Service and get the search */
        $usda = new USDAData();
        $data = $usda->getFoodData($url, $food_id);

        /** Return list items */
        return response()->json([$data]);
    }

}
