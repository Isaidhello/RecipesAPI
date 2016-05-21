<?php

/** Return USDA API Config */
return [
    "token" => "G5qYeGH4MD4hb14kb4njVJ8Xa30Alx1nI9TfdUNS",
    "base_url" => "http://api.nal.usda.gov/ndb/",
    "search_uri" => "search/?format=json&q=%s&api_key=%s&sort=%s",
    "food_report_uri" => "reports/?ndbno=%d&type=b&format=json&api_key=%s"
];
