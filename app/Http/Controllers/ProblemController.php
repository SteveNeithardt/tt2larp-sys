<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Problem;
use tt2larp\Models\Step;
use tt2larp\Models\StepNextStep;

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
		$request->validate([
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
		]);

		$name = $request->name;
		if (Problem::where('name', '=', $name)->count() > 0) {
			return new JsonResponse([ 'success' => false, 'message' => __( "Problem named ':name' already exists.", [ 'name' => $name ] ) ]);
		}

		$id = $request->id;
		$problem = Problem::find($id);
		if ($problem === null) {
			$problem = new Problem();
			$problem->name = $name;
			$problem->save();
			$step = new Step();
			$step->name = 'UNDEFINED';
			$step->description = 'REPLACE ME';
			$step->save();
			$problem->steps()->sync([ $step->id => [ 'first_step' => 1 ] ]);
		} else {
			$problem->name = $name;
			$problem->save();
		}

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * get all steps for problem $id
	 *
	 * array( 'steps' => $steps, 'tree' => $tree )
	 * where $steps are all related steps to the program
	 * and $tree is the relations between all steps
	 */
	public function getStepList($problem_id)
	{
		$problem = Problem::find($problem_id);

		if ($problem === null) abort(422, "Problem $problem_id doesn't exist.");

		$steps = $problem->getSteps();

		$first = $problem->firstSteps->first();
		if ($first !== null) $edges = $first->getEdges();

		return new JsonResponse(compact('steps', 'edges'));
	}

	/**
	 * store a single step (insert/update)
	 */
	public function storeNode(Request $request, $problem_id)
	{
		$problem = Problem::find($problem_id);

		if ($problem === null) abort(422, "Problem $problem_id doesn't exist.");

		$stepId = $request->step_id;
		$step = Step::find($stepId);
		if ($step === null) {
			$step = new Step();
		}
		$step->name = $request->name;
		$step->description = $request->description;
		$step->save();

		$problem->steps()->attach($step);

		return new JsonResponse(['success' => true]);
	}

	/**
	 * deletes a single node
	 */
	public function deleteNode(Request $request, $problem_id)
	{
		$problem = Problem::find($problem_id);

		if ($problem === null) abort(422, "Problem $problem_id doesn't exist.");

		$request->validate([
			'step_id' => 'required|integer',
		]);

		$step_id = $request->step_id;
		$step = Step::find($step_id);
		if ($step === null) {
			return new JsonResponse([ 'success' => false ]);
		}

		$step->problem()->detach();
		$step->delete();

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * store a single edge (insert/update)
	 */
	public function storeEdge(Request $request, $problem_id)
	{
		$problem = Problem::find($problem_id);

		if ($problem === null) abort(422, "Problem $problem_id doesn't exist.");

		$request->validate([
			'id' => 'nullable|integer',
			'step_id' => 'required|integer',
			'next_step_id' => 'required|integer',
			'abilities' => 'required|array',
			'abilities.*.id' => 'required|integer',
			'abilities.*.value' => 'required|integer',
			'codes' => 'required|array',
			'codes.*' => 'requred|string',
		]);

		$id = $request->id;

		$stepId = $request->step_id;
		$step = Step::find($stepId);
		if ($step === null) abort(422, "Step $stepId doesn't exist.");

		$nextStepId = $request->next_step_id;
		$nextStep = Step::find($nextStepId);
		if ($nextStep === null) abort(422, "Step $nextStepId doesn't exist.");

		$stepNextStep = StepNextStep::find($id);

		$pivot = [];
		foreach ($request->abilities as $ability) {
			if (Ability::find($ability->id) === null) {
				continue;
			}
			$pivot[ $ability->id ] = [ 'value' => $ability->value ];
		}
		$stepNextStep->abilities()->sync($pivot);

		$found = [];
		foreach ($request->codes as $codestr) {
			$code = Code::find($codestr);
			if ($code !== null) {
				$instance = $code->instance;
				if ($instance instanceOf StepNextStep && $instance->id == $stepNextStep->id) {
					$found[] = $code;
				}
			} else {
				$code = new Code();
				$code->code = $codestr;
				$found[] = $code;
			}
		}
		$stepNextStep->codes()->delete();
		$stepNextStep->codes()->saveMany($found);

		//$type = $request->type;
		//switch ($type) {
			//case 'ability':
				//$abilityId = $request->ability_id;
				//if ($abilityId === null) abort(422, "AbilityId can't be null when type is 'ability'.");
				//$ability = Ability::find($abilityId);
				//if ($ability === null) abort(422, "Ability $abilityId doesn't exist.");
				//$min_value = min(3, max(0, (int)$request->min_value));
//
				//$delete_condition = function() use ($min_value) {
					//return $min_value === 0;
				//};
				//$assign = function($stepNextStep) use ($abilityId, $min_value) {
					//$stepNextStep->ability_id = $abilityId;
					//$stepNextStep->min_value = $min_value;
				//};
//
				//break;
			//case 'code':
				//$code = $request->code;
				//if ($code === null) abort(422, "Code can't be null when type is 'code'.");
//
				//$delete_condition = function() use ($code) {
					//return strlen($code) < 3;
				//};
				//$assign = function($stepNextStep) use ($code) {
					//$stepNextStep->code = $code;
				//};
//
				//break;
			//default:
				//abort(422, "Unknown $type for step_next_steps.");
		//}
//
		//if ($delete_condition()) {
			//if ($stepNextStep !== null) { 
				//$stepNextStep->delete();
			//}
		//} else {
			//if ($stepNextStep === null) {
				//$stepNextStep = new StepNextStep();
				//$stepNextStep->step_id = $stepId;
				//$stepNextStep->next_step_id = $nextStepId;
			//}
			//$stepNextStep->type = $type;
//
			//$assign($stepNextStep);
//
			//$stepNextStep->save();
		//}

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * deletes a single edge
	 */
	public function deleteEdge(Request $request, $problem_id)
	{
		$problem = Problem::find($problem_id);

		if ($problem === null) abort(422, "Problem $problem_id doesn't exist.");

		$request->validate([
			'edge_id' => 'required|integer',
		]);

		$edge_id = $request->edge_id;
		$edge = StepNextStep::find($edge_id);
		if ($edge === null) {
			return new JsonResponse([ 'success' => false ]);
		}

		$edge->delete();

		return new JsonResponse([ 'success' => true ]);
	}
}
