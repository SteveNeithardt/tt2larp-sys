<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Character;

class CharacterController extends Controller
{
	/**
	 * Main entry point for Abilities, everything happens in Vuejs
	 */
	public function index()
	{
		return view('character.portal');
	}

	/**
	 * returns all characters
	 */
	public function getList()
	{
		$characters = Character::select('id', 'name', 'player', 'description')->with(['abilities'=>function($q) {
			$q->select('id', 'name');
		}])->get();

		foreach($characters as $character) {
			foreach ($character->abilities as $ability) {
				$ability->value = $ability->pivot->value;
				unset($ability->pivot);
			}
		}

		return new JsonResponse($characters);
	}

	/**
	 * store a single character (insert/update)
	 */
	public function store(Request $request)
	{
		$id = $request->id;

		$character = Character::find($id);

		if ($character === null) $character = new Character();

		$character->name = $request->name;
		$character->description = $request->description ?? "";
		$character->player = $request->player;
		$character->save();

		$abilities = Ability::pluck('name', 'id');

		$relations_array = [];
		foreach ($request->abilities as $ab) {
			$id = $ab["id"];
			if (isset($abilities[$id]) && isset($ab["value"])) {
				$relations_array[$id] = ["value" => $ab["value"]];
			}
		}

		$character->abilities()->sync($relations_array);

		$characters = Character::with('abilities')->get();

		return new JsonResponse($characters);
	}
}
