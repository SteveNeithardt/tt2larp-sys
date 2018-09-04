<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManyToManyStepNextStepAbilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('step_next_step_abilities', function (Blueprint $table) {
			$table->unsignedInteger('step_next_step_id');
			$table->unsignedInteger('ability_id');
			$table->integer('value');

			$table->foreign('step_next_step_id')->references('id')->on('step_next_steps');
			$table->foreign('ability_id')->references('id')->on('abilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('step_next_step_abilities');
    }
}
