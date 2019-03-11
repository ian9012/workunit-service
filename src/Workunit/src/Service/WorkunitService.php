<?php

namespace Workunit\Service;

use Workunit\Entity\Workunit;

class WorkunitService
{
    private $collections;

    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * @param $idAccount
     * @param $title
     * @return mixed
     * @throws \Exception
     */
    public function create($idAccount, $title): int
    {
        if (empty($idAccount) || !is_int($idAccount)) {
            throw new \Exception('id account must be a valid integer value', 400);
        }

        if (empty($title)) {
            throw new \Exception('title must NOT be empty', 400);
        }

        $workunit = $this->setWorkunitObject($idAccount, $title);

        $this->add($workunit);

        return $workunit->getId();
    }

    public function get($id)
    {
        $index = array_search($id, array_column($this->collections, 'id'));

        if (!$index) {
            return $index;
        }

        return $this->collections[$index];
    }

    /**
     * command to get collections
     */
    public function getWorkunitCollections() : array
    {
        return $this->collections;
    }

    private function initCollections(): void
    {
        $workunit = new Workunit();
        $workunit->setIdAccount(rand(1, 9999));
        $workunit->setTitle('Example Workunit #' . rand(1, 9999));
        $workunit->setId(rand(1, 9999));
        $this->add($workunit);
    }

    /**
     * @param $idAccount
     * @param $title
     * @return Workunit
     */
    private function setWorkunitObject($idAccount, $title): Workunit
    {
        $workunit = new Workunit();
        $workunit->setId(rand(1, 9999));
        $workunit->setIdAccount($idAccount);
        $workunit->setTitle($title);
        return $workunit;
    }

    /**
     * @param Workunit $workunit
     */
    private function add(Workunit $workunit): void
    {
        $this->collections[] = $workunit;
    }
}
