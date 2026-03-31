<?php

namespace App\Entities;

class CampagneArgent extends CampagneFinanciere
{
    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif, $objectifMontant)
    {
        parent::__construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif, $objectifMontant);

    }
}