<?php

declare(strict_types=1);

namespace Ping\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class PingAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return new JsonResponse('Ping successfully at '. date('d-m-y'), 200);
    }
    
    public function anotherhandle(ServerRequestInterface $request) : ResponseInterface
    {
        return new JsonResponse('Ping 2 successfully at '. date('d-m-y'), 200);
    }
}
