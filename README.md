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
    1. [Bundles](#bundles)
    1. [Config](#config)
    1. [Routing](#routing)
1. [Request Handler](#request-handler)
1. [Error Handling](#error-handling)

*****
*****

## Configuration

### Bundles

```php
<?php
// confing/bundles.php
return [
    // ...
    Enm\Bundle\JsonApi\Server\EnmJsonApiServerBundle::class => ['all' => true],
    // ...
];

```

*****

### Config
All bundle configurations are optional.

```yaml
# config/packages/(dev/|prod/|test/|)enm_json_api.yaml
enm_json_api_server:
    debug: false
    url_prefix: '' # configure this to use a url prefix for your json api routes: e.g. /api/{type}. only needed if a prefix is defined in your routing
    route_name_prefix: 'enm.json_api' # Prefix of the route names in symfony (for exception handling). only needed if a nam prefix is defined in your routing
```

*****

### Routing

```yaml
# app/config/routing.yml | config/routes.yaml
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

## Request Handler
Each request handler can simply be registered via the service container (tag: `json_api_server.request_handler`):

```yml
AppBundle\RequestHandler\YourRequestHandler:
    tags:
      - { name: json_api_server.request_handler, type: 'myResources' }
```

The tag attribute `type` must contain the json api resource type which will be handled by this request handler.

*****

## Error Handling
The bundle will handle all exceptions and convert them to valid json api error responses.
