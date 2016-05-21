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
            $table->bigInteger('id_recipe')->unsigned();
            $table->integer('quantity');
            $table->integer('measure')->default(1);
            $table->longText('description');

            /** Set the Foreign keys */
            $table->foreign("id_recipe")->references("id_user")->on("users");
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
