<?php

declare(strict_types=1);

namespace Workunit\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Workunit\Presenter\CreateWorkunitPresenter;
use Workunit\Service\WorkunitService;
use Zend\Diactoros\Response\JsonResponse;

class CreateWorkunitAction implements RequestHandlerInterface
{
    /**
     * @var WorkunitService|null
     */
    private $service = null;
    /**
     * @var CreateWorkunitPresenter
     */
    private $presenter = null;


    public function __construct(WorkunitService $workunitService, CreateWorkunitPresenter $presenter)
    {
        $this->service = $workunitService;
        $this->presenter = $presenter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $requestBody = $request->getParsedBody();

            $idAccount = $requestBody['idAccount'] ?? null;
            $title = $requestBody['title'] ?? null;

            $idWU = $this->service->create($idAccount, $title);
            $workunit = $this->service->get($idWU);
            return $this->presenter->present($workunit, $request);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }
}
