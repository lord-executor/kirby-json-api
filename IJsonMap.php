<?php

namespace Lar\JsonApi;


interface IJsonMap extends IJsonObject
{
	public function mapField($key, $extractorFn);
	public function selectFields($names);
}
