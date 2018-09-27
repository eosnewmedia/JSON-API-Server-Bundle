<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class JsonApiResponse extends Response
{
    /**
     * Prepares the Response before it is sent to the client.
     *
     * This method tweaks the Response to ensure that it is
     * compliant with RFC 2616. Most of the changes are based on
     * the Request that is "associated" with this Response.
     *
     * @param Request $request A Request instance
     *
     * @return $this
     */
    public function prepare(Request $request): self
    {
        parent::prepare($request);
        // fix the content type for json api standard
        $this->headers->set('Content-Type', 'application/vnd.api+json');

        return $this;
    }
}
