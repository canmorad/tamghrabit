<?php
namespace App\Repositories;
class RegisterRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function store($adherent)
    {

        $stm = $this->conn->prepare("call register(?,?,?,?,?,?,?,?)");
        $stm->execute([
            $adherent->getNom(),
            $adherent->getPrenom(),
            $adherent->getEmail(),
            $adherent->getPassword(),
            $adherent->getSexe(),
            $adherent->getDateNaissance(),
            $adherent->getRole()->getNom(),
            $adherent->getTokenVerification()
        ]);
    }
}