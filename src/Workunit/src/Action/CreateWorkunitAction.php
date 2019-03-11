<?php

declare(strict_types=1);

namespace Workunit\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Workunit\Entity\Workunit;
use Workunit\Service\WorkunitService;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Hydrator\ReflectionHydrator;

class CreateWorkunitAction implements RequestHandlerInterface
{
    /**
     * @var WorkunitService|null
     */
    private $service = null;
    /**
     * @var ReflectionHydrator|null
     */
    private $hydrator = null;

    public function __construct(WorkunitService $workunitService)
    {
        $this->service = $workunitService;
        $this->hydrator = new ReflectionHydrator();
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $request = $request->getParsedBody();

            $idAccount = $request['idAccount'] ?? null;
            $title = $request['title'] ?? null;

            $idWU = $this->service->create($idAccount, $title);
            $workunit = $this->service->get($idWU);

            $response = $this->generateResponse($workunit);
            return new JsonResponse($response);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param Workunit $workunit
     * @return array
     */
    private function generateResponse(Workunit $workunit): array
    {
        $workunitArr = $this->hydrator->extract($workunit);
        return array_merge(['_links' => '/workunit/'.$workunit->getId().'/timetracking'  ], $workunitArr);
    }
}
