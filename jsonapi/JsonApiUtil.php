<?php

namespace Lar\JsonApi;

use Iterator;


class JsonApiUtil
{
	public static function pageToJson($page)
	{
		if ($page instanceof Iterator)
		{
			return new JsonListCollection(array_map(['self', 'pageToJson'], array_values(iterator_to_array($page))));
		}

		$content = $page->content();
		$collection = new JsonFieldCollection();

		$collection->addFields([
			'id' => new StaticField($page->id()),
			'slug' => new StaticField($page->slug()),
		]);


		foreach ($content->fields() as $field) {
			$collection->addField($field, new PageField($content->get($field)));
		}

		return $collection;
	}

	public static function filesToJson($page)
	{
		$collection = new JsonFieldCollection();

		foreach ($page->files() as $file) {
			$collection->addFields([
				'url' => new StaticField($file->url()),
				'name' => new StaticField($file->name()),
				'extension' => new StaticField($file->extension()),
				'size' => new StaticField($file->size()),
				'niceSize' => new StaticField($file->niceSize()),
				'mime' => new StaticField($file->mime()),
				'type' => new StaticField($file->type),
			]);
		}

		return $collection;
	}

	public static function pageToNode($page, $fullTree = false)
	{
		if ($page instanceof Iterator)
		{
			return new JsonListCollection(array_map(['self', 'pageToNode'], array_values(iterator_to_array($page))));
		}

		$node = self::pageToJson($page);
		$node->addFeild('files', new StaticField(self::filesToJson($page)));

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
