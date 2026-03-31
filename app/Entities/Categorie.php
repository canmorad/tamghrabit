<?php

namespace App\Entities;

class Categorie
{
    private $id;
    private $nom;

    public function __construct($nom)
    {
        $this->nom = $nom;
    }
    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }
}