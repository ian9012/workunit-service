<?php

namespace Workunit\Presenter;

use Psr\Http\Message\ResponseInterface;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\HalResource;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\Link;
use Zend\Expressive\Hal\LinkGenerator;
use Zend\Diactoros\ServerRequest;

class CreateWorkunitPresenterTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
    }

    /**
     * @test
     * @dataProvider validCreateResponse
     * @group iCanPresentHalResponseGivenWorkUnitObj
     */
    public function iCanPresentHalResponseGivenWorkUnitObj(Workunit $workunit)
    {
        $request = (new ServerRequest())->withParsedBody([
            'idAccount' => $workunit->getIdAccount(),
            'title' => $workunit->getTitle()
        ]);
        $resourceGenerator = $this->prophesize(ResourceGenerator::class);
        $responseFactory = $this->prophesize(HalResponseFactory::class);
        $linkGenerator = $this->prophesize(LinkGenerator::class);
        $linkGenerator
            ->fromRoute('create-timetrack', $request, 'timetrack.create', ['id' => $workunit->getId()])
            ->willReturn(new Link('create-timetrack', '/api/workunit/'.$workunit->getId().'/timetracking'));
        $resource = $this->getHalResource($workunit);
        $resourceGenerator->fromObject($workunit, $request)->willReturn($resource);
        $resourceGenerator->getLinkGenerator()->willReturn($linkGenerator->reveal());
        $resource = $resource
            ->withLink(new Link('create-timetrack', '/api/workunit/' . $workunit->getId() . '/timetracking'));
        $responseFactory->createResponse($request, $resource)
            ->willReturn(new \Zend\Diactoros\Response\JsonResponse($resource));
        $presenter = new WorkunitPresenter($resourceGenerator->reveal(), $responseFactory->reveal());
        $response = $presenter->present($workunit, $request);
        $this->assertTrue($response instanceof ResponseInterface);
    }

    public function validCreateResponse()
    {
        $idWU = rand(1, 9999);
        $workunit = new Workunit();
        $workunit->setId($idWU);
        $workunit->setIdAccount(1);
        $workunit->setTitle('Example Workunit #'.$idWU);
        return [
            [$workunit]
        ];
    }

    /**
     * @param Workunit $workunit
     * @return HalResource
     */
    private function getHalResource(Workunit $workunit): HalResource
    {
        $hydrator = new \Zend\Hydrator\ReflectionHydrator();
        $links = [
            new Link('self', '/api/workunit/' . $workunit->getId())
        ];
        $resource = new \Zend\Expressive\Hal\HalResource($hydrator->extract($workunit), $links);
        return $resource;
    }
}
