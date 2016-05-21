<?php

namespace App\USDA;


use Illuminate\Support\Facades\Cache;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
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

        /** Cache data */
        $this->cacheData($data, $term);

        /** Return formatted JSON */
        return $this->handleSearchData($data);
    }

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

    private function cacheData($data, $term) {
        /** Add data in Cache */
        Cache::add($term, $data, 720 * 60);
    }
}