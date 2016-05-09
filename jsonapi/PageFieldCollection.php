<?php

namespace Lar\JsonApi;

class PageFieldCollection
{
	private $fields = [];

	public function __construct($fields)
	{
		$this->fields = $fields;
	}

	public function select($fieldNames)
	{
		$fields = [];
		foreach ($this->fields as $field)
		{
			if (array_search($field->getName(), $fieldNames) !== false)
			{
				$fields[] = $field;
			}
		}

		return new PageFieldCollection($fields);
	}

	public function remove($fieldNames)
	{
		$fields = [];
		foreach ($this->fields as $field)
		{
			if (array_search($field->getName, $fieldNames) === false)
			{
				$fields[] = $field;
			}
		}

		return new PageFieldCollection($fields);
	}

	public function add($fields)
	{
		$combined = array_merge($this->fields, $fields);
		return new PageFieldCollection($combined);
	}

	public function toArray($visitor)
	{
		$json = [];
		foreach ($this->fields as $field)
		{
			$json[$field->getName()] = $field->visit($visitor);
		}

		return $json;
	}

	public function toJson($visitor)
	{
		return json_encode($this->toArray($visitor));
	}
}
