<?php

require_once(__DIR__ . '/JsonApiManager.php');
require_once(__DIR__ . '/JsonApiUtil.php');
require_once(__DIR__ . '/JsonApiController.php');

use Lar\JsonApi\JsonApiManager;
use Lar\JsonApi\JsonApiController;

function jsonapi($class = null) {
	return JsonApiManager::instance();
}

jsonapi()->loadExtensions();
