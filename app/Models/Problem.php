<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
	protected $table = 'problems';

	/**
	 * all steps related to this problem
	 */
	public function steps()
	{
		return $this->belongsToMany(Step::class, 'problem_steps', 'problem_id', 'step_id')->withPivot('first_step');
	}

	/**
	 * First step (should always contain a single element
	 */
	public function firstSteps()
	{
		return $this->steps()->wherePivot('first_step', '=', 1);
	}

	/**
	 * gets all related steps as list ordered by name
	 */
	public function getSteps()
	{
		$steps = $this->steps()->select('id', 'name')->orderBy('name')->get();

		foreach ($steps as $step) {
			$step->first_step = $step->pivot->first_step;
			unset($step->pivot);
		}

		return $steps;
	}
}
