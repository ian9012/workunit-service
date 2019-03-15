<?php

use Timetrack\Service\TimetrackService;
use Timetrack\Entity\Timetrack;
use Timetrack\Validator\TimetrackValidator;

class TimetrackServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var TimetrackService
     */
    private $service;
    /**
     * @test
     * @dataProvider provideTimetrack
     */
    public function iCanCreateTimetrack(Timetrack $timetrack)
    {
        $validator = $this->prophesize(TimetrackValidator::class);
        $validator->validate($timetrack)->willReturn(null);
        $this->service = new TimetrackService($validator->reveal());
        $timetrackResponse = $this->service->create($timetrack);
        $this->assertTrue($timetrack instanceof Timetrack);
        $this->assertEquals($timetrack->getDate(), $timetrackResponse->getDate());
        $this->assertEquals($timetrack->getDescription(), $timetrackResponse->getDescription());
        $this->assertEquals($timetrack->getDuration(), $timetrackResponse->getDuration());
        $this->assertEquals($timetrack->getIdUser(), $timetrackResponse->getIdUser());
        $this->assertEquals($timetrack->getIdWorkunit(), $timetrackResponse->getIdWorkunit());
        $id = $timetrackResponse->getId();
        $this->assertNotNull($id);
        $this->assertTrue($this->service->get($id) instanceof Timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid id workunit.
     * @dataProvider provideInvalidTimetrackObject
     */
    public function iCannotCreateTimetrackWithInvalidTimetrackObject(Timetrack $timetrack)
    {
        $validator = $this->prophesize(TimetrackValidator::class);
        $validator->validate($timetrack)->willThrow(new \Exception('Invalid id workunit.',400));
        $this->service = new TimetrackService($validator->reveal());
        $this->service->create($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @dataProvider provideNonexistingAndInvalidTimetrack
     */
    public function iCannotGetTimetrackWithANonExistingAndInvalidId($id)
    {
        $validator = $this->prophesize(TimetrackValidator::class);
        $this->service = new TimetrackService($validator->reveal());
        $this->service->get($id);
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
    public function provideNonexistingAndInvalidTimetrack()
    {
        return [
            [null],
            [999991],
            ['0'],
            [0],
            [-1],
            ['aeiou'],
            ['hello world'],
            ['!!!qqqq']
        ];
    }

    public function provideInvalidTimetrackObject()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        $timetrackNegativeIdWorkunit = clone $timetrack;
        $timetrackNegativeIdWorkunit->setIdWorkunit(-1);
        $timetrackNoIdWorkunit = clone $timetrackNegativeIdWorkunit;
        $timetrackNoIdWorkunit->setIdWorkunit(null);
        $timetrackInvalidIdWorkunit = clone $timetrackNoIdWorkunit;
        $timetrackInvalidIdWorkunit->setIdWorkunit('hello world');

        return [
            [$timetrackNegativeIdWorkunit],
            [$timetrackNoIdWorkunit],
            [$timetrackInvalidIdWorkunit]
        ];
    }
}
