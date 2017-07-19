<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Listener;

use Enm\Bundle\JsonApi\Server\JsonApiServerDecorator;
use Enm\JsonApi\Exception\HttpException;
use Enm\JsonApi\Exception\JsonApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ExceptionListener
{
    /**
     * @var JsonApiServerDecorator
     */
    private $jsonApi;

    /**
     * @var string
     */
    private $routeNamePrefix = 'enm.json_api';

    /**
     * @param JsonApiServerDecorator $jsonApi
     */
    public function __construct(JsonApiServerDecorator $jsonApi)
    {
        $this->jsonApi = $jsonApi;
    }

    /**
     * @param string $routeNamePrefix
     *
     * @return void
     */
    public function setRouteNamePrefix(string $routeNamePrefix)
    {
        $this->routeNamePrefix = $routeNamePrefix;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $route = (string)$event->getRequest()->attributes->get('_route');
        if (strpos($route, $this->routeNamePrefix) === 0) {
            $event->setResponse(
                $this->jsonApi->handleException(
                    $this->convertThrowable($event->getException())
                )
            );
        }
    }

    /**
     * @param \Throwable $throwable
     * @return JsonApiException
     */
    private function convertThrowable(\Throwable $throwable): JsonApiException
    {
        if ($throwable instanceof JsonApiException) {
            return $throwable;
        }

        if ($throwable instanceof HttpExceptionInterface) {
            return $this->createHttpException($throwable->getStatusCode(), $throwable);
        }

        if ($throwable instanceof AuthenticationException) {
            return $this->createHttpException(Response::HTTP_UNAUTHORIZED, $throwable);

        }

        if ($throwable instanceof AccessDeniedException) {
            return $this->createHttpException(Response::HTTP_FORBIDDEN, $throwable);

        }

        return new JsonApiException($throwable->getMessage(), $throwable->getCode(), $throwable);
    }

    /**
     * @param int $statusCode
     * @param \Throwable $throwable
     * @return HttpException
     */
    private function createHttpException(int $statusCode, \Throwable $throwable): HttpException
    {
        return new HttpException(
            $statusCode,
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable
        );
    }
}
