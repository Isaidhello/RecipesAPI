<?php

use App\Models\User;
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

function serviceErrorMessage($message, $code) {
    /** Return encoded JSON */
    return response()->json(['error' => $message], $code);
}

function getUserModel($request) {
    /** Check if the Token is on Header */
    $token = '';
    if ($request->hasHeader('APIAuth')) {
        $token = $request->header('APIAuth');
        /** If not on Header, try to get via query String */
    } else if ($request->has('key')) {
        $token = $request->get('key');
    }

    /** Load the user by his token */
    $user = User::byToken($token);

    /** return the first record */
    return $user->first();

}