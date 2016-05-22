<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    /** Remove the timestamps */
    public $timestamps = false;

    /** Set the table name */
    protected $table = "users";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /** This function return an array telling the validation rules
     * of this Model
     * */
    public static function rules() {
        return [
            "name" => "required|max:100",
            "email" => "required|email|unique:users",
            "password" => "required"
        ];
    }

    /** Query scope get user by token */
    public function scopeByToken($query, $token) {
        return $query->where('api_token', $token)->get();
    }
}
