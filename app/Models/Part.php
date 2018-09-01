<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
	protected $table = 'parts';

	/**
	 * Article this Part belongs to
	 */
	public function article()
	{
		return $this->belongsTo(Article::class, 'article_id', 'id');
	}
}