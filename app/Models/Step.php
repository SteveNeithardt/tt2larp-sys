<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
	protected $table = 'steps';

	/**
	 * Next Step instances
	 */
	public function nextSteps()
	{
		return $this->belongsToMany(Step::class, 'step_next_steps', 'step_id', 'next_step_id')->withPivot('ability_id', 'min_value');
	}

	/**
	 * StepNextStep this Step has
	 */
	public function stepNextSteps()
	{
		return $this->hasMany(StepNextStep::class, 'step_id', 'id');
	}

	/**
	 * StepNextStep that point to this Step
	 */
	public function stepPreviousSteps()
	{
		return $this->hasMany(StepNextStep::class, 'next_step_id', 'id')->with('ability');
	}
}
