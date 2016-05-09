<?php

namespace Lar\JsonApi;


abstract class JsonField
{
	private $extractorFn;

	public function setExtractor($extractorFn)
	{
		$this->extractorFn = $extractorFn;
	}

	public function getExtractor()
	{
		return $this->extractorFn;
	}

	public function extract()
	{
		$fn = ($this->extractorFn ? $this->extractorFn : $this->getDefaultExtractor());

		return $this->callExtractor($fn);
	}

	protected function callExtractor($fn)
	{
		return $fn($this);
	}

	protected abstract function getDefaultExtractor();
}
