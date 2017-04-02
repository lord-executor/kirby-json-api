# JSON API Plugin
The Kirby JSON API plugin is a fairly simple layer on top of the existing Kirby infrastructure that provides a language-aware, read-only (for now) JSON API to access the content tree from JavaScript and other external clients. It also provides some basic functionality for developers to easily add their own JSON-based APIs.

## Table of Contents
* [Installation](#installation)
* [Who Should Use This?](#who-should-use-this)
* [Built-In API](#built-in-api)
  * [Configuring the Built-In API](#configuring-the-built-in-api)
  * [Built-In API Features](#built-in-api-features)
* [Custom API Extensions](#custom-api-extensions)
  * [Registering Routes](#registering-routes)
  * [Actions](#actions)
  * [Authentication](#authentication)
  * [Language](#language)
  * [Working with Pages](#working-with-pages)
* [Using the API from JavaScript](#using-the-api-from-javascript)
  * [Exposing some Kirby to JavaScript](#exposing-some-kirby-to-javascript)
* [Examples](#examples)
* [Sponsors](#sponsors)

## Installation

### Using the KirbyCLI
```bash
kirby plugin:install lord-executor/kirby-json-api
```

### Manually
Get the plugin files by downloading a ZIP archive of the source from https://github.com/lord-executor/kirby-json-api or by cloning the repository with
```bash
git clone https://github.com/lord-executor/kirby-json-api.git
```

Then, copy all the files into your Kirby installation's `site/plugins` directory under a newly created `jsonapi` directory.

**Note** that the plugin directory _must_ be named `jsonapi` and it _must_ directly contain the `jsonapi.php` file.

### Configuration Quick-Start
Add the following to your `site/config/config.php`

```php
/*
JSON API Configuration
*/
c::set('jsonapi.built-in.enabled', true);
// this is for demonstration purposes ONLY - in any kind of "real world" application
// this should be set to _some_ form of authentication as described in the documentation
c::set('jsonapi.built-in.auth', false);
```

## Who Should Use This?
Well... everybody obviously ;)
But seriously;
* are you building a website with some advanced JavaScript that needs to access your Kirby content structure asynchronously?
* are you trying to build a single page application (SPA) with Angular, Ember, etc.?

If you answered any of the above questions with "yes", then this plugin is likely something you could benefit from.

# Built-In API
> **Note**: The built-in API is disabled by default and has to be enabled first in the Kirby configuration as described below.

## Configuring the Built-In API
**Note:** Configuring handler functions for the built-in API (namely the `jsonapi.built-in.auth` and `jsonapi.built-in.lang` handlers) works differently from setting the same handlers directly on custom API routes.

Since most classes are not yet loaded when the configuration file is being executed, you can't actually use any of the classes provided by the JSON API or other plugins directly.
You can however use **factory functions** to do this. A factory function is just a function that returns the actual handler.
Since the factory function is executed when the built-in API is being initialized, all classes from the JSON API are available in the factory function body.

### Enabling the API
To enable the built-in API, add the following to your Kirby configuration. The built-in API is disabled by default as a security measure. You should only enable it if you actually need it and if you do, you should make sure to check out the remaining security configuration options.
```php
c::set('jsonapi.built-in.enabled', true);
```

### Authentication and Authorization
Once enabled, the built-in API is only available to the logged-in Kirby admin - again, primarily for security.

To make the API available to all logged-in users, you can use a **factory** function to set the configuration as follows:
```php
c::set('jsonapi.built-in.auth', function () {
	return Lar\JsonApi\JsonApiAuth::isLoggedIn();
});
```

To completely disable the authentication and make the API available to anybody, set the configuration like this:
```php
c::set('jsonapi.built-in.auth', false);
```

There are plenty more authentication options available (those are described below) and you can also implement your own. Be aware that the _configuration_ option `jsonapi.built-in.auth` expects an _authentication provider_ function - that is a function that returns the actual authentication function. This is to work around the bootstrapping issue where the plugin providing the authentication function isn't actually loaded yet. The _provider_ function is invoked when the built-in API is registered, at which point all the functionality of the JSON API plugin are available.

### Setting the Language Handler
The built-in API normally uses the global default language handler which determines the language from the `lang` query string parameter.
You can change this by providing your own **factory function** that returns a language handler.

```php
c::set('jsonapi.built-in.lang', function () { return Lar\JsonApi\JsonApiLang::fromPathSegment(); });
```

For more details on the available options, see the [Language](#language) section.

### API Path Prefix
All registered API controllers are made available under **one** path prefix that defaults to `api`, so all registered URL patterns are automatically prefixed with this value. You can of course change this prefix in the configuration:

```php
c::set('jsonapi.prefix', 'myapi');
```

All the examples below assume the default prefix `api`.

### Disabling Autodetection of Strucutred Fields
If the automatic detection and processing of structured fields causes problems for you, this can be disabled with:

```php
c::set('jsonapi.auto-structured', false);
```

## Built-In API Features

### Pages, Nodes and Trees
The primary API comes in three flavors:
* `[prefix]/page/[id]`: This will return all the page fields of a given page with the addition to the default meta fields described below.
* `[prefix]/node/[id]`: This works just like `page`, but adds all the files associated with a page as well as the `id`s of all its children.
* `[prefix]/tree/[id]`: Just like `node`, except that instead of only returning child `id`s, this endpoint _recursively_ includes all of the child pages.

All of these endpoints take the same single argument that is the `id` or a page. If for example you want to get the `page` with the ID `path/to/page`, then you can call the API with the path `/api/page/path/to/page`.

#### Examples
Example of the result returned by a call to the `page` API with `/api/page/projects/project-a`
```json
{
	"id":"projects\/project-a",
	"url":"http:\/\/mydomain.com\/projects\/project-a",
	"uid":"project-a",
	"title":"Project A",
	"text":"Lorem ipsum dolor sit amet"
}
```

Example of the result returned by a call to the `node` API with `/api/node/projects/project-a`
```json
{
	"id":"projects\/project-a",
	"url":"http:\/\/mydomain.com\/projects\/project-a",
	"uid":"project-a",
	"title":"Project A",
	"text":"Lorem ipsum dolor sit amet",
	"files":[
		{
			"url":"http:\/\/mydomain.com\/content\/2-projects\/1-project-a\/forest.jpg",
			"name":"forest",
			"extension":"jpg",
			"size":280900,
			"niceSize":"274.32 kB",
			"mime":"image\/jpeg",
			"type":"image"
		},
		{
			"url":"http:\/\/mydomain.com\/content\/2-projects\/1-project-a\/green.jpg",
			"name":"green",
			"extension":"jpg",
			"size":306670,
			"niceSize":"299.48 kB",
			"mime":"image\/jpeg",
			"type":"image"
		}
	],
	"children":["projects\/project-a\/sub"]
}
```

Example of the result returned by a call to the `tree` API with `/api/page/projects`
```json
{
	"id": "projects",
	"url": "http:\/\/mydomain.com\/projects",
	"uid": "projects",
	"title": "Projects",
	"text": "",
	"files": [],
	"children": [
		{
			"id": "projects\/project-a",
			...
		},
		{
			"id": "projects\/project-b",
			...
		},
		{
			"id": "projects\/project-c",
			...
		}
	]
}
```

#### Meta Fields
These fields are automatically added to the response of every page.
* `id`: see https://getkirby.com/docs/cheatsheet/page/id
* `url`: see https://getkirby.com/docs/cheatsheet/page/url
* `uid`: see https://getkirby.com/docs/cheatsheet/page/uid

### Child IDs, Children and Files
Every addition to the basic `page` information is also available as a separate API endpoint.

* `[prefix]/child-ids/[id]`: Returns an array of child IDs of the given page.
* `[prefix]/children/[id]`: Returns an array of child pages of the given page including all of their fields (non-recursive).
* `[prefix]/files/[id]`: Returns an array of all files associated with the given page.

### Structured Fields
As of v-1.1.0, there is a feature that tries to automatically detect structured fields (based on whether it _looks_ like a YAML document) and converts them to JSON objects as well.

Note that structured fields are always an **array** of items, so the generated output looks something like this
```json
{
	"id": "root\/child-one",
	"url": "http:\/\/localhost:8080\/root\/child-one",
	"uid": "child-one",
	"title": "Child One",
	"text": "This is the first child",
	"value": "42.1",
	"nextnode": "root",
	"structured": [
		{
			"title": "My Title",
			"message": "..."
		},
		{
			"title": "Sub-Item 2",
			"message": "..."
		},
		{
			"title": "Last",
			"message": "..."
		}
	]
}
```

### Multi-Language Sites
The API manager is the component that is in charge of invoking the API controllers. It is aware of multi-language sites and sets the language based on the `lang` query string parameter by default.


# Custom API Extensions
Getting started with a custom API is really quite straight forward. All you need is a Kirby plugin (that can be an existing plugin or a new one) where you can create a file called `jsonapi.extension.php`. The JSON API plugin looks for files with that name during its initialization and loads them automatically. In this extension file, you can now simply declare your API like this

```php
<?php

jsonapi()->register([
	// api/custom/_name_/_value_
	[
		'method' => 'GET',
		'pattern' => "custom/(:any)/(:num)",
		'action' => function ($name, $value) {
			return ['msg' => "got $name with value $value"];
		},
	],
]);
```

**Note:** For the route pattern parameters see https://getkirby.com/docs/developer-guide/advanced/routing.

That's it. You've got yourself an API which you can call with `/api/custom/me/42`.

## Registering Routes
The global `jsonapi()` function returns an instance of the API manager whose `register($actions)` method takes an array of API route definitions.

## Routing
The JSON API is based on Kirby's [routing mechanism](https://getkirby.com/docs/developer-guide/advanced/routing) and adds some more options and a bit of framework functionality to simplify working with JSON.

The route options `method`, `pattern` and `action` are essentially taken as-is from the Kirby routing system, so please check [that documentation](https://getkirby.com/docs/developer-guide/advanced/routing) for more details.

## Actions
An action can be any [PHP callable](http://php.net/manual/en/language.types.callable.php) just like in any other Kirby route, but additionally, you can also use the _controller_ syntax in your route definitions.

```php
[
	// ...
	'controller' => 'MyController',
	'action' => 'myAction',
	// ...
]
```

The _controller_ class will be instantiated before the action is invoked. The controller must therefore have a parameterless constructor. Once instantiated, the action is invoked on the controller.

Normally, your custom API actions will return a (quite possibly nested) PHP array or PHP scalar which will be converted to a JSON response automatically. The result of an API action can also be any valid Kirby `response` object in which case Kirby's default response handling kicks in. As a middle ground, you can also return any object that implements the `Lar\JsonApi\IJsonObject` interface which gives you full control over how your objects will be serialized to JSON.

If your API is dealing with Kirby page objects, you can also use the helper objects and utilities that come with the JSON API plugin to craft your response. See the section on working with pages below.

## Authentication
The `auth` option of an API route definition ensures that unauthorized requests are handled with an HTTP 401 response. If set, the `auth` option has to be a PHP callable that explicitly returns `true` if the request has been authorized or `false` if the request should be blocked. The authorization function receives the same arguments as the controller.

The JSON API plugin provides the following predefined authorization handlers (see `Lar\JsonApi\JsonApiAuth` for more details):
* `Lar\JsonApi\JsonApiAuth::isLoggedIn()`: returns an auth handler that returns `true` if and only if the user making the request is logged in.
* `Lar\JsonApi\JsonApiAuth::isAdmin()`: returns an auth handler that returns `true` if and only if the user making the request is logged in and has the `admin` role.
* `Lar\JsonApi\JsonApiAuth::isUserWithRole($role = null)`: returns an auth handler that returns `true` if and only if the user making the request is logged in and has the role that is provided as the function argument.

**Example**
```php
[
	// ...
	// infamous "I hate Mondays" API that cannot be used on Mondays
	'auth' => function () {
		$now = new DateTime();
		return ($now->format('N') !== '1');
	},
	// ...
]
```

## Language
Language handling for APIs can be quite tricky, so like everything else, this aspect is customizable. Adding the `lang` option to your routes allows you to specify a language selection handler which receives the same arguments as the controller and returns the language that will be used to fetch data from Kirby.

If you don't specify anything else, the default language handler `Lar\JsonApi\JsonApiLang::fromQuery()` will be used; it determines the language through the `lang` query string parameter. If it matches one of the configured languages, that language will be used.

The JSON API comes with the following language handler implementations:
* `Lar\JsonApi\JsonApiLang::fromQuery($name = 'lang')`: returns a language handler which tries to pick the language based on the language code provided in the query string parameter with the given `$name`.
* `̀Lar\JsonApi\JsonApiLang::fromPathSegment($index = 1)`: returns a language handler which uses the URI path segment at `$index` as the language code. This can also be a negative number, in which case the path segments are counted from the back.

**Example for Language by Path Segment**
```php
[
	// ...
	'method' => 'GET',
    'pattern' => "(:any)/my-api",
	'lang' => \Lar\JsonApi\JsonApiLang::fromPathSegment(),
	// ...
]
```
If this API is called with the URL `/api/de/my-api`, then all page contend will be retrieved in German - provided that German is configured as one of the site languages.

Besides a callback, the `lang` option can also be one of the following strings:
* `session` (default): The session's current browsing language. See https://getkirby.com/docs/cheatsheet/site/session-language.
* `default`: The site's default language. See https://getkirby.com/docs/cheatsheet/site/default-language
* `detected`: The user's detected language. See https://getkirby.com/docs/cheatsheet/site/detected-language
* `visitor`: The language determined for the current visitor. See https://getkirby.com/docs/cheatsheet/site/visitor-language

## Working with Pages
The built-in API is fairly generic and may be too much or too little depending on your needs. If you are working with Kirby pages you might want some more control over what fields are included or how field values are returned. The utility functions around JsonApiUtil and the JsonFieldCollection/JsonListCollection should allow you to do just that.

### Using JsonApiUtil
Before you start _customizing_ the returned result, you probably want to get started by converting your Kirby page(s) to something more JSON-friendly.

* `Lar\JsonApi\JsonApiUtil::pageToJson($page)`
  Converts the given Kirby page object or collection of pages to an instance of `Lar\JsonApi\JsonFieldCollection` or `Lar\JsonApi\JsonListCollection` respectively. In the case of a page collection, the result list collection object will contain one field collection for each page.
* `Lar\JsonApi\JsonApiUtil::pageToNode($page, $fullTree = false)`
  Works on page objects or a collection of pages just like `pageToJson`, but it adds the page's files and children to the returned object as well. If the `$fullTree` parameter is set to true, children are returned recursively.

### Selecting and Mapping Fields
Once you have a `JsonListCollection` or `JsonFieldCollection` object, both of which implement the `IJsonMap` interface, you can pick the fields you want to return with the `selectFields($names)` method:

```php
return JsonApiUtil::pageToJson($myPage)
	->selectFields(['id', 'url', 'title', 'text');
```

Only fields included in this list will be returned.

By default, the page fields are returned as-is, in the form of textual data. Since you might not always want to push the burden of processing the fields to the client, you can use the `mapField($key, $extractorFn)` method:

```php
return JsonApiUtil::pageToJson($myPage)
	// the 'location' field contains a select-type link to another page
	->mapField('location', function ($field) {
		// the field contains the _id_ of the target page, so we can just
		// use Kirby's 'toPage' method.
		$target = $field->toPage();
		// always watch your false/null values
		return $target ? JsonApiUtil::pageToJson($target) : null;
	})
	// the 'index' field contains a number and we would like the returned JSON
	// to contain a number too (instead of a string that happens to contain digits)
	->mapField('index', function ($field) {
		return intval($field->value());
	});
```

The `$extractorFn` receives the collection's `$field` definition as its argument. In the case of a Kirby page that has been converted to a JSON list/field collection, that definition is the field instance. This means that all the [Kirby Field Methods](https://getkirby.com/docs/cheatsheet#field-methods) are available to you.

When one of these functions is called on a list collection, then the mapping or selection is applied to all field collections in that list (non-recursively).

# Using the API from JavaScript
Usually you'll want to access the API from _somewhere_, that somewhere likely being JavaScript code. One of the first problems you'll likely run into is that of figuring out the right URLs for your API calls.
* Relative URLs are kinda clunky and not to be recommended in this situation
* Absolute paths make an assumption that your Kirby site is hosted under a specific path and may break with different environemtns
* Full URIs are even more specific and will break if you have a test environment

## Exposing some Kirby to JavaScript
A good practice is to simply expose some of Kirby's settings to JavaScript in an easy-to-digest format like a JavaScript object. Add the following snippet to your main HTML header snippet - most Kirby pages tend to have one of those; the snippet should be included in all of your templates.

```html
<script type="text/javascript">
    /**
     * Kirby JavaScript settings object. It contains settings and functions that JavaScript code
     * potentially needs to know about when dealing with Kirby
     */
    window.Kirby = {
        lang: '<?php echo $site->language()->code(); ?>',
        apiPrefix: '<?php echo c::get('jsonapi.prefix', 'api'); ?>',
        baseUrl: '<?php echo $site->url(); ?>',
        langUrl: '<?php echo $site->language()->url(); ?>',
        url: function (path) {
            return this.baseUrl + (path && path[0] === '/' ? path : '/' + path);
        },
        pageUrl: function (path) {
            return this.langUrl + (path && path[0] === '/' ? path : '/' + path);
        },
        apiUrl: function (path) {
            var url = this.baseUrl + '/' + this.apiPrefix + (path && path[0] === '/' ? path : '/' + path);
            url += (url.indexOf('?') < 0 ? '?' : '&') + 'lang=' + this.lang;
            return url;
        },
    };
</script>
```

With this, it is now trivial to access your API without having to worry about the path your Kirby instance is hosted under, the configured API prefix or the language. Just use the `apiUrl` function of the new Kirby object to build your actual URL like so:

```javascript
$.get(Kirby.apiUrl('/my/path')).then(...);
```

# Examples

## Custom API with All Bells and Whistles
```php
<?php

use Lar\JsonApi\JsonApiUtil;
use response as KirbyResponse;

class SettingsController
{
	public function getSettings($name)
	{
		$page = page('meta/settings')->find($name);

		if ($page === false)
			return KirbyResponse::error('Page not found', 404, ['name' => $name]);

		return JsonApiUtil::pageToJson($page)
			->selectFields(['depth', 'home'])
			->mapField('depth', function ($field) {
				return intval($field->value());
			})
			->mapField('home', function ($field) {
				$target = $field->toPage();
				return $target ? JsonApiUtil::pageToJson($target) : null;
			});
	}
}

jsonapi()->register([
	// api/custom/settings/_name_
	[
		'method' => 'GET',
		'pattern' => "custom/settings/(:any)",
		// only allow access if 'name' does not contain slashes
		'auth' => function ($name) { return strpos($name, '/') === false; },
		// force language to English
		'lang' => function () { return 'en'; },
		'controller' => 'SettingsController',
		'action' => 'getSettings',
	],
]);
```

## Inline Action
```php
<?php

jsonapi()->register([
	// api/data
	[
		'method' => 'GET',
		'pattern' => 'data',
		'action' => function () {
			$projects = page('projects')->children();
			$files = [];

			// just get all the files for each project
			JsonApiUtil::pageToNode($projects)
				->mapField('files', function ($field) use (&$files) {
					$files = array_merge($files, $field->getValue()->toArray());
				})
				// this is needed to trigger the serialization which will call
				// the field mapping function
				->toArray();

			return $files;
		},
	],
]);
```

# Sponsors

![getunik.com Logo](http://getunik.com/themes/img/gu_logo.svg "getunik.com Logo")

Development of this plugin was partially sponsored by [getunik.com](http://getunik.com/)
