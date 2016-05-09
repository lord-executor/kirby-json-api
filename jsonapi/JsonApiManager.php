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

	public function __construct()
	{
		$this->prefix = c::get('jsonapi.prefix', 'api');
	}

	public function register($actions)
	{
		$apiRoutes = [];

		foreach ($actions as $action) {
			$apiRoutes[] = [
				'method' => a::get($action, 'method', 'GET'),
				'pattern' => $this->prefix . '/' . a::get($action, 'pattern'),
				'action' => $this->dispatch(a::get($action, 'auth'), a::get($action, 'controller'), a::get($action, 'action')),
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

	protected function dispatch($auth, $controller, $action)
	{
		$manager = $this;

		return function () use ($manager, $auth, $controller, $action)
		{
			$args = func_get_args();

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
