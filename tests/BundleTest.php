<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\RequestHandlerPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\ResourceProviderPass;
use Enm\Bundle\JsonApi\Server\EnmJsonApiServerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class BundleTest extends TestCase
{
    public function testEnmJsonApiBundle()
    {
        $builder = new ContainerBuilder();
        $bundle = new EnmJsonApiServerBundle();
        $bundle->build($builder);

        $containsResourceProviderPass = false;
        $containsRequestHandlerPass = false;

        $passes = $builder->getCompiler()->getPassConfig()->getPasses();
        foreach ($passes as $pass) {
            if ($pass instanceof ResourceProviderPass) {
                $containsResourceProviderPass = true;
            }

            if ($pass instanceof RequestHandlerPass) {
                $containsRequestHandlerPass = true;
            }
        }

        self::assertTrue($containsResourceProviderPass);
        self::assertTrue($containsRequestHandlerPass);
    }
}
