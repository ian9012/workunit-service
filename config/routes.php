<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;
use Middlewares\HttpAuthentication;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/api/ping', \Ping\Action\PingAction::class, 'ping');
    $app->post('/api/workunit', [
        HttpAuthentication::class,
        \Workunit\Action\CreateWorkunitAction::class
    ], 'workunit.create');
    //mock

    $app->get('/api/workunit/{id:[0-9]*}/timetrack/{id:[0-9]*}', [
        HttpAuthentication::class,
        \Ping\Action\PingAction::class],
        'timetrack.get');
    $app->get('/api/workunit/{id:[0-9]*}', [
        HttpAuthentication::class,
        \Workunit\Action\GetWorkunitAction::class],
        'workunit.get');
    $app->post('/api/workunit/{id:[0-9]*}/timetrack', [
        HttpAuthentication::class,
        \Timetrack\Action\CreateTimetrackAction::class
    ], 'timetrack.create');
};
