<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;

/**
 * Format USDA Search Service URL.
 *
 * @param string $term
 *    Search term.
 *
 * @return string $url
 *    Formatted URL.
 */
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

/**
 * Format USDA Food Report Service URL.
 *
 * @param string $food_id
 *    Food USDA ID term.
 *
 * @return string $url
 *    Formatted URL.
 */
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

/**
 * Generate an service error message.
 *
 * @param string $message
 *    Error message.
 * @param int    $code
 *    Error code.
 *
 * @return response.
 *  JSON Error response.
 */
function serviceErrorMessage($message, $code) {
    /** Return encoded JSON */
    return response()->json(['error' => $message], $code);
}

/**
 * Get user model from database.
 *
 * @param Request $request
 *    HTTP Request.
 *
 * @return object $user
 *    A valid database user object.
 */
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