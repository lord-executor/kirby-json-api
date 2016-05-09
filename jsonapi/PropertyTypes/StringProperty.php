<?php

namespace Lar\JsonApi\PropertyTypes;

class StringProperty extends RawProperty
{
	public function toValue()
	{
		return (string)$this->field->value();
	}
}
