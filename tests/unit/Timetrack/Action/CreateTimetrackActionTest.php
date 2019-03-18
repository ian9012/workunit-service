<?php

use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;
use Timetrack\Action\CreateTimetrackAction;
use Timetrack\Service\TimetrackService;
use Timetrack\Entity\Timetrack;
use Zend\Expressive\Hal\Link;
use Helper\HalPresenter;

class CreateTimetrackActionTest extends \Codeception\Test\Unit
{
    /**
     * @var HalPresenter
     */
    private $presenter = null;

    protected function _before()
    {
        $this->presenter = $this->prophesize(HalPresenter::class);
    }

    /**
     * @test
     * @dataProvider provideTimetrack
     */
    public function iCanGetAPIResponseWhenCreatingATimetrackIsSuccessful(Timetrack $timetrack)
    {
        $service = $this->prophesize(TimetrackService::class);
        $timetrackResponse = clone $timetrack;
        $timetrackResponse->setId(rand(1, 9999));
        $request = $this->getRequest($timetrack);

        $service->create($timetrack)->willReturn($timetrackResponse);
        $this->presenter->present($timetrackResponse, $request)->willReturn($this->getHalResponse($timetrack));

        $action = new CreateTimetrackAction($service->reveal(), $this->presenter->reveal());
        $response = $action->handle($request);
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals(200, $response->getStatusCode());
    }
    /**
     * @test
     * @dataProvider provideTimetrack
     */
    public function iCanGetAPIResponseWhenCreatingATimetrackIsUnsuccessful(Timetrack $timetrack)
    {
        $service = $this->prophesize(TimetrackService::class);
        $service->create($timetrack)->willThrow(new \Exception('Invalid id user.', 400));
        $action = new CreateTimetrackAction($service->reveal(), $this->presenter->reveal());
        $response = $action->handle($this->getRequest($timetrack));
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function iCanGetAPIResponseWhenCreatingATimetrackIsUnsuccessfulWithEmptyRequest()
    {
        $timetrack = new Timetrack();
        $service = $this->prophesize(TimetrackService::class);
        $service->create($timetrack)->willThrow(new \Exception('Invalid id user.', 400));
        $action = new CreateTimetrackAction($service->reveal(), $this->presenter->reveal());
        $response = $action->handle($this->getRequest($timetrack));
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals(400, $response->getStatusCode());
    }

    private function getRequest(Timetrack $timetrack)
    {
        return (new ServerRequest())->withParsedBody([
            'idAccount' => $timetrack->getIdAccount(),
            'idWorkUnit' => $timetrack->getIdWorkunit(),
            'duration' => $timetrack->getDuration(),
            'description' => $timetrack->getDescription(),
            'date' => $timetrack->getDate(),
        ]);
    }

    public function provideTimetrack()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdAccount(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h30m');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        return [
            [$timetrack]
        ];
    }

    private function getHalResponse(Timetrack $timetrack): \Psr\Http\Message\ResponseInterface
    {
        $hydrator = new \Zend\Hydrator\ReflectionHydrator();
        $links = [
            new Link('create-timetrack', '/api/workunit/'
                .$timetrack->getIdWorkunit().'/timetrack/'.$timetrack->getId())
        ];
        $resource = new \Zend\Expressive\Hal\HalResource($hydrator->extract($timetrack), $links);
        return new \Zend\Diactoros\Response\JsonResponse($resource);
    }
}
