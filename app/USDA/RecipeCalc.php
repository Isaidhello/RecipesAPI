<?php
/**
 * Created by PhpStorm.
 * User: vitor
 * Date: 21/05/16
 * Time: 17:08
 */

namespace App\USDA;


class RecipeCalc {

    public $ingredients_list = [];

    /**
     * @param $ingredients
     */
    public function __construct($ingredients) {

        foreach ($ingredients as $ingredient) {

            /** Get URL */
            $url = formatFoodReportURL($ingredient->food_id);

            /** Hit the USDA Service and get the search */
            $usda = new USDAData();
            $data = $usda->getFoodData($url, $ingredient->food_id);

            $this->calcIndividualNutrients($data, $ingredient->quantity);
            $this->sumTotalNutrients($data);

            $this->ingredients_list[] = $data;
        }

    }

    private function calcIndividualNutrients(&$data, $qty) {
        foreach ($data as &$nutrient) {
            $nutrient['value'] = ($qty / 100) * (float) $nutrient['value'];
        }
    }

    private function sumTotalNutrients(&$data) {

    }

}