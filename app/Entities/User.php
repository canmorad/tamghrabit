<?php
namespace App\Entities;
class User
{
    protected $id;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $password;
    protected $imageProfile;
    protected $role;
    protected $idGoogle ;
    protected $tokenVerification;
    protected $estVerifieGmail;
    protected $adherent;

    public function __construct($nom, $prenom, $email, $password)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
    }

    public function getAdherent()
    {
        return $this->adherent;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getImageProfile()
    {
        return $this->imageProfile;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAdherent($adherent)
    {
        $this->adherent = $adherent;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setImageProfile($imageProfile)
    {
        $this->imageProfile = $imageProfile;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
    public function getIdGoogle()
    {
        return $this->idGoogle;
    }

    public function setIdGoogle($idGoogle)
    {
        $this->idGoogle = $idGoogle;
        return $this;
    }

    public function getTokenVerification()
    {
        return $this->tokenVerification;
    }

    public function setTokenVerification($tokenVerification)
    {
        $this->tokenVerification = $tokenVerification;
        return $this;
    }

    public function getEstVerifieGmail()
    {
        return (bool) $this->estVerifieGmail;
    }
    public function setEstVerifieGmail($estVerifieGmail)
    {
        $this->estVerifieGmail = $estVerifieGmail;
        return $this;
    }

}