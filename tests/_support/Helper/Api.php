<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function getResponse(): array
    {
        $response = $this->getModule('REST')->response;
        return json_decode($response, true);
    }
}
