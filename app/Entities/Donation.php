<?php

namespace App\Entities;

class Donation
{
    protected $id;
    protected $montant;
    protected $status;
    protected $dateDon;
    protected $campagne;
    protected $adherent;

    public function __construct($montant)
    {
        $this->montant = $montant;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDateDon()
    {
        return $this->dateDon;
    }

    public function getCampagne()
    {
        return $this->campagne;
    }

    public function getAdherent()
    {
        return $this->adherent;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setMontant($montant)
    {
        $this->montant = $montant;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setDateDon($dateDon)
    {
        $this->dateDon = $dateDon;
        return $this;
    }

    public function setCampagne($campagne)
    {
        $this->campagne = $campagne;
        return $this;
    }

    public function setAdherent($adherent)
    {
        $this->adherent = $adherent;
        return $this;
    }
}