<?php

namespace Lar\JsonApi;


class DefaultFieldVisitor
{
	public function visitPageField($field, $type, $settings)
	{
		$typeName = $type . 'PageField';
		if (!method_exists($this, $typeName))
		{
			$typeName = 'defaultPageField';
		}

		return $this->{$typeName}($field, $type, $settings);
	}

	protected function defaultPageField($field, $type, $settings)
	{
		var_dump($field->key() . " > " . $type . " >> " . $field->value());
		return $field->value();
	}

	protected function referencePageField($field, $type, $settings)
	{
		var_dump("reference: " . $field->value());
		#var_dump($field->toPage());
		#var_dump(page($field->value()));
		#var_dump(page("locations/konzertsaal-kkl"));
		$page = $field->toPage();
		return $page ? JsonApiUtil::pageToCollection($page)->toArray($visitor) : $field->value();
	}
}
