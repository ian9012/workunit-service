<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
use Tuupola\Middleware\JwtAuthentication;
use Workunit\Action\CreateWorkunitAction;
use Ping\Action\PingAction;
use Workunit\Action\GetWorkunitAction;
use Authentication\Middleware\WorkunitAuthenticationMiddleware;
use Timetrack\Action\CreateTimetrackAction;
use Workunit\Middleware\WorkunitLinkFilterMiddleware;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/api/ping', PingAction::class, 'ping');
    $app->post('/api/workunit', [
        JwtAuthentication::class,
        CreateWorkunitAction::class
    ], 'workunit.create');
    $app->get('/api/workunit/{id:[0-9]*}', [
        JwtAuthentication::class,
        WorkunitLinkFilterMiddleware::class,
        GetWorkunitAction::class],
        'workunit.get');
    $app->post('/api/workunit/{id:[0-9]*}/timetrack', [
        JwtAuthentication::class,
        WorkunitAuthenticationMiddleware::class,
        CreateTimetrackAction::class
    ], 'timetrack.create');
    $app->get('/api/timetrack/{id:[0-9]*}', [
        JwtAuthentication::class,
        PingAction::class],
        'timetrack.get');

    //mock
    $app->put('/api/workunit/{id:[0-9]*}', [
        JwtAuthentication::class,
        PingAction::class],
        'workunit.update');
};
