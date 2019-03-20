<?php

declare(strict_types=1);

namespace Authentication\Factory;

use Psr\Container\ContainerInterface;
use Tuupola\Middleware\JwtAuthentication;

class JwtAuthenticationFactory
{
    public function __invoke(ContainerInterface $container) : JwtAuthentication
    {
        return new JwtAuthentication($container->get('config')['jwt_auth']);
    }
}
