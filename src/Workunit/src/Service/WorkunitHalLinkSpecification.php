<?php

namespace Workunit\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Link\LinkInterface;
use Workunit\Entity\Workunit;
use Zend\Expressive\Hal\ResourceGenerator;

class WorkunitHalLinkSpecification
{
    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var ResourceGenerator
     */
    private $generator;
    /**
     * @var LinkInterface[]
     */
    private $links;
    /**
     * @var Workunit
     */
    private $workunit;

    public function __construct(
        ServerRequestInterface $request,
        ResourceGenerator $generator,
        Workunit $workunit
    ) {

        $this->request = $request;
        $this->generator = $generator;
        $this->workunit = $workunit;

        if (empty($this->workunit->getId())) {
            throw new \Exception('Workunit cannot be empty', 400);
        }

        $this->init();
    }

    private function init()
    {
        $linkCreateTimetrack = $this->generator
            ->getLinkGenerator()
            ->fromRoute(
                'create-timetrack',
                $this->request,
                'timetrack.create',
                ['id' => $this->workunit->getId()]
            );
        $linkUpdateWorkunit = $this->generator
            ->getLinkGenerator()
            ->fromRoute(
                'update-workunit',
                $this->request,
                'workunit.update',
                ['id' => $this->workunit->getId()]
            );
        $this->links[] = $linkCreateTimetrack;
        $this->links[] = $linkUpdateWorkunit;
    }

    /**
     * @return LinkInterface[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
