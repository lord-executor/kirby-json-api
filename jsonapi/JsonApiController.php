<?php

namespace Lar\JsonApi;

use response as KirbyResponse;


class JsonApiController
{
	public function getPage($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::pageToJson($page);
	}

	public function getChildIds($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return array_keys(iterator_to_array($page->children()));
	}

	public function getChildren($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::pageToJson($page->children());
	}

	public function getFilteredChildren($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::pageToJson($page->children());
	}

	public function getFiles($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::filesToJson($page);
	}

	public function getNode($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::pageToNode($page, false);
	}

	public function getTree($id)
	{
		$page = page($id);
		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['id' => $id]);

		return JsonApiUtil::pageToNode($page, true);
	}
}
