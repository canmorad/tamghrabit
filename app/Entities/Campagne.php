<?php

namespace App\Entities;

abstract class Campagne
{
    protected $id;
    protected $adherent;
    protected $categorie;
    protected $titre;
    protected $description;
    protected $image;
    protected $telephone;
    protected $dateDebut;
    protected $dateFin;
    protected $justificatif;
    protected $status;
    protected $dateCreation;
    protected $dateModifier;
    protected $dateSupprimer;

    public function __construct($titre, $description, $image, $telephone, $dateDebut, $dateFin, $categorie, $adherent, $justificatif)
    {
        $this->titre = $titre;
        $this->description = $description;
        $this->image = $image;
        $this->telephone = $telephone;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->categorie = $categorie;
        $this->adherent = $adherent;
        $this->justificatif = $justificatif;
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

    public function getCategorie()
    {
        return $this->categorie;
    }
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

    public function getTitre()
    {
        return $this->titre;
    }
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getImage()
    {
        return $this->image;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function getDateDebut()
    {
        return $this->dateDebut;
    }
    public function setDateDebut($date)
    {
        $this->dateDebut = $date;
    }

    public function getDateFin()
    {
        return $this->dateFin;
    }
    public function setDateFin($date)
    {
        $this->dateFin = $date;
    }

    public function getJustificatif()
    {
        return $this->justificatif;
    }
    public function setJustificatif($path)
    {
        $this->justificatif = $path;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }
    public function getDateModifier()
    {
        return $this->dateModifier;
    }
    public function getDateSupprimer()
    {
        return $this->dateSupprimer;
    }
}