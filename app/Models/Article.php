<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Article extends Model
{
	use HasCodeTrait;

	protected $table = 'articles';

	/**
	 * upon boot, declare what happens on delete
	 */
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($article) {
			foreach ($article->parts as $part) {
				$part->delete();
			}
			$article->codes()->delete();
		});
	}

	/**
	 * Parts this Article has
	 */
	public function parts()
	{
		return $this->hasMany(Part::class, 'article_id', 'id');
	}

	/**
	 * Library Station this is assigned to
	 */
	public function libraryStation()
	{
		return $this->belongsTo(LibraryStation::class, 'library_station_id', 'id')->with('stations');
	}
}
