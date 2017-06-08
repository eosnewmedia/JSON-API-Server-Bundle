<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Controller;

use Enm\JsonApi\Server\JsonApi;
use Enm\JsonApi\Server\Model\Request\FetchRequest;
use Enm\JsonApi\Server\Model\Request\SaveResourceRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiController extends Controller
{
    /**
     * @param Request $request
     * @param string $type
     *
     * @return Response
     * @throws \Exception
     */
    public function fetchResourceCollectionAction(Request $request, string $type): Response
    {
        return $this->getJsonApi()
            ->fetchResources($type, new FetchRequest($request));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     *
     * @return Response
     * @throws \Exception
     */
    public function fetchResourceAction(Request $request, string $type, string $id): Response
    {
        return $this->getJsonApi()
            ->fetchResource($type, $id, new FetchRequest($request));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     * @param string $relationship
     *
     * @return Response
     * @throws \Exception
     */
    public function fetchRelationshipAction(Request $request, string $type, string $id, string $relationship): Response
    {
        return $this->getJsonApi()
            ->fetchRelationship(
                $type,
                $id,
                new FetchRequest($request),
                $relationship
            );
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     * @param string $relationship
     *
     * @return Response
     * @throws \Exception
     */
    public function fetchRelatedAction(Request $request, string $type, string $id, string $relationship): Response
    {
        return $this->getJsonApi()
            ->fetchRelated(
                $type,
                $id,
                new FetchRequest($request),
                $relationship
            );
    }

    /**
     * @param Request $request
     * @param string $type
     *
     * @return Response
     * @throws \Exception
     */
    public function createResourceAction(Request $request, string $type): Response
    {
        return $this->getJsonApi()
            ->createResource(
                $type,
                new SaveResourceRequest($request)
            );
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     *
     * @return Response
     * @throws \Exception
     */
    public function patchResourceAction(Request $request, string $type, string $id): Response
    {
        return $this->getJsonApi()
            ->patchResource(
                $type,
                $id,
                new SaveResourceRequest($request)
            );
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     *
     * @return Response
     * @throws \Exception
     */
    public function deleteResourceAction(Request $request, string $type, string $id): Response
    {
        return $this->getJsonApi()
            ->deleteResource(
                $type,
                $id,
                $request
            );
    }

    /**
     * @return JsonApi
     */
    protected final function getJsonApi(): JsonApi
    {
        return $this->get('enm.json_api');
    }
}
