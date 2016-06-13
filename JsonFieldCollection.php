<?php

namespace Lar\JsonApi;

use Exception;


class JsonFieldCollection implements IJsonMap
{
	private $map = [];

	public function __construct()
	{
	}

	public function addField($key, $field)
	{
		if (!($field instanceof JsonField))
		{
			throw new Exception('Field must be an implementation of JsonField');
		}

		$this->map[$key] = $field;

		return $this;
	}

	public function addFields($fields)
	{
		foreach ($fields as $key => $field)
		{
			$this->addField($key, $field);
		}

		return $this;
	}

	public function mapField($key, $extractorFn)
	{
		if (isset($this->map[$key]))
		{
			$this->map[$key]->setExtractor($extractorFn);
		}

		return $this;
	}

	public function selectFields($names)
	{
		foreach ($this->map as $key => $field)
		{
			if (array_search($key, $names) === false)
			{
				unset($this->map[$key]);
			}
		}

		return $this;
	}

	public function toArray()
	{
		$result = [];

		foreach ($this->map as $key => $field)
		{
			$result[$key] = $field->extract();
			if ($result[$key] instanceof IJsonObject)
			{
				$result[$key] = $result[$key]->toArray();
			}
		}

		return $result;
	}
}
