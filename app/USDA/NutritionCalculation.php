<?php

namespace App\USDA;


class NutritionCalculation {

    private $ingredients;
    private $sumNutrients;
    private $ingredientsList = [];
    private $usdaData;

    /**
     * @param $ingredients
     * @param USDAData $usdaData
     */
    public function __construct($ingredients, USDAData $usdaData) {
        /** Set the properties */
        $this->ingredients = $ingredients;
        $this->usdaData = $usdaData;
    }

    /**
     * Calculate nutrients on a given recipe.
     */
    public function calculateNutrients() {
        /** Loop all ingredients and get the nutrients */
        foreach ($this->ingredients as $ingredient) {
            /** Get URL */
            $url = formatFoodReportURL($ingredient->food_id);

            /** Hit the USDA Service and get the search
             * If the same search was performed before, the data will be get from the cache
             * */
            $nutrients_food_data = $this->usdaData->getFoodData($url, $ingredient->food_id);
            $nutrients_food_data = $nutrients_food_data['nutrients'];

            /**
             * Calculate the nutrients of an ingredient, given the quantity in 'g'
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

    /**
     * Calc individual nutrients based on its quantity in "g".
     *
     * @param array $data
     *    Nutrient data.
     * @param int $qty
     *    Ingredient quantity in "g"
     */
    private function calcIndividualNutrients(&$data, $qty) {
        /** @var array $nutrient
         * Loop each nutrient and calculate the value by de qty in 'g'
         * */
        foreach ($data as &$nutrient) {
            $nutrient['value'] = ($qty / 100) * (float)$nutrient['value'];
        }
    }

    /**
     * Sum total nutrients for given recipe.
     */
    private function sumTotalNutrients() {
        /** Before Loop, reset the sum array */
        foreach ($this->sumNutrients as $nutrients) {
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
                    /** Create the item the the current value
                     * e. g.: if the element, have the value, 0.12, the default value
                     * will be 0.12 and not 0 like we did before to reset the values
                     * */
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