<?php
namespace App\Entities;

class Conversation
{
    protected $id;
    protected $dateCreation;

    public function getId()
    {
        return $this->id;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }
}