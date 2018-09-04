<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class StepNextStep extends Model
{
	protected $table = 'step_next_steps';

	//@todo possibliity of having 'AND' relations within a single step (some kind of morphto many-to-many relationship to abilities)

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
	 * Abilities this instance requires
	 */
	public function abilities()
	{
		return $this->belongsToMany(Ability::class, 'step_next_step_abilities', 'step_next_step_id', 'ability_id', 'id', 'id')->withPivot('value');
	}
	
	/**
	 * Codes this instance requires
	 */
	public function codes()
	{
		return $this->morphMany(Code::class, 'coded');
	}
}
