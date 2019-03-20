<?php

use Psr\Http\Message\ResponseInterface;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\HalResource;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\Link;
use Zend\Expressive\Hal\LinkGenerator;
use Zend\Diactoros\ServerRequest;
use Zend\Hydrator\ReflectionHydrator;
use Workunit\Presenter\WorkunitPresenter;
use Zend\Diactoros\Response\JsonResponse;
use Prophecy\Prophecy\ObjectProphecy;

class CreateWorkunitPresenterTest extends \Codeception\Test\Unit
{
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

        $linkGenerator = $this->generateMockLinkGenerator($workunit, $request);

        $resource = $this->getHalResource($workunit);
        $resourceGenerator->fromObject($workunit, $request)->willReturn($resource);
        $resourceGenerator->getLinkGenerator()->willReturn($linkGenerator->reveal());
        $resource = $resource
            ->withLink(new Link('create-timetrack', '/api/workunit/' . $workunit->getId() . '/timetracking'));
        $resource = $resource
            ->withLink(new Link('update-workunit', '/api/workunit/' . $workunit->getId()));
        $responseFactory->createResponse($request, $resource)
            ->willReturn(new JsonResponse($resource));
        $presenter = new WorkunitPresenter($resourceGenerator->reveal(), $responseFactory->reveal());
        $response = $presenter->present($workunit, $request);
        $this->assertTrue($response instanceof ResponseInterface);
        $response = json_decode($response->getBody()->getContents(), true);
        $expectedResponse = new JsonResponse($resource);
        $expectedResponse = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertEquals($expectedResponse, $response);
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
        $hydrator = new ReflectionHydrator();
        $links = [
            new Link('self', '/api/workunit/' . $workunit->getId())
        ];
        $resource = new HalResource($hydrator->extract($workunit), $links);
        return $resource;
    }

    /**
     * @param Workunit $workunit
     * @param ServerRequest $request
     * @return ObjectProphecy
     */
    private function generateMockLinkGenerator(Workunit $workunit, ServerRequest $request): \Prophecy\Prophecy\ObjectProphecy
    {
        $linkGenerator = $this->prophesize(LinkGenerator::class);
        $linkGenerator
            ->fromRoute('create-timetrack', $request, 'timetrack.create', ['id' => $workunit->getId()])
            ->willReturn(new Link('create-timetrack', '/api/workunit/' . $workunit->getId() . '/timetracking'));
        $linkGenerator
            ->fromRoute('update-workunit', $request, 'workunit.update', ['id' => $workunit->getId()])
            ->willReturn(new Link('update-workunit', '/api/workunit/' . $workunit->getId()));
        return $linkGenerator;
    }
}
