<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Code;
use tt2larp\Models\Character;
use tt2larp\Models\LibraryStation;
use tt2larp\Models\ProblemStation;
use tt2larp\Models\Problem;
use tt2larp\Models\Station;
use tt2larp\Models\Step;
use tt2larp\Models\StepNextStep;

class StationController extends Controller
{
	/**
	 * Main entry point for Stations, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('station.portal');
	}

	/**
	 * returns all stations
	 */
	public function getList()
	{
		$stations = Station::with('station')->orderBy('name')->get();

		return new JsonResponse($stations);
	}

	/**
	 * get active step entourage for station
	 */
	public function getStepEntourage(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'station_id' => 'required|integer',
			'forward' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$basestation = Station::find($request->station_id);
		if (! $basestation->station instanceOf ProblemStation) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested station is not a ProblemStation. Invalid Request.') ], 400);
		}

		$problem = $basestation->station->problem;
		if ($problem === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.There is no active Problem on the Station.') ], 400);
		}

		$step = $basestation->station->step;
		if ($step === null) {
			$step = $basestation->station->problem->firstSteps()->first();
			if ($step === null) {
				return new JsonResponse([ 'success' => false, 'message' => __('i.There is no acive Problem on the Station.') ], 400);
			}
		}

		$steps = null;
		$forward = $request->forward;
		if ($forward > 0) {
			$steps = $step->nextSteps->unique();
		} else if ($forward < 0) {
			$steps = $step->previousSteps->unique();
		} else {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Invalid forward value, must be non-zero.') ], 400);
		}

		$steps = $steps->map(function ($s) {
			return [ 'id' => $s->id, 'text' => $s->name ];
		});

		return new JsonResponse($steps);
	}

	/**
	 * edit all station names at once
	 */
	public function setNames(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'stations' => 'required|array',
			'stations.*.id' => 'required|distinct|integer',
			'stations.*.name' => 'required|distinct|string',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		foreach ($request->stations as $s) {
			$station = Station::find($s['id']);
			if ($station === null) {
				throw new \LogicException("This is impossible. You cheated.");
			}
			$station->name = $s['name'];
			$station->save();
		}

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * set active Problem on ProblemStation
	 */
	public function setActiveProblem(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'station_id' => 'required|integer',
			'problem_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$basestation = Station::find($request->station_id);
		if (! $basestation->station instanceOf ProblemStation) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested station is not a ProblemStation. Invalid Request.') ], 400);
		}

		$problem_id = $request->problem_id;
		if ($problem_id < 0) {
			$basestation->station->problem()->dissociate();
			$basestation->station->step()->dissociate();
			$basestation->station->save();
		} else {
			$problem = Problem::find($problem_id);
			if ($problem === null) {
				return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Problem' ]) ], 422);
			}

			$basestation->station->problem()->associate($problem);
			$basestation->station->step()->dissociate();
			$basestation->station->save();
		}

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * set active Step on ProblemStation
	 */
	public function setActiveStep(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'station_id' => 'required|integer',
			'step_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$basestation = Station::find($request->station_id);
		if (! $basestation->station instanceOf ProblemStation) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested station is not a ProblemStation. Invalid Request.') ], 400);
		}

		$step = Step::find($request->step_id);
		if ($step === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested Step doesn\'t exist.') ], 400);
		}

		$problem = $basestation->station->problem;
		if ($problem === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.There is no active Problem on the Station.') ], 400);
		}

		if ($step->problem()->first()->id === $problem->id) {
			$basestation->station->step()->associate($step);
			$basestation->station->save();
			return new JsonResponse([ 'success' => true ]);
		}

		return new JsonResponse([ 'success' => false, 'message' => __('i.Step doesn\'t belong to active problem.') ], 400);
	}
}
