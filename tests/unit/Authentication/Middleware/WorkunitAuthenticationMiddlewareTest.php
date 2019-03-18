<?php

use Authentication\Middleware\WorkunitAuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ServerRequest;
use Workunit\Service\WorkunitService;
use Workunit\Entity\Workunit;
use Zend\Diactoros\Response\JsonResponse;
use Codeception\Util\HttpCode;

class WorkunitAuthenticationMiddlewareTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @group iAmAbleToCreateTimetrackGivenAnExistingWorkunitId
     */
    public function iAmAbleToContinueOnToTheNextMiddlewareGivenAnExistingWorkunitId()
    {
        $idWork = 1;
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $service = $this->prophesize(WorkunitService::class);
        $workunit = $this->getWorkunit($idWork, rand(1, 9999));
        $service->get($idWork)->willReturn($workunit);
        $middleware = new WorkunitAuthenticationMiddleware($service->reveal());
        $request = (new ServerRequest())->withAttribute('id', $idWork)->withParsedBody([
            'idWorkUnit' => $idWork
        ]);
        $handler->handle($request)->willReturn(new JsonResponse('success'));
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(HttpCode::OK, $response->getStatusCode());
    }
    /**
     * @test
     * @group iAmAbleToCreateTimetrackGivenAnExistingWorkunitId
     */
    public function iAmAbleNotToContinueOnToTheNextMiddlewareGivenAnNonExistingWorkunitId()
    {
        $idWork = rand(999, 9999);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $service = $this->prophesize(WorkunitService::class);
        $service->get($idWork)->willThrow(new \Exception('Workunit not exist of id : '.$idWork, 400));
        $middleware = new WorkunitAuthenticationMiddleware($service->reveal());
        $request = (new ServerRequest())->withAttribute('id', $idWork)->withParsedBody([
            'idWorkUnit' => $idWork
        ]);
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(HttpCode::BAD_REQUEST, $response->getStatusCode());
    }
    /**
     * @test
     * @group iAmAbleToCreateTimetrackGivenAnExistingWorkunitId
     */
    public function iAmAbleNotToContinueOnToTheNextMiddlewareGivenAnWorkunitIdAttributeNotEqualToThatOfWorkunitIdRequest()
    {
        $idWork = rand(999, 9999);
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $service = $this->prophesize(WorkunitService::class);
        $service->get($idWork)->willThrow(new \Exception('Id workunit attribute does not equal to id workunit request', 400));
        $middleware = new WorkunitAuthenticationMiddleware($service->reveal());
        $request = (new ServerRequest())->withAttribute('id', $idWork)->withParsedBody([
            'idWorkUnit' => rand(99999, 999999)
        ]);
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(HttpCode::BAD_REQUEST, $response->getStatusCode());
    }

    private function getWorkunit($idWork, $idAccount): Workunit
    {
        $workunit = new Workunit();
        $workunit->setId($idWork);
        $workunit->setIdAccount($idAccount);
        $workunit->setTitle('Test');
        return $workunit;
    }
}
