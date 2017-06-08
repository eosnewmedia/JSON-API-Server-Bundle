<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Listener;

use Enm\Bundle\JsonApi\Server\Exception\HttpException;
use Enm\JsonApi\Server\JsonApi;
use Enm\JsonApi\Model\Error\Error;
use Enm\JsonApi\Model\Error\ErrorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ExceptionListener
{
    /**
     * @var JsonApi
     */
    private $jsonApi;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $routeNamePrefix = 'enm.json_api';

    /**
     * @param JsonApi $jsonApi
     * @param KernelInterface $kernel
     */
    public function __construct(JsonApi $jsonApi, KernelInterface $kernel)
    {
        $this->jsonApi = $jsonApi;
        if (in_array($kernel->getEnvironment(), ['dev', 'test'], true)) {
            $this->debug = true;
        }
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
            $error = $this->createErrorFromException($event->getException());

            $event->setResponse($this->jsonApi->handleError($error));
        }
    }

    /**
     * @param \Exception $exception
     *
     * @return ErrorInterface
     */
    private function createErrorFromException(\Exception $exception): ErrorInterface
    {
        if ($exception instanceof HttpExceptionInterface) {
            $exception = new HttpException(
                $exception->getStatusCode(),
                $exception
            );
        }

        if ($exception instanceof AuthenticationException) {
            $exception = new HttpException(
                Response::HTTP_UNAUTHORIZED,
                $exception
            );
        }

        if ($exception instanceof AccessDeniedException) {
            $exception = new HttpException(
                Response::HTTP_FORBIDDEN,
                $exception
            );
        }

        if ($exception instanceof \InvalidArgumentException) {
            $exception = new HttpException(
                Response::HTTP_BAD_REQUEST,
                $exception
            );
        }

        return Error::createFromException($exception, $this->debug);
    }
}
