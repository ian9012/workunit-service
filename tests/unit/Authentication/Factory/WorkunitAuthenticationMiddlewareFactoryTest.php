<?php

use Authentication\Factory\WorkunitAuthenticationMiddlewareFactory;
use Authentication\Middleware\WorkunitAuthenticationMiddleware;
use Psr\Container\ContainerInterface;

class WorkunitAuthenticationMiddlewareFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @group iCanGenerateInstanceOfWorkunitAuthenticationMiddleware
     */
    public function iCanGenerateInstanceOfWorkunitAuthenticationMiddleware()
    {
        $factory = new WorkunitAuthenticationMiddlewareFactory();
        $container = $this->prophesize(ContainerInterface::class);
        $response = $factory($container->reveal());
        $this->assertTrue($response instanceof WorkunitAuthenticationMiddleware);
    }
}
