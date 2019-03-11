<?php

namespace Workunit\Entity;

class Workunit
{
    private $id;
    private $idAccount;
    private $title;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdAccount()
    {
        return $this->idAccount;
    }

    /**
     * @param mixed $idAccount
     */
    public function setIdAccount($idAccount): void
    {
        $this->idAccount = $idAccount;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function __get($propertyName)
    {
        return $this->$propertyName;
    }

    public function __isset($propertyName)
    {
        return isset($this->$propertyName);
    }
}
