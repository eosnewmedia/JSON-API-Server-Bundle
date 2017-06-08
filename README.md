JSON API Server-Bundle
======================
[![Build Status](https://travis-ci.org/eosnewmedia/JSON-API-Server-Bundle.svg?branch=master)](https://travis-ci.org/eosnewmedia/JSON-API-Server-Bundle)

The symfony integration for [`enm/json-api-server`](https://eosnewmedia.github.io/JSON-API-Server/).

## Installation

    composer require enm/json-api-server-bundle

*****

## Documentation
You should read the docs of [`enm/json-api-server`](https://eosnewmedia.github.io/JSON-API-Server/) first,
since this bundle only integrate its functionalities into your symfony project.

1. [Configuration](#configuration)
    1. [AppKernel](#appkernel)
    1. [Routing](#routing)
1. [Resource Providers](#resource-providers)
1. [Error Handling](#error-handling)

*****
*****

## Configuration

### AppKernel

```php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Enm\Bundle\JsonApi\Server\EnmJsonApiServerBundle(),
        ];
        
        // ...
        
        return $bundles;
    }
```

*****

### Routing

```yml
# app/config/routing.yml
json_api:
  resource: "@EnmJsonApiServerBundle/Resources/config/routing.xml"
  #prefix: /api #uncomment this line to use a url prefix for your json api routes: e.g. /api/{type}
```

If you use the predefined routing (without prefix), the following routes will be matched to your providers:

    GET /{type}
    
    GET /{type}/{id}
    
    GET /{type}/{id}/relationship/{relationship}
    
    GET /{type}/{id}/{relationship}
    
    POST /{type}
    
    PATCH /{type}/{id}
    
    DELETE /{type}/{id}

*****
*****

## Resource Providers
Each resource provider can simply be registered via the service container (tag: `json_api.resource_provider`):

```yml
app.resource_provider.your_provider:
    class: AppBundle\ResourceProvider\YourResourceProvider
    tags:
      - { name: json_api.resource_provider }
```

The bundle will detect these services and will use the services to configure a resource provider registry which will be
used for the main `enm.json_api` service.

*****
*****

## Error Handling
By default the bundle will handle all exceptions for you.

If you don't use the default routing you have to call `Enm\Bundle\JsonApi\Server\ExceptionListener::setRouteNamePrefix($prefix)`
to configure which routes should handled by the default exception listener.

The service you have to configure is: `enm.json_api.exception_listener`.
