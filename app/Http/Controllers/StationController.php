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
use tt2larp\Models\Station;
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
		$stations = Station::with('station')->get();

		return new JsonResponse($stations);
	}

	/**
	 * edit all station names at once
	 */
	public function setNames(Request $request)
	{
		$request->validate([
			'stations' => 'required|array',
			'stations.*.id' => 'required|distinct|integer',
			'stations.*.name' => 'required|distinct|string',
		]);

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
	 * try to solve problem
	 *
	 * @param  GET
	 *     - station_id (integer|required)
	 *     - codes (array|required)
	 *     - codes.* (string|required|distinct|min:3)
	 *
	 * @return json
	 *     - success (boolean|required)
	 *     - errors (object|optional)
	 *     - message (string|optional)
	 *     - messages (array|optional)
	 *     - messages.* (string|required)
	 *     - @todo add necessary fields for the frontend
	 */
	public function tryProblem(Request $request)
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

		$basestation->last_ping = now();
		$basestation->save();

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

		// from all codes sent through the api
		$codes = Code::findMany($request->codes);

		// filter out the valid StepNextStep instances and find the Character performing the duty.
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
		$failure_messages = [];
		$failure_messages[] = $step->description;
		foreach ($step->stepNextSteps as $stepNextStep) {
			$failure_messages[] = $stepNextStep->failure_message;

			foreach ($stepNextSteps as $potential) {
				if ($stepNextStep->id === $potential->id) {
					$successfulNextStep = $stepNextStep;
					break;
				}
			}

			if ($character !== null) {
				$valid = Ability::CompareAllInFirst($stepNextStep->abilities, $character->abilities);
				if (count($valid) > 0) {
					$successfulNextStep = $stepNextStep;
					break;
				}
			}
		}
		if ($successfulNextStep === null) {
			return new JsonResponse([ 'success' => true, 'messages' => $failure_messages ]);
		}

		$nextStep = $successfulNextStep->nextStep;

		$station->step()->associate($nextStep);
		$station->save();

		return new JsonResponse([ 'success' => true, 'message' => $nextStep->description ]);
	}
}
