<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Routing;

use Enm\Bundle\JsonApi\Server\Controller\JsonApiController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiLoader extends Loader
{
    /**
     * @var string
     */
    private $apiPrefix;

    /**
     * @param string $apiPrefix
     */
    public function __construct(string $apiPrefix = '')
    {
        $this->apiPrefix = $apiPrefix;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return RouteCollection
     */
    public function load($resource, $type = null): RouteCollection
    {
        $routes = new RouteCollection();
        $routes->add(
            'enm.json_api',
            new Route(
                '/{type}/{id}/{relationshipFirstPart}/{relationshipSecondPart}',
                [
                    '_controller' => JsonApiController::class.':jsonApiAction',
                    'id' => '',
                    'relationshipFirstPart' => '',
                    'relationshipSecondPart' => '',
                ]
            )
        );

        $routes->addPrefix($this->apiPrefix);

        return $routes;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null): bool
    {
        return 'enm.json_api_server' === $type;
    }
}
