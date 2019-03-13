<?php

declare(strict_types=1);

namespace Workunit\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Workunit\Presenter\WorkunitPresenter;
use Workunit\Service\WorkunitService;
use Zend\Diactoros\Response\JsonResponse;

class GetWorkunitAction implements RequestHandlerInterface
{
    /**
     * @var WorkunitService
     */
    private $service;
    /**
     * @var WorkunitPresenter
     */
    private $presenter;
    public function __construct(WorkunitService $service, WorkunitPresenter $presenter)
    {
        $this->service = $service;
        $this->presenter = $presenter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $id = $request->getAttribute('id');

            if (empty($id) || ($id && !filter_var($id, FILTER_VALIDATE_INT))) {
                throw new \Exception('Invalid id. Only valid integer is allowed');
            }
            $workunit = $this->service->get($id);
            return $this->presenter->present($workunit, $request);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }
}
