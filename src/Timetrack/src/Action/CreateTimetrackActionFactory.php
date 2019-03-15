<?php

declare(strict_types=1);

namespace Timetrack\Action;

use Psr\Container\ContainerInterface;

class CreateTimetrackActionFactory
{
    public function __invoke(ContainerInterface $container) : CreateTimetrackAction
    {
        return new CreateTimetrackAction();
    }
}
