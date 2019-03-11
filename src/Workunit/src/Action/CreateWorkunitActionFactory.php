<?php

namespace Workunit\Action;

use Interop\Container\ContainerInterface;
use Workunit\Service\WorkunitService;
use Zend\ServiceManager\Factory\FactoryInterface;

class CreateWorkunitActionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new CreateWorkunitAction(new WorkunitService());
    }
}
