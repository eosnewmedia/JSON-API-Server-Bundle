<?php
declare(strict_types = 1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\Controller\JsonApiController;
use Enm\JsonApi\Server\JsonApi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiControllerTest extends TestCase
{
    public function testFetchResourceCollectionAction()
    {
        $controller = $this->getController();
        $response   = $controller->fetchResourceCollectionAction(
          $this->buildRequest(), 'test'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testFetchResource()
    {
        $controller = $this->getController();
        $response   = $controller->fetchResourceAction(
          $this->buildRequest(), 'test', '1'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testFetchRelationship()
    {
        $controller = $this->getController();
        $response   = $controller->fetchRelationshipAction(
          $this->buildRequest(), 'test', '1', 'parent'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testFetchRelated()
    {
        $controller = $this->getController();
        $response   = $controller->fetchRelatedAction(
          $this->buildRequest(), 'test', '1', 'parent'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testCreateResource()
    {
        $controller = $this->getController();
        $response   = $controller->createResourceAction(
          $this->buildDataRequest('test', ''), 'test'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testPatchResource()
    {
        $controller = $this->getController();
        $response   = $controller->patchResourceAction(
          $this->buildDataRequest('test', 'test-1'), 'test', 'test-1'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    public function testDeleteResource()
    {
        $controller = $this->getController();
        $response   = $controller->deleteResourceAction(
          $this->buildRequest(), 'test', 'test-1'
        );
        
        self::assertInstanceOf(Response::class, $response);
    }
    
    /**
     * @return JsonApiController
     */
    private function getController(): JsonApiController
    {
        $builder = new ContainerBuilder();
        $builder->set('enm.json_api', $this->createMock(JsonApi::class));
        $builder->compile();
        
        $controller = new JsonApiController();
        $controller->setContainer($builder);
        
        return $controller;
    }
    
    /**
     * @param array $query
     *
     * @return Request
     */
    private function buildRequest(array $query = []): Request
    {
        $request = new Request($query);
        $request->headers->set('Content-Type', 'application/vnd.api+json');
        
        return $request;
    }
    
    /**
     * @param string $type
     * @param string $id
     *
     * @return Request
     */
    private function buildDataRequest(string $type, string $id): Request
    {
        $request = new Request(
          [],
          [],
          [],
          [],
          [],
          [],
          json_encode(
            [
              'data' => ['type' => $type, 'id' => $id],
            ]
          )
        );
        $request->headers->set('Content-Type', 'application/vnd.api+json');
        
        return $request;
    }
}
