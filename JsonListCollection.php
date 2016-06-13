<?php

namespace Lar\JsonApi;

use Exception;


class JsonListCollection implements IJsonMap
{
	private $list;

	public function __construct($list)
	{
		$this->list = $list;
	}

	public function mapField($key, $extractorFn)
	{
		foreach ($this->list as $value)
		{
			if ($value instanceof IJsonMap) {
				$value->mapField($key, $extractorFn);
			}
		}

		return $this;
	}

	public function selectFields($names)
	{
		foreach ($this->list as $value)
		{
			if ($value instanceof IJsonMap)
			{
				$value->selectFields($names);
			}
		}

		return $this;
	}

	public function toArray()
	{
		$result = [];

		foreach ($this->list as $value)
		{
			$result[] = ($value instanceof IJsonObject ? $value->toArray() : $value);
		}

		return $result;
	}
}
