<?php

namespace App\USDA;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class USDAData {

    private $httpClient;

    public function __construct() {
        /** @var Object httpClient
         * Create Guzzle client object
         */
        $this->httpClient = new GuzzleHttp\Client();
    }

    /**
     * Make an API call to USDA Service.
     *
     * @param string $url
     *    API Formatted URL.
     * @param string $term
     *    Search Term.
     * @return JSON data.
     *    Return, if sucessfull a JSON data with current food list.
     */
    public function performSearch($url, $term) {
        /** Check if cache exists */
        if (Cache::has($term)) {
            $data = Cache::get($term);
            return $this->handleSearchData($data);
        }

        /** Hit the Service to get the search data */
        $data = $this->hitService($url);
        if (($data['error'])) {
            return $data;
        }

        /** Cache data */
        $this->cacheData($data, $term);

        /** Return formatted JSON */
        return $this->handleSearchData($data);
    }

    /**
     * Get Food Data from USDA service.
     *
     * @param string $url
     *    Formatted USDA Service URL.
     * @param string $food_id
     *    USDA Food ID.
     * @return JSON data
     *    Formatted JSON object.
     */
    public function getFoodData($url, $food_id) {

        /** Check if cache exists */
        if (Cache::has($food_id)) {
            $data = Cache::get($food_id);
            return $this->handleReportData($data);
        }

        /** Hit the Service to get the search data */
        $data = $this->hitService($url);
        if (($data['error'])) {
            return $data;
        }

        /** Cache data */
        $this->cacheData($data, $food_id);

        /** Return formatted JSON */
        return $this->handleReportData($data);
    }

    /**
     * Given a Search USDA JSON food list , format it to make it simpler.
     *
     * @param JSON $data
     *    Returned USDA Service JSON
     * @return JSON $data
     *    Formatted JSON food List.
     */
    private function handleSearchData($data) {
        /** Decode Data JSON */
        $json = json_decode($data['body']);
        $items = $json->list->item;

        /** Loop each element and format items list */
        $formatted_list = [];
        foreach ($items as $item) {
            $formatted_list[$item->ndbno] = $item->name;
        }
        return $formatted_list;
    }

    /**
     * Given a food USDA JSON, parse it and make it simpler.
     *
     * @param JSON $data
     *    USDA Response JSON
     * @return JSON $data
     *    Simpler food report JSON.
     */
    private function handleReportData($data) {
        $json = json_decode($data['body']);
        $nutrients = $json->report->food->nutrients;

        /** Loop each element and format item */
        $formatted_list = [];
        foreach ($nutrients as $nutrient) {
            $formatted_list[$nutrient->nutrient_id] = [
                "name" => $nutrient->name,
                "unit" => $nutrient->unit,
                "value" => $nutrient->value,
            ];
        }
        return $formatted_list;
    }

    /**
     * Connects to USDA Service and make and API Call.
     *
     * @param string $url
     *    Formatted Service URL.
     * @return response json
     *    Service JSON response.
     */
    private function hitService($url) {
        /** @var Object $response
         * Call the USDA API
         */

        try {
            /** @var  $response
             * Make request to USDA
             */
            $response = $this->httpClient->get($url);

            /** Return data */
            return [
                "error" => false,
                "body" => $response->getBody()->getContents(),
            ];
        } catch (ClientException $ex) {
            /** Log the Service error */
            Log::error($ex->getResponse());

            /** Return null telling that something went wrong */
            return [
                "error" => true,
                "error_message" => $ex->getResponse()->getReasonPhrase(),
            ];
        } catch (RequestException $ex) {
            /** Log the Service error */
            Log::error($ex->getResponse());
            /** Return null telling that something went wrong */
            return [
                "error" => true,
                "error_message" => $ex->getMessage(),
            ];
        }
    }

    /**
     * Cache Service response.
     * In order to avoid hitting USDA service every search or report request,
     * we create a cache based either on search term, or food id from USDA, this
     * makes our own service response API much faster, and even creates a backup
     * in case of a USDA service downtime.
     *
     * @param object $data
     *    Service response data.
     * @param string $term
     *    Cache key, based on term.
     */
    private function cacheData($data, $term) {
        /** Add data in Cache for 7 days */
        Cache::add($term, $data, Config::get('api.cache_expiration_time'));
    }
}