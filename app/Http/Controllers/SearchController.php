<?php

namespace App\Http\Controllers;

use App\USDA\USDAData;

class SearchController extends Controller {

    public function searchByTerm($term) {

        /** Get URL */
        $url = formatSearchURL($term);

        /** Hit the USDA Service and get the search */
        $usda = new USDAData();
        $data = $usda->performSearch($url, $term);

        return response()->json(['data' => $data]);
    }


}
