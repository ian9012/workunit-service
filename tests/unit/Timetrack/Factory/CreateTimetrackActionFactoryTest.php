<?php

use Timetrack\Action\CreateTimetrackAction;
use Timetrack\Action\CreateTimetrackActionFactory;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class CreateTimetrackActionFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function weCanCreateTimetrackActionFactory()
    {
        $factory = new CreateTimetrackActionFactory();
        $container = $this->prophesize(\Psr\Container\ContainerInterface::class);
        $container->get('config')
            ->willReturn([
                'timetrack_validator' => []
            ]);
        $container->get(HalResponseFactory::class)
            ->willReturn($this->prophesize(HalResponseFactory::class));
        $container->get(ResourceGenerator::class)
            ->willReturn($this->prophesize(ResourceGenerator::class));
        $response = $factory($container->reveal());
        $this->assertTrue($response instanceof CreateTimetrackAction);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Config instance of timetrack_validator needed
     */
    public function weCannotCreateTimetrackActionFactoryIfWeInjectEmptyConfigInValidator()
    {
        $factory = new CreateTimetrackActionFactory();
        $container = $this->prophesize(\Psr\Container\ContainerInterface::class);
        $container->get('config')->willReturn([]);
        $container->get(HalResponseFactory::class)
            ->willReturn($this->prophesize(HalResponseFactory::class));
        $container->get(ResourceGenerator::class)
            ->willReturn($this->prophesize(ResourceGenerator::class));
        $factory($container->reveal());
    }
}
