<?php

namespace Lar\JsonApi;

use \C;


class PageField extends JsonField
{
	private $field;

	public function __construct($field)
	{
		$this->field = $field;
	}

	protected function callExtractor($fn)
	{
		return $fn($this->field);
	}

	protected function getDefaultExtractor()
	{
		return function ($field) {
			$val = $field->value();
			if (C::get('jsonapi.auto-structured', true) && preg_match('/^\s*-\s*[\n\r]\s+/', $val)) {
				return JsonApiUtil::structureToJson($field->toStructure());
			} else {
				return $val;
			}
		};
	}
}
