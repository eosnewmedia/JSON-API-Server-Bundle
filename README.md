JSON API Server-Bundle
======================
[![Build Status](https://travis-ci.org/eosnewmedia/JSON-API-Server-Bundle.svg?branch=master)](https://travis-ci.org/eosnewmedia/JSON-API-Server-Bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/56e6d8ea-6f12-45e6-8c2c-c8a75c8a65c7/mini.png)](https://insight.sensiolabs.com/projects/56e6d8ea-6f12-45e6-8c2c-c8a75c8a65c7)

The symfony integration for [`enm/json-api-server`](https://eosnewmedia.github.io/JSON-API-Server/).

## Installation

    composer require enm/json-api-server-bundle

*****

## Documentation
You should read the docs of [`enm/json-api-server`](https://eosnewmedia.github.io/JSON-API-Server/) first,
since this bundle only integrate its functionalities into your symfony project.

1. [Configuration](#configuration)
    1. [AppKernel](#appkernel)
    1. [Config](#config)
    1. [Routing](#routing)
1. [Request Handler](#request-handler)
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

### Config
All bundle configurations are optional.

```yaml
enm_json_api_server:
    debug: false
    api_prefix: "/api" # configure this to use a url prefix for your json api routes: e.g. /api/{type}
    logger: "logger" # a service implementing the psr-3 log interface to log exceptions and debug messages
    psr7_factory: "your_psr7_factory_service" # only required if you do not want to use "zend-diactoros" for symfony request/response converting
    http_foundation_factory: "your_http_foundation_factory_service" # only required if you do not want to use the default implementation shipped with "symfony/psr-http-message-bridge"
```

*****

### Routing

```yaml
# app/config/routing.yml
json_api:
  resource: "@EnmJsonApiServerBundle/Resources/config/routing.xml"
```

If you use the predefined routing (without api prefix configuration), the following routes will be matched:

    GET /{type}
    
    GET /{type}/{id}
    
    GET /{type}/{id}/relationship/{relationship}
    
    GET /{type}/{id}/{relationship}
    
    POST /{type}
    
    PATCH /{type}/{id}
    
    DELETE /{type}/{id}
    
    POST /{type}/{id}/relationship/{relationship}
    
    PATCH /{type}/{id}/relationship/{relationship}
    
    DELETE /{type}/{id}/relationship/{relationship}

*****
*****

## Request Handler
Each resource provider can simply be registered via the service container (tag: `json_api_server.resource_provider`):

```yml
AppBundle\RequestHandler\YourRequestHandler:
    tags:
      - { name: json_api_server.resource_provider, type: 'myResources' }
      
AppBundle\RequestHandler\YourGenericRequestHandler:
    tags:
      - { name: json_api_server.resource_provider }
```

The tag attribute `type` must contain the json api resource type which will be handled by this request handler or can 
be empty to direct all requests to this handler.

If all requests are handled by your request handler it should be throw a UnsupportedTypeException for unsupported 
resource types. If a UnsupportedTypeException is thrown the bundle tries the next registered request handler.

Request handlers with configured type and resource providers are always called before your generic handlers are called.
If a request handler or resource provider matches a request the generic handlers are not called anymore.

Request handlers with configured type are always called before resource providers.
If a request handler matches a request resource providers are not called anymore.

## Resource Providers
Each resource provider can simply be registered via the service container (tag: `json_api_server.resource_provider`):

```yml
app.resource_provider.your_provider:
    class: AppBundle\ResourceProvider\YourResourceProvider
    tags:
      - { name: json_api_server.resource_provider, type: 'myResources' }
```

The tag attribute `type` must contain the json api resource type which will be handled by this provider.

*****
*****

## Error Handling
The bundle will handle all exceptions and convert them to valid json api error responses.
