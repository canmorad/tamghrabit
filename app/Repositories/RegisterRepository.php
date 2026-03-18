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
        try {
            $stm = $this->conn->prepare("call register(?,?,?,?,?,?,?)");
            $stm->execute([
                $adherent->getNom(),
                $adherent->getPrenom(),
                $adherent->getEmail(),
                $adherent->getPassword(),
                $adherent->getSexe(),
                $adherent->getDateNaissance(),
                $adherent->getRole()->getNom(),
            ]);

        } catch (\PDOException $e) {
            $errorInfo = $e->errorInfo;

            if (isset($errorInfo[2])) {
                throw new \Exception($errorInfo[2]);
            }

            throw new \Exception("Erreur serveur");
        }
    }
}