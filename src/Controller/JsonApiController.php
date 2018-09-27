<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Controller;

use Enm\Bundle\JsonApi\Server\Response\JsonApiResponse;
use Enm\JsonApi\Model\Request\Request as JsonApiRequest;
use Enm\JsonApi\Server\JsonApiServer;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiController
{
    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @var JsonApiServer
     */
    private $jsonApi;

    /**
     * @param null|string $prefix
     * @param JsonApiServer $jsonApi
     */
    public function __construct(?string $prefix, JsonApiServer $jsonApi)
    {
        $this->prefix = $prefix;
        $this->jsonApi = $jsonApi;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        $apiRequest = new JsonApiRequest(
            $request->getMethod(),
            new Uri($request->getUri()),
            $this->jsonApi->createRequestBody((string)$request->getContent()),
            $this->prefix
        );
        foreach ($request->headers->all() as $key => $value) {
            $apiRequest->headers()->set($key, $value);
        }

        $response = $this->jsonApi->handleRequest($apiRequest);

        return new JsonApiResponse(
            $this->jsonApi->createResponseBody($response),
            $response->status(),
            $response->headers()->all()
        );
    }
}
