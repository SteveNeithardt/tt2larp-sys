<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Article;
use tt2larp\Models\Code;
use tt2larp\Models\Character;
use tt2larp\Models\CraftingStation;
use tt2larp\Models\Ingredient;
use tt2larp\Models\LibraryStation;
use tt2larp\Models\ProblemStation;
use tt2larp\Models\Problem;
use tt2larp\Models\Recipe;
use tt2larp\Models\Station;
use tt2larp\Models\Step;
use tt2larp\Models\StepNextStep;

class StationApiController extends Controller
{
	//@TODO
	//
	// Do something about all the messages: define a format that shows bold parts
	// QTextEdit can potentially use html tags to format its text.
	// Investigate

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
	public function index(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'station_id' => 'required|integer',
			'codes' => 'sometimes|array',
			'codes.*' => 'required|string',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$station_id = $request->station_id;

		$basestation = Station::find($station_id);
		if ($basestation === null) {
			return new JsonResponse([
				'success' => false,
				'message' => "Station $station_id doesn't exist.",
			], 422);
		}

		$basestation->last_ping = now();
		$basestation->save();

		$station = $basestation->station;
		if ($station instanceof ProblemStation) {
			return $this->problem($request, $station);
		}
		if ($station instanceof LibraryStation) {
			return $this->article($request, $station);
		}
		if ($station instanceof CraftingStation) {
			return $this->crafting($request, $station);
		}

		return new JsonResponse([
			'success' => false,
			'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Station' ]),
		], 400);
	}

	/**
	 * Manage the request when it is a ProblemStation
	 *
	 * @param  $request (Request)
	 * @param  $station (ProblemStation)
	 *
	 * @return JsonResponse
	 */
	private function problem(Request $request, ProblemStation $station)
	{
		$problem = $station->problem;
		if ($problem === null) {
			return new JsonResponse([ 'success' => true ]);
		}
		$step = $station->step;
		if ($step === null) {
			$step = $problem->firstSteps()->first();
			if ($step === null) {//abort("This should be impossible.");
				return new JsonResponse([ 'success' => true ]);
			}
		}

		if ($request->codes === null || count($request->codes) === 0) {
			return new JsonResponse([
				'success' => true,
				'messages' => [ $step->description ],
				'keep' => false,
			]);
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
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one character present in input array.'),
						'keep' => false,
					]);
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
			return new JsonResponse([
				'success' => true,
				'messages' => $failure_messages,
				'keep' => ($character === null ? true : false),
			]);
		}

		$nextStep = $successfulNextStep->nextStep;

		$station->step()->associate($nextStep);
		$station->save();

		return new JsonResponse([
			'success' => true,
			'messages' => [ $nextStep->description ],
			'keep' => false,
		]);
	}

	/**
	 * Manage the request when it is a LibraryStation
	 *
	 * @param  $request (Request)
	 * @param  $station (LibraryStation)
	 *
	 * @return JsonResponse
	 */
	public function article(Request $request, LibraryStation $station)
	{
		if ($request->codes === null || count($request->codes) === 0) {
			return new JsonResponse([ 'success' => true ]);
		}

		$codes = Code::findMany($request->codes);

		$characters = [];
		$article = null;
		foreach ($codes as $code) {
			$instance = $code->coded;
			if ($instance instanceof Character) {
				$characters[] = $instance;
			} else if ($instance instanceof Article) {
				if ($article !== null) {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one article present in input array.'),
						'keep' => false,
					]);
				}
				$article = $instance;
			}
		}
		if ($article === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.No article present in input array.'),
				'keep' => true,
			]);
		}

		$abilities = Ability::CollapseCharacters($characters);

		$parts = [];
		foreach ($article->parts as $part) {
			$ability_id = $part->ability_id;
			$min_value = $part->min_value;
			if ($ability_id === null || $min_value === null) {
				$parts[] = $part;
			} else {
				if (isset($abilities[$ability_id]) && $abilities[$ability_id] >= $min_value) {
					$parts[] = $part;
				}
			}
		}

		if (count($parts) === 0) {
			return new JsonResponse([
				'success' => true,
				'messages' => [],
				'keep' => true,
			]);
		}

		return new JsonResponse([
			'success' => true,
			'messages' => array_column($parts, 'description'),
			'keep' => true,
		]);
	}

	/**
	 * Manage the request when it is a CraftingStation
	 *
	 * @param  $request (Request)
	 * @param  $station (CraftingStation)
	 *
	 * @return JsonResponse
	 */
	public function crafting(Request $request, CraftingStation $station)
	{
		if ($request->codes === null || count($request->codes) === 0) {
			return new JsonResponse([
				'success' => true,
				'messages' => [],
				'keep' => false,
			]);
		}

		// from all codes sent through the api
		$codes = Code::findMany($request->codes);

		// filter out Recipe instances, Ingredient instances and the Character performing the search.
		$character = null;
		$recipe = null;
		$ingredients = [];
		foreach ($codes as $code) {
			$instance = $code->coded;
			if ($instance instanceof Character) {
				if ($character !== null) {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one character present in input array.'),
						'keep' => false,
					]);
				}
				$character = $instance;
			} else if ($instance instanceof Recipe) {
				if ($recipe !== null) {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one recipe present in input array.'),
						'keep' => false,
					]);
				}
				$recipe = $instance;
			} else if ($instance instanceof Ingredient) {
				$ingredients[] = $instance;
			}
		}

		if ($character === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.No character present in input array.'),
				'keep' => false,
			]);
		}

		if ($recipe === null) {
			//$recipes = $character->availableRecipes();
			return new JsonResponse([
				'success' => true,
				'messages' => [ __('i.Please enter a recipe you know.') ],
				//'messages' => $recipes,
				'keep' => true,
			]);
		}

		$valid = true;
		foreach ($recipe->ingredients as $ingredient) {
			$found = false;
			foreach ($ingredients as $i) {
				if ($i->id === $ingredient->id) {
					$found = true;
					break;
				}
			}
			$valid = $valid && $found;
		}
		if ($valid === false) {
			return new JsonResponse([
				'success' => true,
				'messages' => [ __('i.Not enough ingredients for recipe.') ],
				'keep' => true,
			]);
		}

		return new JsonResponse([
			'success' => true,
			'messages' => [ $recipe->description ],
			'keep' => false,
		]);
	}
}
