<?php

namespace Lar\JsonApi;

use c;
use a;
use Response as KirbyResponse;


class JsonApiAuth
{
	public static function isLoggedIn()
	{
		return function () {
			$user = site()->user();
			return !empty($user);
		};
	}

	public static function isUserWithRole($role = null)
	{
		return function () use ($role) {
			$user = site()->user();
			return (!empty($user) && ($role === null || $user->hasRole($role)));
		};
	}

	public static function isAdmin()
	{
		return function () use ($role) {
			$user = site()->user();
			return (!empty($user) && $user->isAdmin($role));
		};
	}
}
