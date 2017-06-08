<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\Listener\ExceptionListener;
use Enm\JsonApi\Exception\Exception;
use Enm\JsonApi\Exception\UnsupportedMediaTypeException;
use Enm\JsonApi\Server\JsonApi;
use Enm\JsonApi\Server\Provider\ResourceProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ExceptionListenerTest extends TestCase
{

    public function testJsonApiException()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new Exception('Test'));
        $listener->onKernelException($event);

        self::assertJson($event->getResponse()->getContent());
        self::assertEquals(500, $event->getResponse()->getStatusCode());

        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertCount(1, $json['errors']);
        self::assertEquals(500, $json['errors'][0]['status']);
        self::assertEquals('Test', $json['errors'][0]['title']);
        self::assertArrayNotHasKey('meta', $json['errors'][0]);
    }

    public function testJsonApiExceptionWithDebug()
    {
        $listener = $this->getListener(true);
        $event = $this->getEvent(new UnsupportedMediaTypeException('Test'));
        $listener->onKernelException($event);

        self::assertJson($event->getResponse()->getContent());
        self::assertEquals(415, $event->getResponse()->getStatusCode());

        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertCount(1, $json['errors']);
        self::assertEquals(415, $json['errors'][0]['status']);
        self::assertEquals('Test', $json['errors'][0]['title']);
        self::assertArrayHasKey('meta', $json['errors'][0]);
        self::assertArrayHasKey('file', $json['errors'][0]['meta']);
        self::assertArrayHasKey('line', $json['errors'][0]['meta']);
    }

    public function testJsonApiExceptionWithCode()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new Exception('Test', 1));
        $listener->onKernelException($event);

        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(1, $json['errors'][0]['code']);
    }

    public function testHttpException()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new NotFoundHttpException());
        $listener->onKernelException($event);

        self::assertEquals(404, $event->getResponse()->getStatusCode());
        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(404, $json['errors'][0]['status']);
    }


    public function testAuthenticationException()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new AuthenticationException());
        $listener->onKernelException($event);

        self::assertEquals(401, $event->getResponse()->getStatusCode());
        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(401, $json['errors'][0]['status']);
    }

    public function testAccessDeniedException()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new AccessDeniedException());
        $listener->onKernelException($event);

        self::assertEquals(403, $event->getResponse()->getStatusCode());
        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(403, $json['errors'][0]['status']);
    }

    public function testInvalidArgumentException()
    {
        $listener = $this->getListener();
        $event = $this->getEvent(new \InvalidArgumentException());
        $listener->onKernelException($event);

        self::assertEquals(400, $event->getResponse()->getStatusCode());
        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(400, $json['errors'][0]['status']);
    }

    public function testException()
    {
        $listener = $this->getListener();
        $listener->setRouteNamePrefix('enm.json_api');
        $event = $this->getEvent(new \Exception());
        $listener->onKernelException($event);

        self::assertEquals(500, $event->getResponse()->getStatusCode());
        $json = json_decode($event->getResponse()->getContent(), true);
        self::assertEquals(500, $json['errors'][0]['status']);
    }

    /**
     * @param bool $debug
     *
     * @return ExceptionListener
     */
    private function getListener(bool $debug = false): ExceptionListener
    {
        return new ExceptionListener(
            new JsonApi($this->createMock(ResourceProviderInterface::class)),
            new TestAppKernel($debug ? 'dev' : 'prod', $debug)
        );
    }

    /**
     * @param \Exception $exception
     *
     * @return GetResponseForExceptionEvent
     */
    private function getEvent(\Exception $exception): GetResponseForExceptionEvent
    {
        return new GetResponseForExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request([], [], ['_route' => 'enm.json_api.fetch']),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );
    }
}
