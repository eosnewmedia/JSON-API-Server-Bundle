<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\RequestHandlerPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\ResourceProviderPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
use Enm\JsonApi\Exception\UnsupportedTypeException;
use Enm\JsonApi\Model\Document\DocumentInterface;
use Enm\JsonApi\Server\Model\Request\FetchRequestInterface;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerChain;
use Enm\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Enm\JsonApi\Server\RequestHandler\ResourceProviderRequestHandler;
use Enm\JsonApi\Server\ResourceProvider\ResourceProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class CompilerPassTest extends TestCase
{
    /**
     * @param ContainerBuilder $builder
     */
    private function addTestProvider(ContainerBuilder $builder)
    {
        $testDefinition = new Definition($this->createMock(ResourceProviderInterface::class));
        $testDefinition->addTag('json_api_server.resource_provider', ['type' => 'providerTests']);
        $builder->setDefinition('app.resource_provider.test', $testDefinition);
    }

    /**
     * @param ContainerBuilder $builder
     */
    private function addTestHandler(ContainerBuilder $builder)
    {
        $testDefinition = new Definition($this->createMock(RequestHandlerInterface::class));
        $testDefinition->addTag('json_api_server.request_handler', ['type' => 'handlerTests']);
        $builder->setDefinition('app.request_handler.test', $testDefinition);
    }


    public function testCompilerPass()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(
            [
                'kernel' => new Definition(
                    TestAppKernel::class, ['dev', true]
                )
            ]
        );
        $extension = new EnmJsonApiServerExtension();
        $extension->load([], $builder);

        $this->addTestProvider($builder);
        $this->addTestHandler($builder);

        $resourceProviderPass = new ResourceProviderPass();
        $resourceProviderPass->process($builder);

        $requestHandlerPass = new RequestHandlerPass();
        $requestHandlerPass->process($builder);

        // initialize json api service to configure request handlers (json api aware)
        $builder->get('enm.json_api_server.server');

        $chain = $builder->get('enm.json_api_server.request_handler.chain');

        /** @var FetchRequestInterface $handlerTestRequest */
        $handlerTestRequest = $this->createConfiguredMock(
            FetchRequestInterface::class, [
                'type' => 'handlerTests'
            ]
        );
        self::assertInstanceOf(DocumentInterface::class, $chain->fetchResource($handlerTestRequest));

        /** @var FetchRequestInterface $providerTestRequest */
        $providerTestRequest = $this->createConfiguredMock(
            FetchRequestInterface::class, [
                'type' => 'providerTests'
            ]
        );
        self::assertInstanceOf(DocumentInterface::class, $chain->fetchResource($providerTestRequest));

        try {
            /** @var FetchRequestInterface $invalidTypeRequest */
            $invalidTypeRequest = $this->createConfiguredMock(
                FetchRequestInterface::class, [
                    'type' => 'tests'
                ]
            );
            $chain->fetchResource($invalidTypeRequest);
            self::fail('Invalid type caused no exception!');
        } catch (UnsupportedTypeException $e) {
            self::assertTrue(true);
        }
    }

    public function testCompilerPassIgnoreTagsOnMissingResourceProviderHandler()
    {
        $pass = new ResourceProviderPass();
        $builder = new ContainerBuilder();
        $this->addTestProvider($builder);
        $pass->process($builder);

        self::assertTrue(true);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidDefinitionException
     */
    public function testCompilerPassInvalidResourceProviderType()
    {
        $pass = new ResourceProviderPass();
        $builder = new ContainerBuilder();
        $builder->setDefinition(
            'enm.json_api_server.request_handler.resource_provider',
            new Definition(ResourceProviderRequestHandler::class)
        );

        $testDefinition = new Definition($this->createMock(ResourceProviderInterface::class));
        $testDefinition->addTag('json_api_server.resource_provider');
        $builder->setDefinition('app.resource_provider.test', $testDefinition);

        $pass->process($builder);
    }

    public function testCompilerPassIgnoreTagsOnMissingRequestHandlerRegistry()
    {
        $pass = new RequestHandlerPass();
        $builder = new ContainerBuilder();
        $builder->setDefinition(
            'enm.json_api_server.request_handler.chain',
            new Definition(RequestHandlerChain::class)
        );

        $this->addTestHandler($builder);
        $pass->process($builder);

        self::assertTrue(true);
    }

    public function testCompilerPassIgnoreTagsOnMissingRequestHandlerChain()
    {
        $pass = new RequestHandlerPass();
        $builder = new ContainerBuilder();
        $builder->setDefinition(
            'enm.json_api_server.request_handler.registry',
            new Definition(RequestHandlerChain::class)
        );

        $this->addTestHandler($builder);
        $pass->process($builder);

        self::assertTrue(true);
    }
}
