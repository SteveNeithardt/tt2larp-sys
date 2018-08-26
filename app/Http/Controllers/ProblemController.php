<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Problem;
use tt2larp\Models\Step;

class ProblemController extends Controller
{
	/**
	 * Main entry point for Problems, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('problem.portal');
	}

	/**
	 * returns all problems
	 */
	public function getList()
	{
		$problems = Problem::select('id','name')->with(['steps' => function($q) {
			$q->select('id','name','description');
		}])->get();

		return new JsonResponse($problems);
	}

	/**
	 * store a single problem (insert/update)
	 */
	public function store(Request $request)
	{

	}

	/**
	 * get all steps for problem $id
	 *
	 * array( 'steps' => $steps, 'tree' => $tree )
	 * where $steps are all related steps to the program
	 * and $tree is the relations between all steps
	 */
	public function getStepList($id)
	{
		$problem = Problem::find($id);

		if ($problem === null) abort(422, "Problem $id doesn't exist.");

		$steps = $problem->getSteps();

		$first = $problem->firstSteps->first();
		$tree = [];
		if ($first !== null) $tree = $first->getTree();

		return new JsonResponse(compact('steps', 'tree'));
	}

	/**
	 * store a single step (insert/update)
	 */
	public function storeNode(Request $request, $id)
	{
		$problem = Problem::find($id);

		if ($problem === null) abort(422, "Problem $id doesn't exist.");

		$stepId = $request->step_id;
		$step = Step::find($stepId);
		if ($step === null) {
			$step = new Step();
			$step->id = $stepId;
		}
		$step->name = $request->name;
		$step->description = $request->description;
		$step->save();

		$problem->steps()->attach($step);
	}

	/**
	 * store a single edge (insert/update)
	 */
	public function storeEdge(Request $request, $id)
	{
		$problem = Problem::find($id);

		if ($problem === null) abort(422, "Problem $id doesn't exist.");

		$request->validate([
			'step_id' => 'required|integer',
			'next_step_id' => 'required|integer',
			'ability_id' => 'required|integer',
			'min_value' => 'required|integer',
		]);

		$stepId = $request->step_id;
		$step = Step::find($step_id);
		if ($step === null) abort(422, "Step $stepId doesn't exist.");

		$nextStepId = $request->next_step_id;
		$nextStep = Step::find($nextStepId);
		if ($nextStep === null) abort(422, "Step $nextStepId doesn't exist.");

		$abilityId = $request->ability_id;
		$ability = Ability::find($abilityId);
		if ($ability === null) abort(422, "Ability $abilityId doesn't exist.");

		$min_value = min(0, max(3, $request->min_value));

		$stepNextStep = StepNextStep::where('step_id', '=', $stepId)->where('next_step_id', '=', $nextStepId)->first();
		if ($min_value === 0) {
			if ($stepNextStep !== null) { 
				$stepNextStep->delete();
			}
		} else {
			if ($stepNextStep === null) {
				$stepNextStep = new StepNextStep();
				$stepNextStep->step_id = $stepId;
				$stepNextStep->next_step_id = $nextStepId;
			}
			$stepNextStep->ability_id = $abilityId;
			$stepNextStep->min_value = $min_value;

			$stepNextStep->save();
		}
	}
}
