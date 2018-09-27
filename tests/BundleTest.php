<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\RequestHandlerPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
use Enm\Bundle\JsonApi\Server\EnmJsonApiServerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class BundleTest extends TestCase
{
    public function testEnmJsonApiBundle(): void
    {
        $builder = new ContainerBuilder();
        $builder->setParameter('kernel.environment', 'dev');
        $bundle = new EnmJsonApiServerBundle();
        $bundle->build($builder);

        (new EnmJsonApiServerExtension())->load(
            [
                'enm_json_api_server' => []
            ],
            $builder
        );

        $containsRequestHandlerPass = false;

        $passes = $builder->getCompiler()->getPassConfig()->getPasses();
        foreach ($passes as $pass) {
            if ($pass instanceof RequestHandlerPass) {
                $containsRequestHandlerPass = true;
            }
        }

        self::assertTrue($containsRequestHandlerPass);
    }
}
