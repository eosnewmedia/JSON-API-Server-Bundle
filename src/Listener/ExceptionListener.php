<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Listener;

use Enm\Bundle\JsonApi\Server\Response\JsonApiResponse;
use Enm\JsonApi\Exception\HttpException;
use Enm\JsonApi\Exception\JsonApiException;
use Enm\JsonApi\Server\JsonApiServer;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ExceptionListener
{
    /**
     * @var string
     */
    private $routeNamePrefix;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var JsonApiServer
     */
    private $jsonApi;

    /**
     * @param string $routeNamePrefix
     * @param bool $debug
     * @param JsonApiServer $jsonApi
     */
    public function __construct(string $routeNamePrefix, bool $debug, JsonApiServer $jsonApi)
    {
        $this->routeNamePrefix = $routeNamePrefix;
        $this->debug = $debug;
        $this->jsonApi = $jsonApi;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $apiRoute = strpos((string)$event->getRequest()->attributes->get('_route'), $this->routeNamePrefix) === 0;
        $apiType = $event->getRequest()->headers->get('Content-Type') === 'application/vnd.api+json';

        if ($apiRoute || $apiType) {
            $response = $this->jsonApi->handleException($this->convertThrowable($event->getException()), $this->debug);
            $event->setResponse(
                new JsonApiResponse(
                    $this->jsonApi->createResponseBody($response),
                    $response->status(),
                    $response->headers()->all()
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
            return new HttpException(
                $throwable->getStatusCode(),
                $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }

        return new JsonApiException($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
