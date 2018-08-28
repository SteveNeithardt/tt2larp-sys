<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStepNextStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('step_next_steps', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('step_id');
			$table->unsignedInteger('next_step_id');
			$table->enum('type', ['ability', 'code']);
			$table->unsignedInteger('ability_id')->nullable();
			$table->integer('min_value')->nullable();
			$table->string('code')->nullable();
			$table->timestamps();

			$table->foreign('step_id')->references('id')->on('steps');
			$table->foreign('next_step_id')->references('id')->on('steps');
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
		Schema::dropTable('step_next_steps');
    }
}
