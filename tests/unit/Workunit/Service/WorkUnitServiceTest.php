<?php

use Workunit\Service\WorkunitService;
use Workunit\Entity\Workunit;

class WorkUnitServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var WorkunitService
     */
    private $service;

    protected function _before()
    {
        $this->service = new WorkunitService();
    }

    protected function _after()
    {
        $this->service = null;
    }

    /**
     * @test
     */
    public function weCanCreateWorkunit()
    {
        $idAccount = 1;
        $title = 'Workunit Example';
        $idWorkUnit = $this->service->create($idAccount, $title);
        $this->assertIsInt($idWorkUnit);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @dataProvider pageProvider
     */
    public function weCannotCreateWorkunitWithInvalidParameters($idAccount, $title)
    {
        $this->service->create($idAccount, $title);
    }

    /**
     * @test
     */
    public function weCanGetWorkunitByID()
    {
        $idAccount = 1;
        $title = 'Workunit Example';
        $idWorkUnit = $this->service->create($idAccount, $title);
        $workunit = $this->service->get($idWorkUnit);
        $this->assertTrue($workunit instanceof Workunit);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @dataProvider invalidIdWorkUnitProvider
     */
    public function weCannotGetWorkunitByNonExistingIDAndInvalidID($idWorkUnit)
    {
        $this->service->get($idWorkUnit);
    }

    /**
     * @return array
     */
    public function pageProvider()
    {
        return [
            [null, 'Example workunit'],
            ['iAmAString', 'Example workunit'],
            [1, null],
            [-1, null],
            ['1', null],
            ['-1', null]
        ];
    }

    /**
     * @return array
     */
    public function invalidIdWorkUnitProvider()
    {
        return [
            [rand(99999, 999999)],
            [null],
            ['iAmAString'],
            ['0'],
            [0],
        ];
    }
}