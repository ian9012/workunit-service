<?php namespace Authentication\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\JwtAuthentication;

class JwtAuthenticationFactoryTest extends \Codeception\Test\Unit
{
    private $container;

    protected function _before()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get('config')->willReturn($this->getConfig());
    }

    /**
     * @test
     */
    public function iCanCreateJWTAUthThroughFactory()
    {
        $factory = new JwtAuthenticationFactory();
        $response = $factory($this->container->reveal());
        $this->assertTrue($response instanceof JwtAuthentication);
    }

    private function getConfig()
    {
        return [
            'jwt_token' => [
                'token' => [
                    "iss" => "http://api.auth.local",
                    "aud" => "http://api.auth.local",
                    "iat" => 1356999524,
                    "nbf" => 1357000000,
                ],
                'key' => 'my-key'
            ],
            'jwt_auth' => [
                'secret' => 'my-key',
                'secure' => false,
                'attribute' => JwtAuthentication::class,
                'before' => function (ServerRequestInterface $request, $params) {
                    $parsedBody = $request->getParsedBody();
                    $parsedBody = array_merge($parsedBody, ['jwt' => $params['decoded']]);
                    $request = $request->withParsedBody($parsedBody);
                    return $request;
                }

            ]
        ];
    }
}
