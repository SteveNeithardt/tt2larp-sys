<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Code;
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
	public function storeProblem(Request $request)
	{
		$request->validate([
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
		]);

		$name = $request->name;
		if (Problem::where('name', '=', $name)->count() > 0) {
			return new JsonResponse([ 'success' => false, 'message' => __( ":instance named ':name' already exists.", [ 'instance' => 'Problem', 'name' => $name ] ) ]);
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
	 * delete a single Problem
	 */
	public function deleteProblem(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$problem = Problem::find($request->id);

		if ($problem->problemStation()->exists()) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Problem is active on a ProblemStation.') ], 422);
		}

		$problem->delete();

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

		$validation = [
			'id' => 'nullable|integer',
			'step_id' => 'required|integer',
			'next_step_id' => 'required|integer',
			'abilities' => 'present|array',
			'abilities.*.id' => 'required|integer',
			'abilities.*.value' => 'required|integer',
			'codes' => 'present|array',
			'codes.*.code' => 'required|string|min:3|max:8',
			'message' => 'nullable|string',
		];
		$request->validate($validation);

		$id = $request->id;

		$stepId = $request->step_id;
		$step = Step::find($stepId);
		if ($step === null) abort(422, "Step $stepId doesn't exist.");

		$nextStepId = $request->next_step_id;
		$nextStep = Step::find($nextStepId);
		if ($nextStep === null) abort(422, "Step $nextStepId doesn't exist.");

		$stepNextStep = StepNextStep::find($id);
		if ($stepNextStep === null) {
			$stepNextStep = new StepNextStep();
			$stepNextStep->step_id = $request->step_id;
			$stepNextStep->next_step_id = $request->next_step_id;
		}
		$stepNextStep->failure_message = $request->message;
		$stepNextStep->save();

		$pivot = [];
		foreach ($request->abilities as $ability) {
			if (Ability::find($ability['id']) === null) {
				continue;
			}
			$pivot[ $ability['id'] ] = [ 'value' => $ability['value'] ];
		}
		$stepNextStep->abilities()->sync($pivot);

		// as we cannot use sync() with morphMany relations,
		// we have to go the hard way along...
		$codes = $stepNextStep->codes;

		// prepare all codes that were in request, labeled as
		$found = [];// when they are already in StepNextStep and as
		$new = [];// when they need to be added later on
		// note that codes found but associated to another instance are ignored.
		foreach ($request->codes as $c) {
			$codestr = $c['code'];
			$code = Code::find($codestr);
			if ($code !== null) {
				$instance = $code->coded;
				if ($instance instanceOf StepNextStep && $instance->id == $stepNextStep->id) {
					$found[] = $code;
				}
			} else {
				$code = new Code();
				$code->code = $codestr;
				$new[] = $code;
			}
		}

		// look through existing codes, comparing to found codes.
		// those that aren't needed anymore are deleted on the spot.
		foreach ($codes as $code) {
			$delete = true;
			foreach ($found as $c) {
				if ($code->code === $c->code) {
					$delete = false;
					break;
				}
			}
			if ($delete === true) {
				$code->delete();
			}
		}

		// finally save all new codes.
		$stepNextStep->codes()->saveMany($new);

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
