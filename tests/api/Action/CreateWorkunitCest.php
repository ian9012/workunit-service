<?php

namespace Action;


use Codeception\Util\HttpCode;

class CreateWorkunitCest
{
    private $accessToken;

    public function _before(\ApiTester $I)
    {
        $config = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings('api', $config);

        $authUrl = $apiSettings['params']['auth-service'];
        $I->sendPOST($authUrl, [
            'email' => $apiSettings['params']['auth-service-email'],
            'password' => $apiSettings['params']['auth-service-password'],
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $response = $I->getResponse();
        $this->accessToken = $response['access_token'];
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCanCreateWorkunit(\ApiTester $I)
    {
        $payload = [
            'idAccount' => '1',
            'title' => 'Example workunit #'.rand(1, 9999),
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('_links');
        $I->seeResponseContains('id');
        $I->seeResponseContains('idAccount');
        $I->seeResponseContains('title');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateWorkunitWithEmptyIdAccount(\ApiTester $I)
    {
        $payload = [
            'idAccount' => null,
            'title' => 'Example workunit #'.rand(1, 9999),
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id account must be supplied');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateWorkunitWithZeroIdAccount(\ApiTester $I)
    {
        $payload = [
            'idAccount' => 0,
            'title' => 'Example workunit #'.rand(1, 9999),
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id account must be supplied');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateWorkunitWithNegativeIdAccount(\ApiTester $I)
    {
        $payload = [
            'idAccount' => -1,
            'title' => 'Example workunit #'.rand(1, 9999),
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('id account must be a POSITIVE integer value');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateWorkunitWithEmptyTitle(\ApiTester $I)
    {
        $payload = [
            'idAccount' => 1,
            'title' => null,
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('title must NOT be empty');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateWorkunitWithoutAccessToken(\ApiTester $I)
    {
        $payload = [
            'idAccount' => 1,
            'title' => 'Some title',
        ];
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
