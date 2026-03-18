<?php
namespace App\Entities;

class Rib
{
    private $id;
    private $rib;
    private $attestationRib;
    private $dateModifier;
    private $adherent;

    public function __construct($rib = null, $attestationRib = null)
    {
        $this->rib = $rib;
        $this->attestationRib = $attestationRib;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getRib()
    {
        return $this->rib;
    }
    public function getAttestationRib()
    {
        return $this->attestationRib;
    }
    public function getAdherent()
    {
        return $this->adherent;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setRib($rib)
    {
        $this->rib = $rib;
    }
    public function setAttestationRib($path)
    {
        $this->attestationRib = $path;
    }
    public function setAdherent($adherent)
    {
        $this->adherent = $adherent;
    }
}