<?php

require_once(__DIR__ . '/JsonField.php');
require_once(__DIR__ . '/StaticField.php');
require_once(__DIR__ . '/PageField.php');
require_once(__DIR__ . '/IJsonObject.php');
require_once(__DIR__ . '/IJsonMap.php');
require_once(__DIR__ . '/JsonListCollection.php');
require_once(__DIR__ . '/JsonFieldCollection.php');
require_once(__DIR__ . '/JsonApiAuth.php');
require_once(__DIR__ . '/JsonApiLang.php');
require_once(__DIR__ . '/JsonApiManager.php');
require_once(__DIR__ . '/JsonApiUtil.php');
require_once(__DIR__ . '/JsonApiController.php');

use Lar\JsonApi\JsonApiManager;


function jsonapi($class = null) {
	return JsonApiManager::instance();
}

jsonapi()->loadExtensions();
