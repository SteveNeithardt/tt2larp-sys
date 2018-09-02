<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Article extends Model
{
	use HasCodeTrait;

	protected $table = 'articles';

	/**
	 * Parts this Article has
	 */
	public function parts()
	{
		return $this->hasMany(Part::class, 'article_id', 'id');
	}
}
