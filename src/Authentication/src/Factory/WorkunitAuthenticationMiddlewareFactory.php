<?php

declare(strict_types=1);

namespace Authentication\Factory;

use Authentication\Middleware\WorkunitAuthenticationMiddleware;
use Psr\Container\ContainerInterface;
use Workunit\Service\WorkunitService;

class WorkunitAuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : WorkunitAuthenticationMiddleware
    {
        return new WorkunitAuthenticationMiddleware(new WorkunitService());
    }
}
