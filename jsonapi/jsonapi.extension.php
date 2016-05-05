<?php

require_once(__DIR__ . '/JsonApiController.php');

jsonapi()->register([
	// api/page/_uri_
	[
		'method' => 'GET',
		'pattern' => "page/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getPage',
	],
	// api/child-ids/_uri_
	[
		'method' => 'GET',
		'pattern' => "child-ids/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getChildIds',
	],
	// api/children/_uri_
	[
		'method' => 'GET',
		'pattern' => "children/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getChildren',
	],
	// api/filter/_uri_?filter=_filter-expr_
	[
		'method' => 'GET',
		'pattern' => "filter/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getFilteredChildren',
	],
	// api/files/_uri_
	[
		'method' => 'GET',
		'pattern' => "files/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getFiles',
	],
	// api/node/_uri_
	[
		'method' => 'GET',
		'pattern' => "node/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getNode',
	],
	// api/tree/_uri_
	[
		'method' => 'GET',
		'pattern' => "tree/(:all)",
		'controller' => 'Lar\JsonApi\JsonApiController',
		'action' => 'getTree',
	],
]);
