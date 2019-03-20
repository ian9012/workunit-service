<?php

namespace Helper;

use Psr\Http\Message\ResponseInterface;

class ResponseExtractor
{
    public static function toArray(ResponseInterface $response)
    {
        $body = $response->getBody();
        $body->rewind();
        return json_decode($body->getContents(), true);
    }
}
