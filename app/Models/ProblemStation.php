<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemStation extends Model
{
	protected $table = 'problem_stations';

	/**
	 * relations to always eager load
	 */
	protected $with = [
		'problem',
		'step',
		'step.stepNextSteps',
		'step.stepPreviousSteps',
	];

	/**
	 * Station(s) referencing this Model
	 */
	public function stations()
	{
		return $this->morphMany(Station::class, 'station');
	}

	/**
	 * current problem active on station
	 */
	public function problem()
	{
		return $this->belongsTo(Problem::class, 'problem_id', 'id');
	}

	/**
	 * current step at which problem is on station
	 */
	public function step()
	{
		return $this->belongsTo(Step::class, 'step_id', 'id');
	}
}
