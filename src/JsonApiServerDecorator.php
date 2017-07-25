<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server;

use Enm\Bundle\JsonApi\Server\HttpMessageFactory\HttpMessageFactory;
use Enm\JsonApi\Server\JsonApiServer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiServerDecorator
{
    /**
     * @var JsonApiServer
     */
    private $jsonApi;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $psr7Factory;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param JsonApiServer $jsonApi
     * @param bool $debug
     */
    public function __construct(JsonApiServer $jsonApi, bool $debug)
    {
        $this->jsonApi = $jsonApi;
        $this->debug = $debug;
    }

    /**
     * @param HttpMessageFactoryInterface $psr7Factory
     *
     * @return void
     */
    public function setPsr7Factory(HttpMessageFactoryInterface $psr7Factory)
    {
        $this->psr7Factory = $psr7Factory;
    }

    /**
     * @param HttpFoundationFactoryInterface $httpFoundationFactory
     *
     * @return void
     */
    public function setHttpFoundationFactory(HttpFoundationFactoryInterface $httpFoundationFactory)
    {
        $this->httpFoundationFactory = $httpFoundationFactory;
    }

    /**
     * @param bool $debug
     *
     * @return void
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return HttpMessageFactoryInterface
     */
    protected function getPsr7Factory(): HttpMessageFactoryInterface
    {
        if (!$this->psr7Factory instanceof HttpMessageFactoryInterface) {
            $this->psr7Factory = new HttpMessageFactory();
        }
        return $this->psr7Factory;
    }

    /**
     * @return HttpFoundationFactoryInterface
     */
    protected function getHttpFoundationFactory(): HttpFoundationFactoryInterface
    {
        if (!$this->httpFoundationFactory instanceof HttpFoundationFactoryInterface) {
            $this->httpFoundationFactory = new HttpFoundationFactory();
        }

        return $this->httpFoundationFactory;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleHttpRequest(Request $request): Response
    {
        return $this->getHttpFoundationFactory()->createResponse(
            $this->jsonApi->handleHttpRequest(
                $this->getPsr7Factory()->createRequest($request),
                $this->debug
            )
        );
    }

    /**
     * @param \Throwable $throwable
     * @return Response
     */
    public function handleException(\Throwable $throwable): Response
    {
        return $this->getHttpFoundationFactory()->createResponse(
            $this->jsonApi->handleException($throwable, $this->debug)
        );
    }
}
