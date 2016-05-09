<?php

namespace Lar\JsonApi;


class StaticField extends JsonField
{
	private $value;
	private $metaData;

	public function __construct($value, $metaData = null)
	{
		$this->value = $value;
		$this->metaData = $metaData;
	}

	protected function getDefaultExtractor()
	{
		return function ($field) {
			return $field->value;
		};
	}
}
