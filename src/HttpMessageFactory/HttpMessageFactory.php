<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\HttpMessageFactory;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class HttpMessageFactory implements HttpMessageFactoryInterface
{
    /**
     * Creates a PSR-7 Request instance from a Symfony one.
     *
     * @param Request $symfonyRequest
     *
     * @return ServerRequestInterface
     * @throws \Exception
     */
    public function createRequest(Request $symfonyRequest): ServerRequestInterface
    {
        return new ServerRequest(
            $symfonyRequest->getMethod(),
            $symfonyRequest->getUri(),
            $symfonyRequest->headers->all(),
            $symfonyRequest->getContent(true),
            '1.1',
            $symfonyRequest->server->all()
        );
    }

    /**
     * Creates a PSR-7 Response instance from a Symfony one.
     *
     * @param Response $symfonyResponse
     *
     * @return ResponseInterface
     */
    public function createResponse(Response $symfonyResponse): ResponseInterface
    {
        return new \GuzzleHttp\Psr7\Response(
            $symfonyResponse->getStatusCode(),
            $symfonyResponse->headers->all(),
            $symfonyResponse->getContent()
        );
    }
}
