<?php namespace Action;

use Codeception\Util\HttpCode;

class GetWorkunitCest
{
    private $accessToken;
    private $accessToken2;
    private $validWorkunitID;

    public function _before(\ApiTester $I)
    {
        $config = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings('api', $config);

        $authUrl = $apiSettings['params']['auth-service'];
        $I->sendPOST($authUrl, [
            'email' => $apiSettings['params']['auth-service-email'],
            'password' => $apiSettings['params']['auth-service-password'],
        ]);
        $this->validWorkunitID = $apiSettings['params']['validWorkunitID'];
        $I->seeResponseCodeIs(HttpCode::OK);
        $response = $I->getResponse();
        $I->seeResponseContains('access_token');
        $this->accessToken = $response['access_token'];
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotGetWorkunitWithoutAccessToken(\ApiTester $I)
    {
        $I->sendGET('workunit/'.$this->validWorkunitID);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @param \ApiTester $I
     * @test
     * @group iCanGetWorkunitWithValidId
     */
    public function iCanGetWorkunitWithValidId(\ApiTester $I)
    {
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendGET('workunit/'.$this->validWorkunitID);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('_links');
        $I->seeResponseContains('id');
        $I->seeResponseContains('idAccount');
        $I->seeResponseContains('title');
        $I->seeResponseContains('update-workunit');
    }

    /**
     * @param \ApiTester $I
     * @test
     * @group iCanGetWorkunitWithValidIdButWithoutUpdateLink
     */
    public function iCanGetWorkunitWithValidIdButWithoutUpdateLink(\ApiTester $I)
    {
        $this->authUser2($I);
        $I->amBearerAuthenticated($this->accessToken2);
        $I->sendGET('workunit/'.$this->validWorkunitID);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('_links');
        $I->seeResponseContains('id');
        $I->seeResponseContains('idAccount');
        $I->seeResponseContains('title');
        $I->cantSeeResponseContains('update-workunit');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotGetWorkunitWithNonexistentId(\ApiTester $I)
    {
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendGET('workunit/1');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Workunit not exist of id : 1');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotGet500WithInvalidId(\ApiTester $I)
    {
        $invalids = [
            '0',
            0
        ];
        for ($i = 0; $i < count($invalids); $i++) {
            $I->amBearerAuthenticated($this->accessToken);
            $I->sendGET('workunit/'.$invalids[$i]);
            $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotGet404InvalidId(\ApiTester $I)
    {
        $invalids = [
            'XXXXX',
            '1xxx!!@'
        ];
        for ($i = 0; $i < count($invalids); $i++) {
            $I->amBearerAuthenticated($this->accessToken);
            $I->sendGET('workunit/'.$invalids[$i]);
            $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        }
    }

    /**
     * @param \ApiTester $I
     */
    private function authUser2(\ApiTester $I): void
    {
        $config = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings('api', $config);
        $authUrl = $apiSettings['params']['auth-service'];
        $I->sendPOST($authUrl, [
            'email' => $apiSettings['params']['auth-service-email-2'],
            'password' => $apiSettings['params']['auth-service-password-2'],
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContains('access_token');
        $response = $I->getResponse();
        $this->accessToken2 = $response['access_token'];
    }
}
