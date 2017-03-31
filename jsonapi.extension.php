<?php

require_once(__DIR__ . '/JsonApiController.php');

if (c::get('jsonapi.built-in.enabled', false))
{
	$auth = c::get('jsonapi.built-in.auth', null);

	if ($auth === false)
	{
		$auth = null;
	}
	else if ($auth)
	{
		// invoke the auth configuration provider
		$auth = $auth();
	}
	else
	{
		$auth = Lar\JsonApi\JsonApiAuth::isAdmin();
	}

	$lang = c::get('jsonapi.built-in.lang', null);

	if (is_callable($lang))
	{
		$lang = $lang();
	}
	else if ($lang !== false)
	{
		$lang = Lar\JsonApi\JsonApiLang::fromQuery();
	}

	jsonapi()->register([
		// api/page/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "page/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getPage',
		],
		// api/child-ids/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "child-ids/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getChildIds',
		],
		// api/children/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "children/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getChildren',
		],
		// api/files/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "files/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getFiles',
		],
		// api/node/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "node/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getNode',
		],
		// api/tree/_id_
		[
			'auth' => $auth,
			'lang' => $lang,
			'method' => 'GET',
			'pattern' => "tree/(:all)",
			'controller' => 'Lar\JsonApi\JsonApiController',
			'action' => 'getTree',
		],
		// // api/filter/_id_?filter=_filter-expr_
		// [
		// 	'auth' => $auth,
		// 	'method' => 'GET',
		// 	'pattern' => "filter/(:all)",
		// 	'controller' => 'Lar\JsonApi\JsonApiController',
		// 	'action' => 'getFilteredChildren',
		// ],
	]);
}
