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

	public function getValue()
	{
		return $this->value;
	}

	public function getMetaData()
	{
		return $this->metaData;
	}

	protected function getDefaultExtractor()
	{
		return function ($field) {
			return $field->value;
		};
	}
}
