<?php

namespace App\Entities;

class CampagneAssociation extends CampagneFinanciere
{
    private $organisation;

    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif,$type, $objectifMontant, $organisation)
    {
        parent::__construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif,$type, $objectifMontant);
        $this->organisation = $organisation;
    }

    public function getOrganisation()
    {
        return $this->organisation;
    }
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }
}