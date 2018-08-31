<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $table = 'articles';

	/**
	 * Parts this Article has
	 */
	public function parts()
	{
		return $this->hasMany(Part::class, 'article_id', 'id');
	}
}
