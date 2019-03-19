<?php

use Workunit\Service\WorkunitHalLinkSpecification;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Hal\ResourceGenerator;
use Workunit\Entity\Workunit;
use Psr\Link\LinkInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Zend\Expressive\Hal\LinkGenerator;
use Zend\Expressive\Hal\Link;

class WorkunitHalLinkSpecificationTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function iCanGetLinks()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $generator = $this->prophesize(ResourceGenerator::class);
        $workUnit = $this->prophesize(Workunit::class);
        $workUnit->getId()->willReturn(1);
        $workUnit = $workUnit->reveal();
        $request = $request->reveal();
        $linkGenerator = $this->generateMockLinkGenerator($request);
        $generator->getLinkGenerator()->willReturn($linkGenerator->reveal());

        $service = new WorkunitHalLinkSpecification($request, $generator->reveal(), $workUnit);

        $links = $service->getLinks();
        for ($i = 0; $i < count($links); $i++) {
            $this->assertTrue($links[$i] instanceof LinkInterface);
        }
    }
    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Workunit cannot be empty
     */
    public function iCannotCreateWorkunitHalLinkSpecificationInstanceGivenEmptyWorkunitObject()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $generator = $this->prophesize(ResourceGenerator::class);
        $workUnit = $this->prophesize(Workunit::class);
        $workUnit->getId()->willReturn(null);
        new WorkunitHalLinkSpecification($request->reveal(), $generator->reveal(), $workUnit->reveal());
    }

    /**
     * @param ServerRequestInterface $request
     * @return ObjectProphecy
     */
    private function generateMockLinkGenerator(ServerRequestInterface $request): ObjectProphecy
    {
        $linkGenerator = $this->prophesize(LinkGenerator::class);
        $linkGenerator
            ->fromRoute('create-timetrack', $request, 'timetrack.create', ['id' => 1])
            ->willReturn(new Link('create-timetrack', '/api/workunit/1/timetracking'));
        $linkGenerator
            ->fromRoute('update-workunit', $request, 'workunit.update', ['id' => 1])
            ->willReturn(new Link('update-workunit', '/api/workunit/1'));
        return $linkGenerator;
    }
}
