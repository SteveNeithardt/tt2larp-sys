<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use ReflectionClass;
use RuntimeException;

use tt2larp\Models\Ability;
use tt2larp\Models\Article;
use tt2larp\Models\Character;
use tt2larp\Models\Code;
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
		$articles = Article::with('codes')->get();

		foreach ($articles as $article) {
			$code = $article->codes->first();
			$article->code = $code === null ? null : $code->code;
			unset($article->codes);
		}

		return new JsonResponse($articles);
	}

	/**
	 * store a single article (insert/update)
	 */
	public function storeArticle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'code' => 'required|string|min:3|max:8',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$id = $request->id;
		$article = Article::find($id);
		if ($article === null) {
			$article = new Article();
		}

		$name = $request->name;
		if ($name !== $article->name && Article::where('name', '=', $name)->count() > 0) {
			return new JsonResponse([ 'success' => false, 'message' => __( ":instance named ':name' already exists.", [ 'instance' => 'Article', 'name' => $name ] ) ]);
		} else {
			$article->name = $name;
		}

		$article->save();

		try {
			$article->assignCode($request->code);
		} catch (RuntimeException $e) {
			return new JsonResponse([ 'success' => false, 'message' => $e->message ]);
		}

		return new JsonResponse(['success' => true ]);
	}

	/**
	 * Delete a single Article
	 */
	public function deleteArticle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$article = Article::find($request->id);

		$article->delete();

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * All different parts of the article (article_id)
	 */
	public function getPartList($article_id)
	{
		$article = Article::find($article_id);
		if ($article === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Article' ]) ], 400);
		}

		$parts = $article->parts;

		return new JsonResponse($parts);
	}

	/**
	 * store a single part (insert/update)
	 */
	public function storePart(Request $request, $article_id)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'description' => 'required|string',
			'ability_id' => 'nullable|integer',
			'min_value' => 'nullable|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$article = Article::find($article_id);
		if ($article === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Article' ]) ], 400);
		}

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
			if ($ability === null) {
				return new JsonResponse([ 'success' => false, 'message' => __('i.AbilityId can\'t be null when min value is set.') ], 422);
			}

			$min_value = max(0, (int)$min_value);
			$part->ability()->associate($ability);
			$part->min_value = $min_value;
		} else {
			$part->ability_id = null;
			$part->min_value = null;
		}
		$part->save();

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * delete a single Part
	 */
	public function deletePart(Request $request, $article_id)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$article = Article::find($article_id);
		if ($article === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Article' ]) ], 400);
		}

		$part_id = $request->id;
		$part = Part::find($part_id);
		if ($part === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Part' ]) ], 400);
		}

		$part->delete();

		return new JsonResponse([ 'success' => true ]);
	}
}
