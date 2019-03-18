<?php

use Codeception\Util\HttpCode;

class CreateTimetrackingCest
{
    private $accessToken;

    public function _before(ApiTester $I)
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
    public function iCannotCreateTimetrackWithoutAuthentication(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->amBearerAuthenticated(null);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCanCreateTimetrack(\ApiTester $I)
    {
           $response = $this->extractResponse($this->createWorkunit($I));
           $response['duration'] = '8h30m';
           $response['description'] = 'First timetrack';
           $response['date'] = '23-08-2018';
           $I->amBearerAuthenticated($this->accessToken);
           $href = $response['links']['create-timetrack']['href'];
           unset($response['links']);
           $I->sendPOST($href, $response);
           $I->seeResponseCodeIs(HttpCode::OK);
           $I->seeResponseIsJson();
           $I->seeResponseContains('_links');
           $I->seeResponseContains('self');
           $I->seeResponseContains('id');
           $I->seeResponseContains('idAccount');
           $I->seeResponseContains('idWorkunit');
           $I->seeResponseContains('duration');
           $I->seeResponseContains('description');
           $I->seeResponseContains('date');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithoutIdUser(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        unset($response['idAccount']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid id user.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithoutIdWorkunit(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        unset($response['idWorkUnit']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid id workunit.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithoutDate(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = 'First timetrack';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid date.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackInvalidDate(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = 'First timetrack';
        $response['date'] = '08-23-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid date.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithoutDuration(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid Duration.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithInvalidDuration(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $response['duration'] = '6h71m';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid Duration.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithInvalidDuration2(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $response['duration'] = '3h   61m';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid Duration.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithInvalidDuration3(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $response['duration'] = '61h3m';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid Duration.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithInvalidDuration4(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['description'] = 'First timetrack';
        $response['date'] = '23-08-2018';
        $response['duration'] = '!!!@@@';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid Duration.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithoutDescription(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['date'] = '23-08-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Invalid description.');
    }

    /**
     * @param \ApiTester $I
     * @test
     */
    public function iCannotCreateTimetrackWithDescriptionMoreThan250(\ApiTester $I)
    {
        $response = $this->extractResponse($this->createWorkunit($I));
        $response['duration'] = '8h30m';
        $response['description'] = "Contrary to popular belief, Lorem Ipsum is not simply random text.
         It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.
          Richard McClintock, 
         a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words,
          consectetur,
          from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the
           undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\"
            (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory
             of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor
              sit amet..\", comes from a line in section 1.10.32.";
        $response['date'] = '23-08-2018';
        $I->amBearerAuthenticated($this->accessToken);
        $href = $response['links']['create-timetrack']['href'];
        unset($response['links']);
        $I->sendPOST($href, $response);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('Description should not be more than 250 characters.');
    }

    private function createWorkunit(\ApiTester $I)
    {
        $payload = [
            'idAccount' => '1',
            'title' => 'Example workunit #'.rand(1, 9999),
        ];
        $I->amBearerAuthenticated($this->accessToken);
        $I->sendPOST('workunit', $payload);
        $I->seeResponseCodeIs(HttpCode::OK);
        $response = $I->getResponse();
        return $response;
    }

    private function extractResponse($response)
    {
        $response['_links']['create-timetrack']['href'] = explode('/api/',
            $response['_links']['create-timetrack']['href'])[1];
        return [
            'idAccount' => $response['idAccount'],
            'idWorkUnit' => $response['id'],
            'links' => $response['_links']
        ];
    }
}
