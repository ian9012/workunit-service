<?php

declare(strict_types=1);

namespace Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Workunit\Service\WorkunitService;
use Zend\Diactoros\Response\JsonResponse;

class WorkunitAuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var WorkunitService
     */
    private $service;

    public function __construct(WorkunitService $service)
    {
        $this->service = $service;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $idWorkunit = $request->getAttribute('id');
            $idWorkunitRequest = $request->getParsedBody()['idWorkUnit'];

            if (($idWorkunitRequest !== $idWorkunit) || empty($idWorkunitRequest)) {
                return new JsonResponse('Id workunit attribute does not equal to id workunit request', 400);
            }

            if ($this->service->get($idWorkunit)) {
                return $handler->handle($request);
            }
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }
}
