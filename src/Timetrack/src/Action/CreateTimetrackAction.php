<?php

declare(strict_types=1);

namespace Timetrack\Action;

use Helper\HalPresenter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Timetrack\Entity\Timetrack;
use Timetrack\Service\TimetrackService;
use Zend\Diactoros\Response\JsonResponse;

class CreateTimetrackAction implements RequestHandlerInterface
{
    /**
     * @var TimetrackService
     */
    private $service;
    /**
     * @var HalPresenter
     */
    private $presenter;

    public function __construct(TimetrackService $service, HalPresenter $presenter)
    {
        $this->service = $service;
        $this->presenter = $presenter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $timetrack = $this->service->create($this->setToObject($request->getParsedBody()));
            return $this->presenter->present($timetrack, $request);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param array $request
     * @return Timetrack
     */
    private function setToObject(array $request): Timetrack
    {
        $timetrack = new Timetrack();
        $timetrack->setIdAccount($request['idAccount'] ?? null);
        $timetrack->setIdWorkunit($request['idWorkUnit'] ?? null);
        $timetrack->setDuration($request['duration'] ?? null);
        $timetrack->setDescription($request['description'] ?? null);
        $timetrack->setDate($request['date'] ?? null);
        return $timetrack;
    }
}
