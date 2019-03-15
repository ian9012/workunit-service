<?php

namespace Timetrack\Entity;

class Timetrack
{
    private $id;
    private $description;
    private $date;
    private $duration;
    private $idUser;
    private $idWorkunit;

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param mixed $idUser
     */
    public function setIdUser($idUser): void
    {
        $this->idUser = $idUser;
    }

    /**
     * @return mixed
     */
    public function getIdWorkunit()
    {
        return $this->idWorkunit;
    }

    /**
     * @param mixed $idWorkunit
     */
    public function setIdWorkunit($idWorkunit): void
    {
        $this->idWorkunit = $idWorkunit;
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
