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
use tt2larp\Models\Part;
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
		$codes = Code::whereIn('code', $request->codes)->get();

		// filter out the valid StepNextStep instances and find the Character performing the duty.
		$stepNextSteps = [];
		//$character = null;
		$characters = [];
		$stack = [];
		foreach ($codes as $code) {
			$instance = $code->coded;
			if ($instance instanceof Character) {
				$characters[] = $instance;
				//if ($character !== null) {
					//return new JsonResponse([
						//'success' => false,
						//'message' => __('i.More than one character present in input array.'),
						//'keep' => false,
					//]);
				//}
				//$character = $instance;
				$stack[] = $instance->name;
			} else if ($instance instanceof StepNextStep) {
				$stepNextSteps[] = $instance;
				$stack[] = $code->code;
			}
		}

		$abilities = Ability::CollapseCharacters($characters);
		$built = [];
		foreach ($abilities as $key => $value) {
			$built[] = (object)[ 'id' => $key, 'pivot' => (object)[ 'value' => $value ] ];
		}

		$successfulNextStep = null;
		$failure_messages = [];
		$failure_messages[] = $step->description;
		foreach ($step->stepNextSteps as $stepNextStep) {
			if ($stepNextStep->failure_messages !== null && count($stepNextStep->failure_messages) > 0) {
				$failure_messages[] = $stepNextStep->failure_message;
			}

			foreach ($stepNextSteps as $potential) {
				if ($stepNextStep->id === $potential->id) {
					$successfulNextStep = $stepNextStep;
					break;
				}
			}

			if (count($characters) > 0) {
			//if ($character !== null) {
				$valid = Ability::CompareAllInFirst($stepNextStep->abilities, $built);
				//$valid = Ability::CompareAllInFirst($stepNextStep->abilities, $character->abilities);
				if (count($valid) == count($stepNextStep->abilities)) {
				//if (count($valid) > 0) {
					$successfulNextStep = $stepNextStep;
					break;
				}
			}
		}
		if ($successfulNextStep === null) {
			return new JsonResponse([
				'success' => true,
				'messages' => $failure_messages,
				//'str_stack' => ($character === null ? $stack : []),
				//'keep' => ($character === null ? true : false),
				'str_stack' => $stack,
				'keep' => true,
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

		$codes = Code::whereIn('code', $request->codes)->get();

		$characters = [];
		$article = null;
		$codeparts = [];
		$stack = [];
		foreach ($codes as $code) {
			$instance = $code->coded;
			if ($instance instanceof Character) {
				$characters[] = $instance;
				$stack[] = $instance->name;
			} else if ($instance instanceof Article) {
				if ($article !== null) {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one article present in input array.'),
						'keep' => false,
					]);
				}
				if ($instance->library_station_id === $station->id) {
					$article = $instance;
					$stack[] = $code->code;
				} else {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.This article is not in this library'),
						'keep' => false,
					]);
				}
			} else if ($instance instanceof Part) {
				$codeparts[] = $part;
			}
		}
		if ($article === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.No article present in input array.'),
				'str_stack' => $stack,
				'keep' => true,
			]);
		}

		$abilities = Ability::CollapseCharacters($characters);

		$built = [];
		foreach ($abilities as $key => $value) {
			$built[] = (object)[ 'id' => $key, 'pivot' => (object)[ 'value' => $value ] ];
		}

		$validparts = [];
		foreach ($article->parts as $part) {
			if ($part->abilities->count() == 0 && $part->codes->count() == 0) {
				$validparts[] = $part;
				continue;
			}
			if ($part->abilities->count() > 0 && $part->codes->count() == 0) {
				$valid = Ability::CompareAllInFirst($part->abilities, $built);
				if (count($valid) === count($part->abilities)) {
					$validparts[] = $part;
				}
			}
			if ($part->abilities->count() == 0 && $part->codes->count() > 0) {
				foreach ($codeparts as $cp) {
					if ($cp->id === $part->id) {
						
					}
				}
			}
			//$ability_id = $part->ability_id;
			//$min_value = $part->min_value;
			//if ($ability_id === null || $min_value === null) {
				//$parts[] = $part;
			//} else {
				//if (isset($abilities[$ability_id]) && $abilities[$ability_id] >= $min_value) {
					//$parts[] = $part;
				//}
			//}
		}

		if (count($validparts) === 0) {
			return new JsonResponse([
				'success' => true,
				'messages' => [],
				'str_stack' => $stack,
				'keep' => true,
			]);
		}

		return new JsonResponse([
			'success' => true,
			'messages' => array_column($validparts, 'description'),
			'str_stack' => $stack,
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
			return new JsonResponse([ 'success' => true ]);
		}

		// from all codes sent through the api
		$codes = Code::whereIn('code', $request->codes)->get();

		// filter out Recipe instances, Ingredient instances and the Character performing the search.
		$character = null;
		$recipe = null;
		$ingredients = [];
		$stack = [];
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
				$stack[] = $character->name;
			} else if ($instance instanceof Recipe) {
				if ($recipe !== null) {
					return new JsonResponse([
						'success' => false,
						'message' => __('i.More than one recipe present in input array.'),
						'keep' => false,
					]);
				}
				$recipe = $instance;
				$stack[] = $code->code;
			} else if ($instance instanceof Ingredient) {
				$ingredients[] = $instance;
				$stack[] = $code->code;
			}
		}

		if ($character === null) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.No character present in input array.'),
				'keep' => false,
			]);
		}

		$recipes = $character->recipes();

		if ($recipe === null) {
			$messages = $recipes->map(function ($r) {
				return $r->name . ' [' . $r->codes()->first()->code . ']';
			});
			return new JsonResponse([
				'success' => true,
				'messages' => $messages,
				'str_stack' => $stack,
				'keep' => true,
			]);
		}

		$allowed = false;
		foreach ($recipes as $r) {
			if ($r->id === $recipe->id) {
				$allowed = true;
				break;
			}
		}
		if ($allowed === false) {
			return new JsonResponse([
				'success' => false,
				'message' => __('i.The Character is not allowed to craft Recipe.'),
				'keep' => false,
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

		//$craft_message = 

		if ($valid === false) {
			return new JsonResponse([
				'success' => true,
				'messages' => [ __('i.Not enough ingredients for recipe.') ],
				'str_stack' => $stack,
				'keep' => true,
			]);
		}

		return new JsonResponse([
			'success' => true,
			'messages' => [ $recipe->description ],
			'str_stack' => $stack,
			'keep' => false,
		]);
	}
}
