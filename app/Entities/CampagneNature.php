<?php

namespace App\Entities;

class CampagneNature extends Campagne
{
    private $typeDon;
    private $nomArticle;

    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif, $typeDon, $nomArticle)
    {
        parent::__construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif);
        $this->typeDon = $typeDon;
        $this->nomArticle = $nomArticle;
    }

    public function getTypeDon()
    {
        return $this->typeDon;
    }
    public function setTypeDon($type)
    {
        $this->typeDon = $type;
    }

    public function getNomArticle()
    {
        return $this->nomArticle;
    }
    public function setNomArticle($nom)
    {
        $this->nomArticle = $nom;
    }
}