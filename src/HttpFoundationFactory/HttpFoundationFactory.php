<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\HttpFoundationFactory;

use Enm\Bundle\JsonApi\Server\Response\JsonApiResponse;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class HttpFoundationFactory extends \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory
{
    /**
     * Creates a Symfony Response instance from a PSR-7 one.
     *
     * @param ResponseInterface $psrResponse
     *
     * @return Response
     * @throws \Exception
     */
    public function createResponse(ResponseInterface $psrResponse): Response
    {
        $response = new JsonApiResponse(
            $psrResponse->getBody()->__toString(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );
        $response->setProtocolVersion($psrResponse->getProtocolVersion());

        return $response;
    }
}
