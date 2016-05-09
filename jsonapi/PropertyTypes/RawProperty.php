<?php

namespace Lar\JsonApi\PropertyTypes;

use Lar\JsonApi\IJsonProperty;


class RawProperty implements IJsonProperty
{
	protected $field;

	public function __construct($field)
	{
		$this->field = $field;
	}

	public function toValue()
	{
		return $this->field->value();
	}
}
