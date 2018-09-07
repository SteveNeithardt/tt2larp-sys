<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
	protected $table = 'steps';

	/**
	 * Properties added to a serialization of this instance
	 */
	protected $appends = [
		'nextEdgeCount',
		'previousEdgeCount',
	];

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
	 * Previous Step instances
	 */
	public function previousSteps()
	{
		return $this->belongsToMany(Step::class, 'step_next_steps', 'next_step_id', 'step_id');
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
	 *
	 * @param  $ids (array) integers
	 *     they are the ids of stepnextsteps already scanned.
	 *     (should avoid loops and duplication)
	 */
	public function getEdges($ids = [])
	{
		$all_edges = [];

		// get all current edges
		$todo_edges = [];
		foreach ($this->stepNextSteps as $stepNextStep) {
			if (in_array($stepNextStep->id, $ids)) {
				continue;// edge already added in
			}
			$ids[] = $stepNextStep->id;
			$todo_edges[] = $stepNextStep;

			$nextStep = $stepNextStep->nextStep;//Step::find($stepNextStep->next_step_id);

			$edge = [
				'id' => $stepNextStep->id,
				'from' => $stepNextStep->step_id,
				'to' => $stepNextStep->next_step_id,
				'message' => $stepNextStep->failure_message,
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
		}

		// go through edges and iterate
		foreach ($todo_edges as $stepNextStep) {
			//$nextStep = Step::find($stepNextStep->next_step_id);
			//$new_edges = $nextStep->getEdges($ids);
			$new_edges = $stepNextStep->nextStep->getEdges($ids);
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

	/**
	 * attribute getter for nextEdgeCount
	 */
	public function getNextEdgeCountAttribute()
	{
		return $this->stepNextSteps()->count();
	}

	/**
	 * attribute getter for previousEdgeCount
	 */
	public function getPreviousEdgeCountAttribute()
	{
		return $this->stepPreviousSteps()->count();
	}
}
