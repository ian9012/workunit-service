<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
use Tuupola\Middleware\JwtAuthentication;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/api/ping', \Ping\Action\PingAction::class, 'ping');
    $app->post('/api/workunit', [
        JwtAuthentication::class,
        \Workunit\Action\CreateWorkunitAction::class
    ], 'workunit.create');
    $app->get('/api/workunit/{id:[0-9]*}', [
        JwtAuthentication::class,
        \Workunit\Action\GetWorkunitAction::class],
        'workunit.get');
    $app->put('/api/workunit/{id:[0-9]*}', [
        JwtAuthentication::class,
        \Ping\Action\PingAction::class],
        'workunit.update');
    $app->post('/api/workunit/{id:[0-9]*}/timetrack', [
        JwtAuthentication::class,
        \Authentication\Middleware\WorkunitAuthenticationMiddleware::class,
        \Timetrack\Action\CreateTimetrackAction::class
    ], 'timetrack.create');
    $app->get('/api/timetrack/{id:[0-9]*}', [
        JwtAuthentication::class,
        \Ping\Action\PingAction::class],
        'timetrack.get');
    //mock
};
