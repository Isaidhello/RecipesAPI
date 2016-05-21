<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model {
    /** Set table name */
    protected $table = 'recipes';

    /** Disable timestamps */
    public $timestamps = false;

    /** Relation with ingredients */
    public function ingredients() {
        return $this->hasMany('App\Models\Ingredients', 'id_recipe');
    }

    /** Scope get recipe by user */
    public function scopeByUser($query, $id_user) {
        return $query->where('id_user', $id_user)->get();
    }

    public static function rules() {
        return [
            "name" => "required",
            "description" => "required",
            "ingredients" => "required|array"
        ];
    }
}