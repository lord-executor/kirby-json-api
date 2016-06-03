<?php

namespace Lar\JsonApi;

use Iterator;


class JsonApiUtil
{
	public static function pageToJson($page)
	{
		if (empty($page)) {
			return null;
		}

		if ($page instanceof Iterator)
		{
			return new JsonListCollection(array_map(['self', 'pageToJson'], array_values(iterator_to_array($page))));
		}

		$content = $page->content();
		$collection = new JsonFieldCollection();

		$collection->addFields([
			'id' => new StaticField($page->id()),
			'url' => new StaticField($page->url()),
			'uid' => new StaticField($page->uid()),
		]);


		foreach ($content->fields() as $field) {
			$collection->addField($field, new PageField($content->get($field)));
		}

		return $collection;
	}

	public static function filesToJson($page)
	{
		if (empty($page)) {
			return null;
		}

		$files = [];

		foreach ($page->files() as $file) {
			$collection = new JsonFieldCollection();
			$collection->addFields([
				'url' => new StaticField($file->url()),
				'name' => new StaticField($file->name()),
				'extension' => new StaticField($file->extension()),
				'size' => new StaticField($file->size()),
				'niceSize' => new StaticField($file->niceSize()),
				'mime' => new StaticField($file->mime()),
				'type' => new StaticField($file->type()),
			]);

			$files[] = $collection;
		}

		return new JsonListCollection($files);
	}

	public static function pageToNode($page, $fullTree = false)
	{
		if (empty($page)) {
			return null;
		}

		if ($page instanceof Iterator)
		{
			return new JsonListCollection(array_map(['self', 'pageToNode'], array_values(iterator_to_array($page))));
		}

		$node = self::pageToJson($page);
		$node->addField('files', new StaticField(self::filesToJson($page)));

		if ($fullTree)
		{
			$node->addField('children', new StaticField(self::pageToNode($page->children())));
		}
		else
		{
			$node->addField('children', new StaticField(array_keys(iterator_to_array($page->children()))));
		}

		return $node;
	}
}
