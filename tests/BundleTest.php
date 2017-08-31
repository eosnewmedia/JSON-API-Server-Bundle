<?php
declare(strict_types=1);

namespace Enm\Bundle\JsonApi\Server\Tests;

use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\RequestHandlerPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\Compiler\ResourceProviderPass;
use Enm\Bundle\JsonApi\Server\DependencyInjection\EnmJsonApiServerExtension;
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

        (new EnmJsonApiServerExtension())->load(
            [
                'enm_json_api_server' => []
            ],
            $builder
        );

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
        self::assertTrue($builder->hasDefinition('enm.json_api_server.pagination.offset_based'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function testEnmJsonApiBundleInvalidPaginationLimitWihString()
    {
        $builder = new ContainerBuilder();
        (new EnmJsonApiServerExtension())->load(
            [
                'enm_json_api_server' => [
                    'pagination' => [
                        'limit' => 'invalid'
                    ]
                ]
            ],
            $builder
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEnmJsonApiBundleInvalidPaginationLimitWihZero()
    {
        $builder = new ContainerBuilder();
        (new EnmJsonApiServerExtension())->load(
            [
                'enm_json_api_server' => [
                    'pagination' => [
                        'limit' => 0
                    ]
                ]
            ],
            $builder
        );
    }
}
