<?php

namespace Lar\JsonApi;


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
			return $field->value();
		};
	}
}
