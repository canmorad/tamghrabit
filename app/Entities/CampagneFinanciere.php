<?php

namespace App\Entities;

use App\Entities\Campagne;
class CampagneFinanciere extends Campagne
{
    protected $objectifMontant;
    protected $montantCollecte;

    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif,$type, $objectifMontant)
    {
        parent::__construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif, $type);

        $this->objectifMontant = $objectifMontant;
    }

    public function getObjectifMontant()
    {
        return $this->objectifMontant;
    }
    public function setObjectifMontant($montant)
    {
        $this->objectifMontant = $montant;
    }

    public function getMontantCollecte()
    {
        return $this->montantCollecte;
    }
    public function setMontantCollecte($montant)
    {
        $this->montantCollecte = $montant;
    }
}