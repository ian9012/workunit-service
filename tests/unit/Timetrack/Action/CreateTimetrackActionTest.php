<?php

use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;
use Timetrack\Action\CreateTimetrackAction;
use Timetrack\Service\TimetrackService;
use Timetrack\Entity\Timetrack;

class CreateTimetrackActionTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @dataProvider provideTimetrack
     */
    public function iCanGetAPIResponseWhenCreatingATimetrackIsSuccessful(Timetrack $timetrack)
    {
        $service = $this->prophesize(TimetrackService::class);
        $timetrackResponse = clone $timetrack;
        $timetrackResponse->setId(rand(1, 9999));
        $service->create($timetrack)->willReturn($timetrackResponse);
        $action = new CreateTimetrackAction($service->reveal());
        $response = $action->handle($this->getRequest($timetrack));
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
        $action = new CreateTimetrackAction($service->reveal());
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
        $action = new CreateTimetrackAction($service->reveal());
        $response = $action->handle($this->getRequest($timetrack));
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals(400, $response->getStatusCode());
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
}
