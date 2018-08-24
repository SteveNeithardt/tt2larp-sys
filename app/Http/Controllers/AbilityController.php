<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;

class AbilityController extends Controller
{
	/**
	 * lists all abilities in a searchable interface
	 */
	public function index()
	{
		$abilities = Ability::all();

		return view('ability.list')->with(compact('abilities'));
	}

	/**
	 * view a single ability
	 */
	public function view(Request $request, $id)
	{
		$ability = ability::find($id);

		if ($ability === null) abort(422, "Ability $id doesn't exist.");

		return view('ability.view')->with(compact('ability'));
	}

	/**
	 * edit a single ability
	 */
	public function edit(Request $request, $id = null)
	{
		$ability = Ability::with('abilities')->find($id);

		return view('ability.edit')->with(compact('ability'));
	}

	/**
	 * store a single ability (insert/update)
	 */
	public function store(Request $request)
	{
		abort(500, "not implmented");

		$id = $request->id;

		$ability = Ability::find($id);

		if ($ability === null) $ability = new Ability();

		$ability->save();

		return new JsonResponse(['success' => true]);
	}
}
