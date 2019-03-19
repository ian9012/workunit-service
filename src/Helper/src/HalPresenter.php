<?php

namespace Helper;

use Psr\Http\Message\ResponseInterface;
use Psr\Link\LinkInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Hal\HalResource;

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

    /**
     * @param $object
     * @param ServerRequestInterface $request
     * @param LinkInterface[] $links
     * @return ResponseInterface
     */
    public function present($object, ServerRequestInterface $request, array $links = []): ResponseInterface
    {
        $resource = $this->resourceGenerator->fromObject($object, $request);
        if (!empty($links)) {
            $resource = $this->addHalLinks($links, $resource);
        }
        return $this->responseFactory->createResponse($request, $resource);
    }

    /**
     * @param LinkInterface[] $links
     * @param HalResource $resource
     * @return HalResource
     */
    private function addHalLinks(array $links, HalResource $resource): HalResource
    {
        for ($i = 0; $i < count($links); $i++) {
            $resource = $resource->withLink($links[$i]);
        }
        return $resource;
    }
}
