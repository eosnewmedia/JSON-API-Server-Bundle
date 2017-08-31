<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests\HttpMessageFactory;

use Enm\Bundle\JsonApi\Server\HttpMessageFactory\HttpMessageFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class HttpMessageFactoryTest extends TestCase
{

    public function testCreateRequest()
    {
        $factory = new HttpMessageFactory();
        self::assertInstanceOf(
            RequestInterface::class,
            $factory->createRequest(Request::create('http://example.com'))
        );
    }

    public function testCreateResponse()
    {
        $factory = new HttpMessageFactory();
        self::assertInstanceOf(
            ResponseInterface::class,
            $factory->createResponse(new Response())
        );
    }
}
