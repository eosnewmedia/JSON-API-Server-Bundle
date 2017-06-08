<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\ResourceProviderPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
use Enm\JsonApi\Server\Provider\ResourceProviderInterface;
use Enm\JsonApi\Server\Provider\ResourceProviderRegistry;
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
        $testDefinition->addTag('json_api.resource_provider', ['type' => 'tests']);
        $builder->setDefinition(
            'enm.json_api.resource_provider.test',
            $testDefinition
        );
    }

    public function testResourceCompilerPass()
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

        $pass = new ResourceProviderPass();
        $pass->process($builder);

        /** @var ResourceProviderRegistry $registry */
        $registry = $builder->get('enm.json_api.resource_provider');

        self::assertInstanceOf(ResourceProviderInterface::class, $registry->provider('tests'));
    }

    public function testResourceCompilerPassIgnoreTagsOnMissingRegistry()
    {
        $pass = new ResourceProviderPass();
        $builder = new ContainerBuilder();
        $this->addTestProvider($builder);
        $pass->process($builder);
        self::assertTrue(true);
    }

    public function testResourceCompilerPassWithContainerContext()
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

        $builder->addCompilerPass(new ResourceProviderPass());
        $builder->compile();

        /** @var ResourceProviderRegistry $registry */
        $registry = $builder->get('enm.json_api.resource_provider');

        self::assertInstanceOf(ResourceProviderInterface::class, $registry->provider('tests'));
    }
}
