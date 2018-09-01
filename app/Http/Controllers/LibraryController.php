<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Article;
use tt2larp\Models\Part;

class LibraryController extends Controller
{
	/**
	 * Main entry point for Problems, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('library.portal');
	}

	/**
	 * returns all articles
	 */
	public function getList()
	{
		$articles = Article::all();

		return new JsonResponse($articles);
	}

	/**
	 * store a single article (insert/update)
	 */
	public function store(Request $request)
	{
		$request->validate([
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'code' => 'required|string|min:3',
		]);

		$name = $request->name;
		$code = $request->code;
		if (Article::where('code', '=', $code)->count() > 0) {
			return new JsonResponse([ 'success' => false, 'message' => __( "Article with code ':code' already exists.", ['code' => $code ] ) ]);
		}
		if (Article::where('name', '=', $name)->count() > 0) {
			return new JsonResponse([ 'success' => false, 'message' => __( "Article named ':name' already exists.", [ 'name' => $name ] ) ]);
		}

		$id = $request->id;
		$article = Article::find($id);
		if ($article === null) {
			$article = new Article();
			$article->name = $name;
			$article->code = $code;
			$article->save();
		} else {
			$article->name = $name;
			$article->code = $code;
			$article->save();
		}

		return new JsonResponse(['success' => true ]);
	}

	/**
	 * All different parts of the article (article_id)
	 */
	public function getPartList($article_id)
	{
		$article = Article::find($article_id);
		if ($article === null) abort(400, "Article $article_id doesn't exist.");

		$parts = $article->parts;

		return new JsonResponse($parts);
	}

	/**
	 * store a signel part (insert/update)
	 */
	public function storePart(Request $request, $article_id)
	{
		$request->validate([
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'description' => 'required|string',
			'ability_id' => 'nullable|integer',
			'min_value' => 'nullable|integer',
		]);

		$article = Article::find($article_id);
		if ($article === null) abort(400, "Article $article_id doesn't exist.");

		$part_id = $request->id;
		$part = Part::find($part_id);
		if ($part === null) {
			$part = new Part();
		}
		$part->article_id = $article->id;
		$part->name = $request->name;
		$part->description = $request->description;

		$ability_id = $request->ability_id;
		$min_value = $request->min_value;

		if ($ability_id !== null && $min_value !== null) {
			$ability = Ability::find($ability_id);
			if ($ability === null) abort(422, "AbilityId can't be null when min value is set.");

			$min_value = min(3, max(0, (int)$min_value));
			$part->ability_id = $ability->id;
			$part->min_value = $min_value;
		} else {
			$part->ability_id = null;
			$part->min_value = null;
		}
		$part->save();

		return new JsonResponse([ 'success' => true ]);
	}
}
