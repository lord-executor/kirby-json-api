<?php

namespace Lar\JsonApi\PropertyTypes;

use Lar\JsonApi\JsonApiUtil;

class ReferenceProperty extends RawProperty
{
	public function toValue()
	{
		return JsonApiUtil::pageToCollection($this->field->toPage());
	}
}
