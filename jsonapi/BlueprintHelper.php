<?php

namespace Lar\JsonApi;

use Data;
use F;


class BlueprintHelper
{
	private static $cache = [];

	/**
	 * See https://forum.getkirby.com/t/get-the-blueprint-panel-field-type-in-the-template/859/3
	 */
	public static function blueprint($page)
	{
		$blueprintName = $page->template();
		if (!isset(static::$cache[$blueprintName]))
		{
			$file = f::resolve(kirby()->roots->blueprints() . DS . $blueprintName, array('yml', 'php', 'yaml'));
			$yaml = data::read($file, 'yaml');
			static::$cache[$blueprintName] = new BlueprintHelper($yaml);
		}

		return static::$cache[$blueprintName];
	}

	private $fields = [];

	private function __construct($yaml)
	{
		if (isset($yaml['fields']))
		{
			foreach ($yaml['fields'] as $name => $settings)
			{
				$this->fields[strtolower($name)] = $settings;
			}
		}
	}

	public function getFieldSettings($fieldName)
	{
		return isset($this->fields[$fieldName]) ? $this->fields[$fieldName] : [];
	}
}
