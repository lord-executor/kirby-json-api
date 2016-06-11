# Introduction

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

### Built-In API Features

## Configuration

## Custom API Extensions

### Authentication Options
