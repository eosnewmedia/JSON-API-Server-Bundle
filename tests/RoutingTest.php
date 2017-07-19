<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\Routing\JsonApiLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class RoutingTest extends TestCase
{
    public function testRouting()
    {
        $loader = new JsonApiLoader();
        $router = new Router(
            $loader, '', ['cache_dir' => null], new RequestContext('/')
        );

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/test')['_controller']
        );
        self::assertEquals(
            'enm.json_api',
            $router->match('/test')['_route']
        );

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/test/1')['_controller']
        );
        self::assertEquals(
            'enm.json_api',
            $router->match('/test/1')['_route']
        );

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/test/1/relationship/parent')['_controller']
        );
        self::assertEquals(
            'enm.json_api',
            $router->match('/test/1/relationship/parent')['_route']
        );

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/test/1/parent')['_controller']
        );
        self::assertEquals(
            'enm.json_api',
            $router->match('/test/1/parent')['_route']
        );
    }

    public function testRoutingPrefix()
    {
        $loader = new JsonApiLoader('/api');
        $router = new Router(
            $loader, '', ['cache_dir' => null], new RequestContext('/')
        );

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/api/test')['_controller']
        );
    }

    public function testFetchRequest()
    {
        $loader = new JsonApiLoader();
        $router = new Router($loader, '', ['cache_dir' => null]);


        $request = Request::create('http://example.com/tests/1');

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->matchRequest($request)['_controller']
        );
        self::assertEquals(
            'enm.json_api',
            $router->matchRequest($request)['_route']
        );
    }

    public function testGenerateApiUrl()
    {
        $loader = new JsonApiLoader();
        $router = new Router(
            $loader,
            '',
            ['cache_dir' => null],
            new RequestContext('http://example.com')
        );

        self::assertEquals(
            'http://example.com/tests/1/relationship/abc',
            $router->generate(
                'enm.json_api',
                [
                    'type' => 'tests',
                    'id' => '1',
                    'relationshipFirstPart' => 'relationship',
                    'relationshipSecondPart' => 'abc'
                ]
            )
        );
    }

    public function testGenerateApiUrlWithPrefix()
    {
        $loader = new JsonApiLoader('/api');
        $router = new Router(
            $loader,
            '',
            ['cache_dir' => null],
            new RequestContext('http://example.com')
        );

        self::assertEquals(
            'http://example.com/api/tests/1',
            $router->generate(
                'enm.json_api',
                [
                    'type' => 'tests',
                    'id' => '1'
                ]
            )
        );
    }

    public function testFullRouteLoading()
    {
        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new XmlFileLoader(new FileLocator([__DIR__ . '/../src/Resources/config'])),
                    new JsonApiLoader('/api')
                ]
            )
        );

        $router = new Router($loader, 'routing.xml', ['cache_dir' => null]);

        self::assertEquals(
            'enm.json_api_server.api_controller:jsonApiAction',
            $router->match('/api/test')['_controller']
        );
    }
}
