<?php

namespace Action;


use Codeception\Util\HttpCode;

class CreateWorkunitCest
{
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
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('title must NOT be empty');
    }
}
