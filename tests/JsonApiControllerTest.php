<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\Controller\JsonApiController;
use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
use Enm\Bundle\JsonApi\Server\HttpMessageFactory\HttpMessageFactory;
use Enm\Bundle\JsonApi\Server\JsonApiServerDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiControllerTest extends TestCase
{
    public function testJsonApiAction()
    {
        /** @var JsonApiServerDecorator $jsonApiServer */
        $jsonApiServer = $this->createMock(JsonApiServerDecorator::class);

        $controller = new JsonApiController($jsonApiServer);
        $response = $controller->jsonApiAction(new Request());

        self::assertInstanceOf(Response::class, $response);
    }

    public function testControllerService()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'dev');

        $container->set('app.psr7_factory', new HttpMessageFactory());
        $container->set('app.http_foundation_factory', new HttpFoundationFactory());

        $extension = new EnmJsonApiServerExtension();
        $extension->load(
            [
                'enm_json_api_server' => []
            ],
            $container
        );
        $container->compile();

        $request = Request::create(
            'http://example.com/api/invalidType',
            'GET',
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE' => 'application/vnd.api+json',
            ]
        );

        $response = $container->get(JsonApiController::class)->jsonApiAction($request);
        self::assertEquals(404, $response->getStatusCode());
    }

    public function testControllerServiceWithFullConfiguration()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'dev');

        $container->set('app.psr7_factory', new HttpMessageFactory());
        $container->set('app.http_foundation_factory', new HttpFoundationFactory());
        $container->set('logger', new NullLogger());

        $extension = new EnmJsonApiServerExtension();
        $extension->load(
            [
                'enm_json_api_server' => [
                    'debug' => true,
                    'api_prefix' => '/api',
                    'logger' => 'logger',
                    'psr7_factory' => 'app.psr7_factory',
                    'http_foundation_factory' => 'app.http_foundation_factory',
                ]
            ],
            $container
        );
        $container->compile();

        $request = Request::create(
            'http://example.com/api/invalidType',
            'GET',
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE' => 'application/vnd.api+json',
            ]
        );

        $response = $container->get(JsonApiController::class)->jsonApiAction($request);
        self::assertEquals(404, $response->getStatusCode());
    }
}
