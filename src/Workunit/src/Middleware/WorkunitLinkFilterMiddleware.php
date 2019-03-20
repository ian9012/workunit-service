<?php

declare(strict_types=1);

namespace Workunit\Middleware;

use Helper\ResponseExtractor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class WorkunitLinkFilterMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $jwt = $request->getParsedBody()['jwt']['data'];
        $response = $handler->handle($request);
        if ($response->getStatusCode() === 200) {
            return $this->modifyResponse($response, $jwt);
        }
        return $response;
    }

    private function modifyResponse(ResponseInterface $response, $jwt): ResponseInterface
    {
        $contents = ResponseExtractor::toArray($response);
        if ($jwt->id !== $contents['idAccount']) {
            unset($contents['_links']['update-workunit']);
        }

        return new JsonResponse($contents);
    }
}
