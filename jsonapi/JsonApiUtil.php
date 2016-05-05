<?php

namespace Lar\JsonApi;


class JsonApiUtil
{
	public static function pageToJson($page)
	{
		$content = $page->content();

		$data = [
			'id' => $page->id(),
			'slug' => $page->slug(),
		];

		foreach ($content->fields() as $field) {
			$data[$field] = $content->get($field)->value();
		}

		return $data;
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
