<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngredientsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_recipe')->unsigned();
            $table->string('food_id');
            $table->integer('quantity');
            $table->integer('measure')->default(1);
            $table->longText('description');

            /** Set the Foreign keys */
            $table->foreign("id_recipe")->references("id")->on("recipes")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ingredients');
    }
}
