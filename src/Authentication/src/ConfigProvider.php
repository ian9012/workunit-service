<?php

declare(strict_types=1);

namespace Authentication;

use Authentication\Factory\JwtAuthenticationFactory;
use Tuupola\Middleware\JwtAuthentication;
use Authentication\Middleware\WorkunitAuthenticationMiddleware;
use Authentication\Middleware\WorkunitAuthenticationMiddlewareFactory;

/**
 * The configuration provider for the Authentication module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
                JwtAuthentication::class => JwtAuthenticationFactory::class,
                WorkunitAuthenticationMiddleware::class => WorkunitAuthenticationMiddlewareFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'authentication'    => [__DIR__ . '/../templates/'],
            ],
        ];
    }
}
