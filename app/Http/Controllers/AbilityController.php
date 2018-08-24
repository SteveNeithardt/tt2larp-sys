<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;

class AbilityController extends Controller
{
	/**
	 * Main entry point for Abilities, everything happens in Vuejs
	 */
	public function portal()
	{
		$abilities = Ability::select('id', 'name')->orderBy('name')->get();

		return view('ability.portal')->with(compact('abilities'));
	}

	/**
	 * returns all abilities
	 */
	public function getList()
	{
		$abilities = Ability::select('id', 'name')->orderBy('name')->get();

		return new JsonResponse($abilities);
	}

	/**
	 * store a single ability (insert/update)
	 */
	public function store(Request $request)
	{
		$id = $request->id;

		$ability = Ability::find($id);

		if ($ability === null) $ability = new Ability();

		$ability->name = $request->name;

		$ability->save();

		$abilities = Ability::select('id', 'name')->orderBy('name')->get();

		return new JsonResponse($abilities);
	}
}
