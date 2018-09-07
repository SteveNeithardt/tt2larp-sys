<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipeIngredientsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('recipe_ingredients', function (Blueprint $table) {
			$table->unsignedInteger('recipe_id');
			$table->unsignedInteger('ingredient_id')->index();
			$table->primary([ 'recipe_id', 'ingredient_id' ]);

			$table->foreign('recipe_id')->references('id')->on('recipes');
			$table->foreign('ingredient_id')->references('id')->on('ingredients');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('recipe_ingredients');
    }
}
