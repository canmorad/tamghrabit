<?php
namespace App\Entities;

class Organisation
{
    private $id;
    private $adherent;
    private $nom;
    private $identifiantFiscal;
    private $adresse;
    private $ribAssociation;
    private $recepisse;
    private $pvElection;
    private $statuts;
    private $attestationRib;
    private $cniPresidentRecto;
    private $cniPresidentVerso;
    private $estVerifie;
    private $dateCreation;

    public function __construct($nom, $identifiantFiscal, $adresse, $ribAssociation, $recepisse, $pvElection, $statuts, $attestationRib, $cniPresidentRecto, $cniPresidentVerso)
    {
        $this->nom = $nom;
        $this->identifiantFiscal = $identifiantFiscal;
        $this->adresse = $adresse;
        $this->ribAssociation = $ribAssociation;
        $this->recepisse = $recepisse;
        $this->pvElection = $pvElection;
        $this->statuts = $statuts;
        $this->attestationRib = $attestationRib;
        $this->cniPresidentRecto = $cniPresidentRecto;
        $this->cniPresidentVerso = $cniPresidentVerso;
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getAdherent()
    {
        return $this->adherent;
    }
    public function setAdherent($adherent)
    {
        $this->adherent = $adherent;
    }

    public function getNom()
    {
        return $this->nom;
    }
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getIdentifiantFiscal()
    {
        return $this->identifiantFiscal;
    }
    public function setIdentifiantFiscal($if)
    {
        $this->identifiantFiscal = $if;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function getRibAssociation()
    {
        return $this->ribAssociation;
    }
    public function setRibAssociation($rib)
    {
        $this->ribAssociation = $rib;
    }
    public function getRecepisse()
    {
        return $this->recepisse;
    }
    public function setRecepisse($path)
    {
        $this->recepisse = $path;
    }

    public function getPvElection()
    {
        return $this->pvElection;
    }
    public function setPvElection($path)
    {
        $this->pvElection = $path;
    }

    public function getStatuts()
    {
        return $this->statuts;
    }
    public function setStatuts($path)
    {
        $this->statuts = $path;
    }

    public function getAttestationRib()
    {
        return $this->attestationRib;
    }
    public function setAttestationRib($path)
    {
        $this->attestationRib = $path;
    }

    public function getCniPresidentRecto()
    {
        return $this->cniPresidentRecto;
    }
    public function setCniPresidentRecto($path)
    {
        $this->cniPresidentRecto = $path;
    }

    public function getCniPresidentVerso()
    {
        return $this->cniPresidentVerso;
    }
    public function setCniPresidentVerso($path)
    {
        $this->cniPresidentVerso = $path;
    }

    public function getEstVerifie()
    {
        return $this->estVerifie;
    }
    public function setEstVerifie($status)
    {
        $this->estVerifie = $status;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }
}