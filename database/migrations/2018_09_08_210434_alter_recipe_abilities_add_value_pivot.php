<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecipeAbilitiesAddValuePivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('recipe_abilities', function (Blueprint $table) {
			$table->integer('value');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('recipe_abilities', function (Blueprint $table) {
			$table->dropColumn('value');
		});
    }
}
