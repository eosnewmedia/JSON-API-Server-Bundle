<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ExtensionTest extends TestCase
{
    public function testExtension()
    {
        $extension = new EnmJsonApiServerExtension();
        $builder = new ContainerBuilder();
        $builder->addDefinitions(
            [
                'kernel' => new Definition(
                    TestAppKernel::class, ['dev', true]
                ),
                'event_dispatcher' => new Definition(
                    EventDispatcher::class
                ),
            ]
        );

        $extension->load([], $builder);

        self::assertTrue($builder->has('enm.json_api.resource_provider'));
        self::assertTrue($builder->has('enm.json_api'));
        self::assertTrue($builder->has('enm.json_api.exception_listener'));
    }

    public function testExtensionWithoutOptionalArguments()
    {
        $extension = new EnmJsonApiServerExtension();
        $builder = new ContainerBuilder();
        $builder->addDefinitions(
            [
                'kernel' => new Definition(
                    TestAppKernel::class, ['dev', true]
                )
            ]
        );

        $extension->load([], $builder);

        self::assertTrue($builder->has('enm.json_api.resource_provider'));
        self::assertTrue($builder->has('enm.json_api'));
        self::assertTrue($builder->has('enm.json_api.exception_listener'));
    }
}
