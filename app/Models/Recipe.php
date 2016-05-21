<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model {
    /** Set table name */
    protected $table = 'recipes';

    /** Disable timestamps */
    public $timestamps = false;

    public static function rules() {
        return [
            "name" => "required",
            "description" => "required",
            "ingredients" => "required|array"
        ];
    }
}