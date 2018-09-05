<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
	protected $table = 'steps';

	/**
	 * Problem(s) this step is related to
	 */
	public function problem()
	{
		return $this->belongsToMany(Problem::class, 'problem_steps', 'step_id', 'problem_id')->withPivot('first_step');
	}

	/**
	 * Next Step instances
	 */
	public function nextSteps()
	{
		return $this->belongsToMany(Step::class, 'step_next_steps', 'step_id', 'next_step_id');
	}

	/**
	 * StepNextStep this Step has
	 */
	public function stepNextSteps()
	{
		return $this->hasMany(StepNextStep::class, 'step_id', 'id')->with('abilities')->with('codes');
	}

	/**
	 * StepNextStep that point to this Step
	 */
	public function stepPreviousSteps()
	{
		return $this->hasMany(StepNextStep::class, 'next_step_id', 'id')->with('abilities')->with('codes');
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

			$edge = [
				'id' => $stepNextStep->id,
				'from' => $stepNextStep->step_id,
				'to' => $stepNextStep->next_step_id,
				'codes' => [],
				'abilities' => [],
			];
			foreach ($stepNextStep->codes as $code) {
				$edge['codes'][] = (object)[ 'code' => $code->code ];
			}
			foreach ($stepNextStep->abilities as $ability) {
				$edge['abilities'][] = (object)[ 'id' => $ability->id, 'value' => $ability->pivot->value ];
			}

			$all_edges[] = $edge;

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
