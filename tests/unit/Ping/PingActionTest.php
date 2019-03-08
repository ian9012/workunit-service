<?php namespace Ping;

use Codeception\Util\HttpCode;
use Ping\Action\PingAction;
use Zend\Diactoros\ServerRequest;

class PingActionTest extends \Codeception\Test\Unit
{
    /**
     * @var PingAction
     */
    private $action;

    protected function _before()
    {
        $this->action = new PingAction();
    }

    /**
     * @test
     */
    public function weShouldGetPingResponse()
    {
        $response = $this->action->handle(new ServerRequest());
        $this->assertEquals(HttpCode::OK, $response->getStatusCode());
    }
}