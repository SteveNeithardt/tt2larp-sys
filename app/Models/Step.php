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
	public function getTree()
	{
		$nodes = [];
		$edges = [];

		$nodes[] = [ 'id' => $this->id, 'label' => $this->name ];

		foreach ($this->stepNextSteps as $stepNextStep) {
			//$nodes[$stepNextStep->next_step_id] = [ 'id' => $stepNextStep->ability_id, 'min_value' => $stepNextStep->min_value ];

			$nextStep = Step::find($stepNextStep->next_step_id);

			$edges[] = [
				'id' => $stepNextStep->step_id . ':' . $stepNextStep->next_step_id,
				'from' => $stepNextStep->step_id,
				'to' => $stepNextStep->next_step_id,
				'arrows' => 'to',
				'ability_id' => $stepNextStep->ability_id,
				'min_value' => $stepNextStep->min_value
			];

			$tree = $nextStep->getTree();
			$nodes = array_merge($nodes, $tree['nodes']);
			$edges = array_merge($edges, $tree['edges']);
		}

		$result = compact('nodes', 'edges');
		return $result;
	}
}
