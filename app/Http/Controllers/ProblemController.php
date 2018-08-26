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
			$q->select('id','name');
		}])->get();

		return new JsonResponse($problems);
	}

	/**
	 * store a single character (insert/update)
	 */
	public function store(Request $request)
	{
	}

	/**
	 * get all steps for problem $id
	 */
	public function getStepList($id)
	{
		$problem = Problem::find($id);

		if ($problem === null) abort(422, "Problem $id doesn't exist.");

		$steps = $problem->steps()->select('id', 'name')->get();

		foreach ($steps as $step) {
			$step->first_step = $step->pivot->first_step;
			unset($step->pivot);
		}

		return new JsonResponse($steps);
	}

	/**
	 * store a signel step (insert/update)
	 */
	public function storeStep(Request $request, $id)
	{
		$problem = Problem::find($id);

		if ($problem === null) abort(422, "Problem $id doesn't exist.");
	}
}
