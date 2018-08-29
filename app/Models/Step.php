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
		return $this->hasMany(StepNextStep::class, 'step_id', 'id')->with('ability');
	}

	/**
	 * StepNextStep that point to this Step
	 */
	public function stepPreviousSteps()
	{
		return $this->hasMany(StepNextStep::class, 'next_step_id', 'id')->with('ability');
	}

	/**
	 * gets all related step ids, ordered along the execution tree
	 * (uses step_next_steps)
	 */
	public function getEdges()
	{
		$all_edges = [];

		foreach ($this->stepNextSteps as $stepNextStep) {
			//$nodes[$stepNextStep->next_step_id] = [ 'id' => $stepNextStep->ability_id, 'min_value' => $stepNextStep->min_value ];

			$nextStep = Step::find($stepNextStep->next_step_id);

			$all_edges[] = [
				'id' => $stepNextStep->id,
				'from' => $stepNextStep->step_id,
				'to' => $stepNextStep->next_step_id,
				'type' => $stepNextStep->type,
				'ability_id' => $stepNextStep->ability_id,
				'min_value' => $stepNextStep->min_value,
				'code' => $stepNextStep->code,
			];

			$new_edges = $nextStep->getEdges();
			$all_edges = array_merge($all_edges, $new_edges);
		}

		$ids = [];
		$edges = [];
		foreach ($all_edges as $edge) {
			if (isset($ids[$edge['id']]))
				continue;
			$ids[$edge['id']] = true;
			$edges[] = $edge;
		}

		return $edges;
	}
}
