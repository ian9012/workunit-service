<?php

namespace Workunit\Presenter;

use Psr\Http\Message\ResponseInterface;
use Workunit\Entity\Workunit;
use Workunit\Service\WorkunitHalLinkSpecification;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Psr\Http\Message\ServerRequestInterface;

class WorkunitPresenter
{
    /** @var ResourceGenerator */
    private $resourceGenerator;

    /** @var HalResponseFactory */
    private $responseFactory;

    public function __construct(
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory
    ) {

        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
    }

    public function present(Workunit $workunit, ServerRequestInterface $request): ResponseInterface
    {
        $resource = $this->resourceGenerator->fromObject($workunit, $request);
        $halSpecs = new WorkunitHalLinkSpecification($request, $this->resourceGenerator, $workunit);
        $links = $halSpecs->getLinks();
        for ($i=0; $i<count($links); $i++) {
            $resource = $resource->withLink($links[$i]);
        }
        return $this->responseFactory->createResponse($request, $resource);
    }
}
