<?php

/** Return USDA API Config */
return [
    /** Token to use on USDA API */
    "token" => "G5qYeGH4MD4hb14kb4njVJ8Xa30Alx1nI9TfdUNS",

    /** Base URL of the USDA Service */
    "base_url" => "http://api.nal.usda.gov/ndb/",

    /** Search URI of the USDA API */
    "search_uri" => "search/?format=json&q=%s&api_key=%s&sort=%s",

    /** Food Report URI of the USDA API */
    "food_report_uri" => "reports/?ndbno=%s&type=b&format=json&api_key=%s",

	"cache_expiration_time" => 168 * 24,
];
