<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
	protected $table = 'problems';

	/**
	 * upon boot, declare what happens on delete
	 */
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($program) {
			foreach ($program->steps as $step) {
				$step->delete();
			}
		});
	}

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
		$steps = $this->steps()->select('id', 'name', 'description')->orderBy('name')->get();

		foreach ($steps as $step) {
			$step->first_step = $step->pivot->first_step;
			unset($step->pivot);
		}

		return $steps;
	}

	/**
	 * ProblemStation on which this Problem is active
	 */
	public function problemStation()
	{
		return $this->hasOne(ProblemStation::class, 'problem_id', 'id');
	}
}
