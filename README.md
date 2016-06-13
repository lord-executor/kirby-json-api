# Introduction
The Kirby JSON API plugin is a fairly simple layer on top of the existing Kirby infrastructure that provides a (read-only) JSON API to access the content tree from JavaScript and other external clients. It also provides some basic functionality for developers to easily add their own JSON-based APIs.

## Built-in API
> **Note**: The built-in API is disabled by default and has to be enabled first in the Kirby configuration as described below.

### Configuring the Built-In API

#### Enabling the API
To enable the built-in API, add the following to your Kirby configuration. The built-in API is disabled by default as a security measure. You should only enable it if you actually need it and if you do, you should make sure to check out the remaining security configuration options.
```php
c::set('jsonapi.built-in.enabled', true);
```
#### Authentication and Authorization
Once enabled, the built-in API is only available to the logged-in Kirby admin - again, primarily for security.

To make the API available to all logged-in users, you can set the configuration as follows:
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

#### API Path prefix
All registerd API controllers are made available under **one** path prefix that defaults to `api`, so all registerd URL patterns are automatically prefixed with this value. You can of course change this prefix in the configuration:

```php
c::set('jsonapi.prefix', 'myapi');
```

All the examples below assume the default prefix `api`.

### Built-In API Features

#### Pages, Nodes and Trees
The primary API comes in three flavors:
* `[prefix]/page/[id]`: This will return all the page fields of a given page with the addition to the default meta fields described below.
* `[prefix]/node/[id]`: This works just like `page`, but adds all the files associated with a page as well as the `id`s of all its children.
* `[prefix]/tree/[id]`: Just like `node`, except that instead of only returning child `id`s, this endpoint _recursively_ includes all of the child pages.

All of these endpoints take the same single argument that is the `id` or a page. If for example you want to get the `page` with the ID `path/to/page`, then you can call the API with the path `/api/page/path/to/page`.

##### Examples
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
			....
		}
	]
}
```

##### Meta fields
These fields are automatically added to the response of every page.
* `id`: see https://getkirby.com/docs/cheatsheet/page/id
* `url`: see https://getkirby.com/docs/cheatsheet/page/url
* `uid`: see https://getkirby.com/docs/cheatsheet/page/uid

#### Child IDs, Children and Files
Every addition to the basic `page` information is also available as a separate API endpoint.

* `[prefix]/child-ids/[id]`: Returns an array of child IDs of the given page.
* `[prefix]/children/[id]`: Returns an array of child pages of the given page including all of their fields (non-recursive).
* `[prefix]/files/[id]`: Returns an array of all files associated with the given page.

## Configuration

## Custom API Extensions

### Authentication Options
