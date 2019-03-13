<?php namespace Workunit\Factory;

use Interop\Container\ContainerInterface;
use Workunit\Action\CreateWorkunitAction;
use Workunit\Action\CreateWorkunitActionFactory;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class CreateWorkunitActionFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function _before()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get(HalResponseFactory::class)
            ->willReturn($this->prophesize(HalResponseFactory::class));
        $this->container->get(ResourceGenerator::class)
            ->willReturn($this->prophesize(ResourceGenerator::class));
    }

    /**
     * @test
     */
    public function iCanCreateCreateWorkunitActionThroughFactory()
    {
        $factory = new CreateWorkunitActionFactory();
        $response = $factory($this->container->reveal(), CreateWorkunitActionFactory::class);
        $this->assertTrue($response instanceof CreateWorkunitAction);
    }
}
