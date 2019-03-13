<?php

namespace Workunit\Presenter;

use Psr\Http\Message\ResponseInterface;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Psr\Http\Message\ServerRequestInterface;

class WorkunitPresenter
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

    public function present(Workunit $workunit, ServerRequestInterface $request): ResponseInterface
    {
        $resource = $this->resourceGenerator->fromObject($workunit, $request);
        $link = $this->resourceGenerator
            ->getLinkGenerator()
            ->fromRoute('create-timetrack', $request, 'timetrack.create', ['id' => $workunit->getId()]);
        $resource = $resource->withLink($link);
        return $this->responseFactory->createResponse($request, $resource);
    }
}
