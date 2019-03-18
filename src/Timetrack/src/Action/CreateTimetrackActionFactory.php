<?php

declare(strict_types=1);

namespace Timetrack\Action;

use Helper\HalPresenter;
use Psr\Container\ContainerInterface;
use Timetrack\Service\TimetrackService;
use Timetrack\Validator\TimetrackValidator;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class CreateTimetrackActionFactory
{
    public function __invoke(ContainerInterface $container) : CreateTimetrackAction
    {
        try {
            $presenter = new HalPresenter(
                $container->get(ResourceGenerator::class),
                $container->get(HalResponseFactory::class)
            );
            $validator = new TimetrackValidator($container->get('config'));
            return new CreateTimetrackAction(new TimetrackService($validator), $presenter);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
