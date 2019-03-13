<?php

use Workunit\Action\CreateWorkunitAction;
use Zend\Diactoros\ServerRequest;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\Link;

class CreateWorkunitActionTest extends \Codeception\Test\Unit
{
    /**
     * @var CreateWorkunitAction
     */
    private $action = null;

    protected function _before()
    {
    }

    protected function _after()
    {
        $this->action = null;
    }

    /**
     * @test
     * @dataProvider validCreateResponse
     * @group weCanGetResponseAfterCreateWorkunit
     */
    public function weCanGetResponseAfterCreateWorkunit(Workunit $workunit)
    {
        $request = (new ServerRequest())->withParsedBody([
            'idAccount' => $workunit->getIdAccount(),
            'title' => $workunit->getTitle()
        ]);
        $mockService = $this->prophesize(\Workunit\Service\WorkunitService::class);
        $mockPresenter = $this->prophesize(\Workunit\Presenter\CreateWorkunitPresenter::class);
        $mockService->create($workunit->getIdAccount(), $workunit->getTitle())->willReturn($workunit->getId());
        $mockService->get($workunit->getId())->willReturn($workunit);
        $mockPresenter->present($workunit, $request)->willReturn($this->getHalResponse($workunit));
        $this->action = new CreateWorkunitAction($mockService->reveal(), $mockPresenter->reveal());
        $response = $this->action->handle($request);
        $this->assertTrue($response instanceof \Psr\Http\Message\ResponseInterface);
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
     * @dataProvider invalidCreateResponse
     */
    public function weGet400ResponseAfterCreateWorkunit(Workunit $workunit)
    {
        $mockPresenter = $this->prophesize(\Workunit\Presenter\CreateWorkunitPresenter::class);
        $mock = $this->prophesize(\Workunit\Service\WorkunitService::class);
        $mock->create($workunit->getIdAccount(), $workunit->getTitle())
            ->willThrow(new \Exception('id account must be a valid integer value', 400));
        $this->action = new CreateWorkunitAction($mock->reveal(), $mockPresenter->reveal());
        $request = (new ServerRequest())->withParsedBody([
            'idAccount' => $workunit->getIdAccount(),
            'title' => $workunit->getTitle()
        ]);
        $response = $this->action->handle($request);
        $this->assertTrue($response instanceof \Psr\Http\Message\ResponseInterface);
        $this->assertEquals(400, $response->getStatusCode());
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

    public function invalidCreateResponse()
    {
        $workunit = new Workunit();
        $workunitInvalidId = new Workunit();
        $workunitInvalidId->setIdAccount('asdfff');
        $workunitEmptyTitle = new Workunit();
        $workunitEmptyTitle->setTitle(null);
        return [
            [$workunit],
            [$workunitInvalidId],
            [$workunitEmptyTitle]
        ];
    }
}