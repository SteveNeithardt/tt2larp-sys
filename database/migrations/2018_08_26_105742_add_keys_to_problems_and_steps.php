<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeysToProblemsAndSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('problem_steps', function (Blueprint $table) {
			$table->unsignedInteger('problem_id');
			$table->unsignedInteger('step_id');
			$table->boolean('first_step')->default(false);

			$table->primary(['problem_id', 'step_id']);
			$table->foreign('problem_id')->references('id')->on('problems');
			$table->foreign('step_id')->references('id')->on('steps');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('problem_steps');
    }
}
