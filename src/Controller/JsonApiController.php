<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Controller;

use Enm\Bundle\JsonApi\Server\JsonApiServerDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiController
{
    /**
     * @var JsonApiServerDecorator
     */
    private $jsonApi;

    /**
     * @param JsonApiServerDecorator $jsonApi
     */
    public function __construct(JsonApiServerDecorator $jsonApi)
    {
        $this->jsonApi = $jsonApi;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function jsonApiAction(Request $request): Response
    {
        return $this->jsonApi->handleHttpRequest($request);
    }
}
