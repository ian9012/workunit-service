<?php

namespace Helper;

use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Psr\Http\Message\ServerRequestInterface;

class HalPresenter
{
    /** @var ResourceGenerator */
    private $resourceGenerator;

    /** @var HalResponseFactory */
    private $responseFactory;

    public function __construct(ResourceGenerator $resourceGenerator, HalResponseFactory $responseFactory)
    {
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
    }

    public function present($object, ServerRequestInterface $request, $link = null): ResponseInterface
    {
        $resource = $this->resourceGenerator->fromObject($object, $request);
        if ($link) {
            $resource = $resource->withLink($link);
        }
        return $this->responseFactory->createResponse($request, $resource);
    }
}
