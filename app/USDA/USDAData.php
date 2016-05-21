<?php

namespace App\USDA;


use Illuminate\Support\Facades\Cache;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class USDAData {

    private $httpClient;

    public function __construct() {
        /** @var Object httpClient
         * Create Guzzle client object
         */
        $this->httpClient = new GuzzleHttp\Client();
    }

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

        return $this->handleSearchData($data);
    }

    private function handleSearchData($data) {
        return $data;
    }

    private function hitService($url) {
        /** @var Object $response
         * Call the USDA API
         */
        $url = "http://api.nal.usda.gov/ndb/search/";

        try {
            $response = $this->httpClient->get($url);

            /**  */

            return $response->getBody()->getContents();
        } catch (RequestException $ex) {
            /** Log the Service error */
            Log::error($ex->getResponse());

            /** Return null telling that something went wrong */
            return [
                "error" => true,
                "message" => $ex->getMessage()
            ];
        }
    }
}