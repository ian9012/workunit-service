<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/api/ping', \Ping\Action\PingAction::class, 'ping');
    $app->post('/api/workunit', [
        \Middlewares\HttpAuthentication::class,
        \Workunit\Action\CreateWorkunitAction::class
    ], 'workunit.create');
    //mock
    $app->get('/api/workunit/{id:[0-9]*}', \Ping\Action\PingAction::class, 'workunit.get');
    $app->post('/api/workunit/{id:[0-9]*}/timetracking', \Ping\Action\PingAction::class, 'timetrack.create');
};
