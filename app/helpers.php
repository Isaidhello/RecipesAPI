<?php

use Illuminate\Support\Facades\Config;

function formatSearchURL($term) {
    /** @var  $token
     * Store the USA API Token
     */
    $token = Config::get('api.token');

    /** @var $base_url */
    $base_url = Config::get('api.base_url');

    /** @var $search_uri */
    $search_uri = Config::get('api.search_uri');
    $search_uri = sprintf($search_uri, $term, $token, 'n');

    /** Return full URL */
    return $base_url . $search_uri;
}

function formatFoodReportURL($food_id) {
    /** @var  $token
     * Store the USA API Token
     */
    $token = Config::get('api.token');

    /** @var $base_url */
    $base_url = Config::get('api.base_url');

    /** @var $search_uri */
    $search_uri = Config::get('api.food_report_uri');
    $search_uri = sprintf($search_uri, $food_id, $token);

    /** Return full URL */
    return $base_url . $search_uri;
}

function returnAuthError($message, $code) {
    return response()->json(['error' => $message], $code);
}