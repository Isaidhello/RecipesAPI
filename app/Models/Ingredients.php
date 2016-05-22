<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model {
    /** Set table name */
    protected $table = 'ingredients';

    /** Disable timestamps */
    public $timestamps = false;

    /** Make the relationship wit the Recipe table */
    public function recipe() {
        return $this->belongsTo('App\Models\Recipe', 'id_recipe');
    }

}
