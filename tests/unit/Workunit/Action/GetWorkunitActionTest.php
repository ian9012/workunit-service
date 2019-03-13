<?php

namespace Workunit\Action;

use Codeception\Util\HttpCode;
use Psr\Http\Message\ResponseInterface;
use Workunit\Entity\Workunit;
use Workunit\Presenter\WorkunitPresenter;
use Workunit\Service\WorkunitService;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Hal\Link;

class GetWorkunitActionTest extends \Codeception\Test\Unit
{

    /**
     * @test
     * @dataProvider validCreateResponse
     */
    public function iCanGetWOrkunitResponseGivenID(Workunit $workunit)
    {
        $request = $this->getRequest($workunit->getId());
        $service = $this->prophesize(WorkunitService::class);
        $service->get($workunit->getId())->willReturn($workunit);
        $presenter = $this->prophesize(WorkunitPresenter::class);
        $presenter->present($workunit, $request)->willReturn($this->getHalResponse($workunit));
        $action = new GetWorkunitAction($service->reveal(), $presenter->reveal());
        $response = $action->handle($request);
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(HttpCode::OK, $response->getStatusCode());
        $responseArr = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('_links', $responseArr);
        $this->assertArrayHasKey('id', $responseArr);
        $this->assertArrayHasKey('idAccount', $responseArr);
        $this->assertArrayHasKey('title', $responseArr);
        $this->assertNotEmpty($responseArr['_links']);
        $this->assertEquals($workunit->getId(), $responseArr['id']);
        $this->assertEquals($workunit->getIdAccount(), $responseArr['idAccount']);
        $this->assertEquals($workunit->getTitle(), $responseArr['title']);
    }

    /**
     * @test
     * @expectedException \Exception
     * @dataProvider invalidRequestAttributes
     */
    public function iCannotGetWorkunitResponseWithInvalidRequestAttributes()
    {
        $request = new ServerRequest();
        $service = $this->prophesize(WorkunitService::class);
        $presenter = $this->prophesize(WorkunitPresenter::class);
        $action = new GetWorkunitAction($service->reveal(), $presenter->reveal());
        $action->handle($request);
    }

    /**
     * @test
     * @dataProvider validCreateResponse
     */
    public function iGetErrorWorkunitResponseWithNonExistingID(Workunit $workunit)
    {
        $request = $this->getRequest($workunit->getId());
        $service = $this->prophesize(WorkunitService::class);
        $presenter = $this->prophesize(WorkunitPresenter::class);
        $service->get($workunit->getId())->willThrow(new \Exception('Workunit not exist of id : '.
            $workunit->getId(), 400));
        $action = new GetWorkunitAction($service->reveal(), $presenter->reveal());
        $response = $action->handle($request);
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(HttpCode::BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return ServerRequest
     */
    private function getRequest($id): ServerRequest
    {
        return (new ServerRequest())->withAttribute('id', $id);
    }

    public function validCreateResponse()
    {
        $idWU = 99999;
        $workunit = new Workunit();
        $workunit->setId($idWU);
        $workunit->setIdAccount(1);
        $workunit->setTitle('Example Workunit #'.$idWU);
        return [
            [$workunit]
        ];
    }

    public function invalidRequestAttributes()
    {
        return [
            [(new ServerRequest())],
            [(new ServerRequest())->withAttribute('id', 'XXXXX')],
            [(new ServerRequest())->withAttribute('id', '1!!!')],
        ];
    }

    public function getHalResponse(Workunit $workunit): \Psr\Http\Message\ResponseInterface
    {
        $hydrator = new \Zend\Hydrator\ReflectionHydrator();
        $links = [
            new Link('self', '/api/workunit/'.$workunit->getId()),
            new Link('create-timetrack', '/api/workunit/'.$workunit->getId().'/timetracking')
        ];
        $resource = new \Zend\Expressive\Hal\HalResource($hydrator->extract($workunit), $links);
        return new \Zend\Diactoros\Response\JsonResponse($resource);
    }
}
