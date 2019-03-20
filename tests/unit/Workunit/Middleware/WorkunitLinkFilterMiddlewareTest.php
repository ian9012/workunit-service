<?php

use Workunit\Middleware\WorkunitLinkFilterMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Hal\HalResource;
use Zend\Expressive\Hal\Link;

class WorkunitLinkFilterMiddlewareTest extends \Codeception\Test\Unit
{
    const DEFAULT_JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;
    /**
     * @test
     * @group iGetAllWorkunitOwnerPrivilageLinksIfIAmTheOwner
     */
    public function iGetAllWorkunitOwnerPrivilageLinksIfIAmTheOwner()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $id = 1;
        $serverRequest->getParsedBody()->willReturn([
            'jwt' => [
                'data' => $this->tempClass($id)
            ]
        ]);
        $request = $serverRequest->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn($this->jsonResponse($id));
        $middleware = new WorkunitLinkFilterMiddleware();
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $response = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('update-workunit', $response['_links']);
    }

    /**
     * @test
     * @group iGetAllWorkunitOwnerPrivilageLinksIfIAmTheOwner
     */
    public function iCannotGetAllWorkunitOwnerPrivilageLinksIfIAmTheOwner()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $id = 1;
        $serverRequest->getParsedBody()->willReturn([
            'jwt' => [
                'data' => $this->tempClass($id)
            ]
        ]);
        $request = $serverRequest->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn($this->jsonResponse(3));
        $middleware = new WorkunitLinkFilterMiddleware();
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $response = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayNotHasKey('update-workunit', $response['_links']);
    }
    /**
     * @test
     * @group iGetAllWorkunitOwnerPrivilageLinksIfIAmTheOwner
     */
    public function middlewareWouldIgnoreAnyResponseWhichIsNot200()
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $id = 1;
        $serverRequest->getParsedBody()->willReturn([
            'jwt' => [
                'data' => $this->tempClass($id)
            ]
        ]);
        $request = $serverRequest->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn(new JsonResponse('Error exception', 400));
        $middleware = new WorkunitLinkFilterMiddleware();
        $response = $middleware->process($request, $handler->reveal());
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals(400, $response->getStatusCode());
    }

    private function jsonResponse($id): ResponseInterface
    {
        $response = new JsonResponse([]);
        $response->getBody()->write(json_encode($this->getHalResource($id), self::DEFAULT_JSON_FLAGS));
        $response->withHeader('Content-Type', 'application/hal');
        return $response;
    }

    /**
     * @return HalResource
     */
    private function getHalResource($id): HalResource
    {
        $links = [
            new Link('self', '/api/workunit/1'),
            new Link('create-timetrack', '/api/workunit/1/timetrack'),
            new Link('update-workunit', '/api/workunit/1')
        ];
        return new HalResource($this->halArray($id), $links);
    }

    private function halArray($id)
    {
        return [
            "id" => 9999,
            "idAccount" => $id,
            "title" => "Example Workunit #8657"
        ];
    }

    private function tempClass($id): stdClass
    {
        $data = new stdClass;
        $data->id = $id;
        return $data;
    }
}
