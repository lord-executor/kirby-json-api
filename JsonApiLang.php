<?php

namespace Lar\JsonApi;


class JsonApiLang
{
	public static function fromQuery($name = 'lang')
	{
		return function () use ($name) {
			$query = kirby()->request()->query();
			return site()->language($query->get($name));
		};
	}

	public static function fromPathSegment($index = 1)
	{
		return function () use ($index) {
			$index = intval($index);
			$path = kirby()->request()->path();

			if ($index < 0) {
				$index = count($path) + $index;
			}

			return site()->language($path->nth($index));
		};
	}
}
