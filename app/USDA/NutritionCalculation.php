<?php
/**
 * Created by PhpStorm.
 * User: vitor
 * Date: 21/05/16
 * Time: 17:08
 */

namespace App\USDA;


class NutritionCalculation {

    private $ingredients;
    private $sumNutrients;
    public $ingredientsList = [];

    /**
     * @param $ingredients
     */
    public function __construct($ingredients) {
        /** Set the properties */
        $this->ingredients = $ingredients;
    }

    public function calculateNutrients() {
        /** Loop all ingredients and get the nutrients */
        foreach ($this->ingredients as $ingredient) {
            /** Get URL */
            $url = formatFoodReportURL($ingredient->food_id);

            /** Hit the USDA Service and get the search */
            $usda = new USDAData();
            $nutrients_food_data = $usda->getFoodData($url, $ingredient->food_id);

            /**
             * Calculate the nutrients of an ingredient, given the quantity on 'g'
             */
            $this->calcIndividualNutrients($nutrients_food_data, $ingredient->quantity);
            $this->ingredientsList[$ingredient->food_id] = $nutrients_food_data;
        }
        /** Make a copy of the first element, to have the exactly structure
         * to make the SUM of all elements
         */
        $this->sumNutrients = array_values($this->ingredientsList)[0];

        /** Now, sum the ingredients */
        $this->sumTotalNutrients($this->ingredientsList);
        return $this->sumNutrients;
    }

    private function calcIndividualNutrients(&$data, $qty) {
        foreach ($data as &$nutrient) {
            $nutrient['value'] = ($qty / 100) * (float)$nutrient['value'];
        }
    }

    private function sumTotalNutrients() {
        /** Before Loop, reset the sum array */
        foreach ($this->sumNutrients as &$nutrients) {
            $nutrients['value'] = 0;
        }

        /** Loop each element and sum all the nutrients */
        foreach ($this->ingredientsList as $food_id => $nutrients) {
            /** Now loop each nutrient and sum */
            foreach ($nutrients as $nutrient_id => $nutrient) {
                /** Sum the Value */

                /** If the nutrient do not exists on sum array, create it */
                if (isset($this->sumNutrients[$nutrient_id]['value'])) {
                    $this->sumNutrients[$nutrient_id]['value'] += $nutrient['value'];
                } else {
                    $this->sumNutrients[$nutrient_id] = [
                        'name' => $nutrient['name'],
                        'unit' => $nutrient['unit'],
                        'value' => $nutrient['value']
                    ];
                }
            }
        }
    }

}