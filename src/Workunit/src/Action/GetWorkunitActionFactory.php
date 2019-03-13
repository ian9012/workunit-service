<?php

declare(strict_types=1);

namespace Workunit\Action;

use Psr\Container\ContainerInterface;
use Workunit\Presenter\WorkunitPresenter;
use Workunit\Service\WorkunitService;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class GetWorkunitActionFactory
{
    public function __invoke(ContainerInterface $container) : GetWorkunitAction
    {
        $presenter = new WorkunitPresenter(
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class)
        );
        return new GetWorkunitAction(new WorkunitService(), $presenter);
    }
}
