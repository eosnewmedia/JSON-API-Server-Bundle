<?php
declare(strict_types = 1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
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
        $loader = new XmlFileLoader(new FileLocator([__DIR__.'/../src/Resources/config']));
        $router = new Router(
          $loader, 'routing.xml', ['cache_dir' => null], new RequestContext('/')
        );
        
        self::assertEquals(
          'EnmJsonApiServerBundle:JsonApi:fetchResourceCollection',
          $router->match('/test')['_controller']
        );
        self::assertEquals(
          'enm.json_api.fetch_resources',
          $router->match('/test')['_route']
        );
        
        
        self::assertEquals(
          'EnmJsonApiServerBundle:JsonApi:fetchResource',
          $router->match('/test/1')['_controller']
        );
        self::assertEquals(
          'enm.json_api.fetch_resource',
          $router->match('/test/1')['_route']
        );
        
        
        self::assertEquals(
          'EnmJsonApiServerBundle:JsonApi:fetchRelationship',
          $router->match('/test/1/relationship/parent')['_controller']
        );
        self::assertEquals(
          'enm.json_api.fetch_relationship',
          $router->match('/test/1/relationship/parent')['_route']
        );
        
        
        self::assertEquals(
          'EnmJsonApiServerBundle:JsonApi:fetchRelated',
          $router->match('/test/1/parent')['_controller']
        );
        self::assertEquals(
          'enm.json_api.fetch_related',
          $router->match('/test/1/parent')['_route']
        );
    }
}
