<?php
namespace App\Entities;
use App\Entities\User;

class Adherent extends User
{
    private $sexe;
    private $dateNaissance;
    private $adresse;
    private $ville;
    private $telephoneCode;
    private $telephone;
    private $pays;
    private $estVerifie = false;

    public function __construct($nom, $prenom, $email, $sexe, $password)
    {
        parent::__construct($nom, $prenom, $email, $password);
        $this->sexe = $sexe;
    }

    public function getSexe()
    {
        return $this->sexe;
    }

    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getTelephoneCode()
    {
        return $this->telephoneCode;
    }

    public function getPays()
    {
        return $this->pays;
    }

    public function getEstVerifie(): bool
    {
        return $this->estVerifie;
    }

    public function setSexe($sexe)
    {
        $this->sexe = $sexe;
    }

    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function setTelephoneCode($telephoneCode)
    {
        $this->telephoneCode = $telephoneCode;
    }

    public function setPays($pays)
    {
        $this->pays = $pays;
    }

    public function setEstVerifie(bool $estVerifie)
    {
        $this->estVerifie = $estVerifie;
    }
}