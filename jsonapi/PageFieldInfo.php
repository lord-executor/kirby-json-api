<?php

namespace Lar\JsonApi;

use a;
use Lar\JsonApi\PropertyTypes\RawProperty;
use Lar\JsonApi\PropertyTypes\StringProperty;
use Lar\JsonApi\PropertyTypes\ReferenceProperty;


class PageFieldInfo
{
	public static $transformations = [];

	private $field;
	private $name;
	private $settings;

	public function __construct($field) {
		$this->field = $field;
		$this->name = $field->key();

		$this->settings = BlueprintHelper::blueprint($field->page())->getFieldSettings($this->name);
	}

	public function getSettings()
	{
		return $this->settings;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getType()
	{
		$type = a::get($this->settings, 'type');

		switch ($type)
		{
			case '':
				return 'unknown';

			case 'checkbox':
			case 'toggle':
				return  'boolean';

			case 'page':
				return 'reference';

			case 'select':
				if (empty(a::get($this->settings, 'options')))
				{
					return 'string';
				}
				else
				{
					return 'reference';
				}

			case 'structure':
				return 'array';

			default:
				return $type;
		}
	}

	public function visit($visitor)
	{
		return $visitor->visitPageField($this->field, $this->getType(), $this->getSettings());
	}
}
