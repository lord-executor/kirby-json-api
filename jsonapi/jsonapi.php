<?php

require_once(__DIR__ . '/BlueprintHelper.php');
require_once(__DIR__ . '/IJsonProperty.php');
require_once(__DIR__ . '/PageFieldInfo.php');
require_once(__DIR__ . '/PageFieldCollection.php');
require_once(__DIR__ . '/DefaultFieldVisitor.php');
require_once(__DIR__ . '/PropertyTypes/RawProperty.php');
require_once(__DIR__ . '/PropertyTypes/StringProperty.php');
require_once(__DIR__ . '/PropertyTypes/ReferenceProperty.php');
require_once(__DIR__ . '/JsonApiManager.php');
require_once(__DIR__ . '/JsonApiUtil.php');
require_once(__DIR__ . '/JsonApiController.php');

use Lar\JsonApi\JsonApiManager;
use Lar\JsonApi\JsonApiController;

function jsonapi($class = null) {
	return JsonApiManager::instance();
}

jsonapi()->loadExtensions();
