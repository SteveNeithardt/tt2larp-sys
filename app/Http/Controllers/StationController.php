<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Station;

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
		$stations = Station::with('station')->get();

		return new JsonResponse($stations);
	}

	/**
	 * try to solve problem
	 *
	 * @return json
	 *     - success (boolean|required)
	 *     - errors (array|optional)
	 *     - message (string|optional)
	 *     - @todo add necessary fields for the frontend
	 */
	public function tryProblem()
	{
		$validator = Validator::make($request->all(), [
			'station_id' => 'required|integer',
			'codes' => 'required|array',
			'codes.*' => 'required|string|distinct|min:3',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$station_id = $request->station_id;

		$basestation = Station::find($station_id);
		if ($basestation === null) {
			return new JsonResponse([ 'success' => false, 'message' => "Station $station_id doesn't exist." ], 422);
		}

		$station = $basestation->station;
		if (! $station instanceof ProblemStation) {
			return new JsonResponse([ 'success' => false, 'message' => __("Station :name (:id) is not a ProblemStation.", [ 'name' => $basestation->name, 'id' => $station_id ]) ], 400);
		}

		$problem = $station->problem;
		if ($problem === null) {
			return new JsonResponse([ 'success' => true, 'message' => __("There are no problems on this station.") ]);
		}
		$step = $station->step;
		if ($step === null) {
			$step = $problem->firstSteps()->first();
			if ($step === null) abort("This should be impossible.");
		}

		if (count($request->codes) === 0) {
			return new JsonResponse([ 'success' => true, 'message' => $step->description ]);
		}

		$codes = Code::findMany($request->codes);

		$stepNextSteps = [];
		$character = null;
		foreach ($codes as $code) {
			$instance = $code->coded;
			if ($instance instanceof Character) {
				if ($character !== null) {
					return new JsonResponse([ 'success' => false, 'message' => __('More than one character present in input array.') ], 422);
				}
				$character = $instance;
			} else if ($instance instanceof StepNextStep) {
				$stepNextSteps[] = $instance;
			}
		}

		$successfulNextStep = null;
		foreach ($stepNextSteps as $stepNextStep) {
			if ($stepNextStep->step_id === $step->id) {
				$valid = Ability::CompareAllInFirst($stepNextStep->abilities, $character->abilities);
				if (count($valid) > 0) {
					$successfulNextStep = $stepNextStep;
					break;
				}
			}
		}
		if ($successfulNextStep === null) {
			return new JsonResponse([ 'success' => true, 'message' => $successfulNextStep->failure_message ]);
		}

		$nextStep = $successfulNextStep->nextSteps()->first();

		$station->step()->associate($nextStep);

		return new JsonResponse([ 'success' => true, 'message' => $nextStep->description ]);
	}
}
