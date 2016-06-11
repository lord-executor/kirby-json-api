<?php

namespace Lar\JsonApi;


interface IJsonObject
{
	public function mapField($key, $extractorFn);
	public function selectFields($names);
	public function toArray();
}
