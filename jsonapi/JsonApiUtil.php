<?php

namespace Lar\JsonApi;

use Lar\JsonApi\PropertyTypes\StringProperty;

class JsonApiUtil
{
	public static function pageToCollection($page)
	{
		$content = $page->content();

		$props = [
			'id' => $page->id(),
			'slug' => $page->slug(),
		];

		$fields = [];

		foreach ($content->fields() as $name) {
			$fields[] = new PageFieldInfo($content->get($name));
		}

		return new PageFieldCollection($fields);
	}

	public static function pageToJson($page, $mappingOrVisitor = null)
	{
		$data = static::pageToCollection($page);

		if ($mappingOrVisitor === null)
		{
			$visitor = new DefaultFieldVisitor();
		}

		return $data->toArray($visitor);
	}

	public static function filesToJson($page)
	{
		$data = [];

		foreach ($page->files() as $file) {
			$data[] = [
				'url' => $file->url(),
				'name' => $file->name(),
				'extension' => $file->extension(),
				'size' => $file->size(),
				'niceSize' => $file->niceSize(),
				'mime' => $file->mime(),
				'type' => $file->type(),
			];
		}

		return $data;
	}

	public static function pageToNode($page, $fullTree = false)
	{
		$node = self::pageToJson($page);
		$node['files'] = self::filesToJson($page);

		if ($fullTree)
		{
			$node['children'] = array_map(['self', 'pageToNode'], array_values(iterator_to_array($page->children())));
		}
		else
		{
			$node['children'] = array_keys(iterator_to_array($page->children()));
		}

		return $node;
	}
}
