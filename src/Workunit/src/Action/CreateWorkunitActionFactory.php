<?php

namespace Workunit\Action;

use Interop\Container\ContainerInterface;
use Workunit\Presenter\WorkunitPresenter;
use Workunit\Service\WorkunitService;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\ServiceManager\Factory\FactoryInterface;

class CreateWorkunitActionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $presenter = new WorkunitPresenter(
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class)
        );
        return new CreateWorkunitAction(new WorkunitService(), $presenter);
    }
}
