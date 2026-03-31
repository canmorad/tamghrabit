<?php

namespace App\Entities;

class CampagneParrainage extends CampagneFinanciere
{
    private $frequence;

    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif, $objectifMontant, $frequence)
    {
        parent::__construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent,$justificatif, $objectifMontant);
        $this->frequence = $frequence;
    }

    public function getFrequence()
    {
        return $this->frequence;
    }
    public function setFrequence($frequence)
    {
        $this->frequence = $frequence;
    }
}