<?php

namespace Lar\JsonApi;

use c;
use a;
use Response as KirbyResponse;


class JsonApiManager
{
	static public $instance;

	static public function instance() {
		if(!is_null(static::$instance)) return static::$instance;
		return static::$instance = new static;
	}


	protected $prefix;
	protected $defaultLang;

	public function __construct()
	{
		$this->prefix = c::get('jsonapi.prefix', 'api');
		$this->defaultLang = JsonApiLang::fromQuery();
	}

	public function register($actions)
	{
		$apiRoutes = [];

		foreach ($actions as $action) {
			$apiRoutes[] = [
				'method' => a::get($action, 'method', 'GET'),
				'pattern' => $this->prefix . '/' . a::get($action, 'pattern'),
				'action' => $this->dispatch(a::get($action, 'auth'), a::get($action, 'controller'), a::get($action, 'action'), a::get($action, 'lang', $this->defaultLang)),
			];
		}

		kirby()->routes($apiRoutes);
	}

	public function loadExtensions()
	{
		// get the plugins root
		$root = kirby()->roots->plugins();

		// check for an existing plugins dir
		if(!is_dir($root)) return;

		foreach(array_diff(scandir($root), array('.', '..')) as $file)
		{
			if(is_dir($root . DS . $file))
			{
				$extension = $root . DS . $file . DS . 'jsonapi.extension.php';
				if(file_exists($extension)) include_once($extension);
			}
		}
	}

	protected function dispatch($auth, $controller, $action, $lang)
	{
		$manager = $this;

		return function () use ($manager, $auth, $controller, $action, $lang)
		{
			$args = func_get_args();
			$site = site();

			if (isset($site->language) && !empty($lang))
			{
				// make sure to set the site language for this request according to the 'lang'
				// action setting, otherwise everything will be returned in the default language
				$site->language = is_callable($lang) ? call_user_func_array($lang, $args) : $site->{$lang . 'Language'}();
				if ($site->language === NULL) {
					$site->language = $site->defaultLanguage();
				}
			}

			if (!$manager->authenticate($auth, $args))
			{
				return KirbyResponse::json('unauthorized', 401);
			}

			$callable = $action;
			if (!empty($controller))
			{
				$instance = new $controller();
				$callable = [$instance, $action];
			}
			$result = call_user_func_array($callable, $args);

			if ($result instanceof IJsonObject)
			{
				return KirbyResponse::json($result->toArray());
			}
			else if ($result instanceof KirbyResponse)
			{
				return $result;
			}
			else
			{
				return KirbyResponse::json($result);
			}
		};
	}

	protected function authenticate($auth, $args)
	{
		if (empty($auth))
		{
			return true;
		}

		if (is_callable($auth))
		{
			return call_user_func_array($auth, $args);
		}

		return false;
	}
}
