<?php

namespace App\Entities;

class Identifier
{
    private $id;
    private $cniRecto;
    private $cniVerso;
    private $passport;
    private $dateModifier;
    private $adherent;

    public function __construct($cniRecto = null, $cniVerso = null, $passport = null)
    {
        $this->cniRecto = $cniRecto;
        $this->cniVerso = $cniVerso;
        $this->passport = $passport;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getCniRecto()
    {
        return $this->cniRecto;
    }

    public function getCniVerso()
    {
        return $this->cniVerso;
    }

    public function getPassport()
    {
        return $this->passport;
    }

    public function getDateModifier()
    {
        return $this->dateModifier;
    }

    public function setAdherent($adherent)
    {
        $this->adherent = $adherent;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCniRecto($path)
    {
        $this->cniRecto = $path;
    }

    public function setCniVerso($path)
    {
        $this->cniVerso = $path;
    }

    public function setPassport($path)
    {
        $this->passport = $path;
    }

    public function getAdherent()
    {
        return $this->adherent;
    }
}