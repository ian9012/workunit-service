<?php

use Zend\Diactoros\Response\JsonResponse;
use Timetrack\Entity\Timetrack;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Hal\HalResource;
use Zend\Expressive\Hal\Link;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Helper\HalPresenter;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\LinkGenerator;

class HalPresenterTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @dataProvider provideTimetrack
     */
    public function iCanGenerateTimetrackHalResponse(Timetrack $timetrack)
    {
        $request = $this->getRequest($timetrack);
        $resourceGenerator = $this->prophesize(ResourceGenerator::class);
        $responseFactory = $this->prophesize(HalResponseFactory::class);
        $resource = $this->getHalResource($timetrack);
        $resourceGenerator->fromObject($timetrack, $request)->willReturn($resource);
        $responseFactory->createResponse($request, $resource)
            ->willReturn(new \Zend\Diactoros\Response\JsonResponse($resource));
        $presenter = new HalPresenter($resourceGenerator->reveal(), $responseFactory->reveal());
        $response = $presenter->present($timetrack, $request);
        $expectedResponse = new JsonResponse($resource);
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
        $this->assertEquals($expectedResponse->getBody()->getContents(), $response->getBody()->getContents());
    }
    /**
     * @test
     * @dataProvider provideWorkunit
     */
    public function iCanGenerateWorkunitHalResponseWithLinks(Workunit $workunit)
    {
        $request = $this->getRequestWorkunit($workunit);
        $resourceGenerator = $this->prophesize(ResourceGenerator::class);
        $responseFactory = $this->prophesize(HalResponseFactory::class);
        $linkGenerator = $this->prophesize(LinkGenerator::class);
        $linkGenerator
            ->fromRoute('create-timetrack', $request, 'timetrack.create', ['id' => $workunit->getId()])
            ->willReturn(new Link('create-timetrack', '/api/workunit/'.$workunit->getId().'/timetrack'));
        $resource = $this->getHalResource($workunit, [$this->getWorkunitLink($workunit)]);
        $resourceGenerator->fromObject($workunit, $request)->willReturn($resource);
        $responseFactory->createResponse($request, $resource)
            ->willReturn(new \Zend\Diactoros\Response\JsonResponse($resource));
        $presenter = new HalPresenter($resourceGenerator->reveal(), $responseFactory->reveal());
        $response = $presenter->present($workunit, $request);
        $expectedResponse = new JsonResponse($resource);
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
        $this->assertEquals($expectedResponse->getBody()->getContents(), $response->getBody()->getContents());
    }

    public function provideTimetrack()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h30m');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        return [
            [$timetrack]
        ];
    }

    public function provideWorkunit()
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
     * @return HalResource
     */
    private function getHalResource($object, $links = []): HalResource
    {
        $hydrator = new \Zend\Hydrator\ReflectionHydrator();
        return new HalResource($hydrator->extract($object), $links);
    }

    private function getRequest(Timetrack $timetrack)
    {
        return (new ServerRequest())->withParsedBody([
            'idUser' => $timetrack->getIdUser(),
            'idWorkUnit' => $timetrack->getIdWorkunit(),
            'duration' => $timetrack->getDuration(),
            'description' => $timetrack->getDescription(),
            'date' => $timetrack->getDate(),
        ]);
    }

    private function getRequestWorkunit(Workunit $workunit)
    {
        return (new ServerRequest())->withParsedBody([
            'idAccount' => $workunit->getIdAccount(),
            'title' => $workunit->getTitle()
        ]);
    }

    /**
     * @param Timetrack $timetrack
     * @return Link
     */
    private function getWorkunitLink(Workunit $workunit): Link
    {
        return new Link('self', '/api/workunit/' . $workunit->getId() . '/timetrack');
    }
}
