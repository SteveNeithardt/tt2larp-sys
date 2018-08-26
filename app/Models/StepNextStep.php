<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class StepNextStep extends Model
{
	protected $table = 'step_next_steps';

	/**
	 * Step that spawned this
	 */
	public function step()
	{
		return $this->belongsTo(Step::class, 'step_id', 'id');
	}

	/**
	 * Next Step instances
	 */
	public function nextSteps()
	{
		return $this->belongsTo(Step::class, 'next_id', 'id');
	}

	/**
	 * Ability this instance requires
	 */
	public function ability()
	{
		return $this->belongsTo(Ability::class, 'ability_id', 'id');
	}
}
