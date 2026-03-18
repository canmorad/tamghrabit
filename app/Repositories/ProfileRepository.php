<?php
namespace App\Repositories;

class ProfileRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function updateProfile($adherent)
    {
        try {
            $this->conn->beginTransaction();

            $sqlUser = "
                update users
                set nom = ?, prenom = ?, dateModifier = now()
                where id = ?
            ";

            $stm = $this->conn->prepare($sqlUser);
            $stm->execute([
                $adherent->getNom(),
                $adherent->getPrenom(),
                $adherent->getId()
            ]);

            $sqlAdherent = "
                update adherents
                set dateNaissance = ?, pays = ?, telephoneCode = ?, telephone = ?, ville = ?, adresse = ?
                where id = ?
            ";

            $stm = $this->conn->prepare($sqlAdherent);
            $stm->execute([
                $adherent->getDateNaissance(),
                $adherent->getPays(),
                $adherent->getTelephoneCode(),
                $adherent->getTelephone(),
                $adherent->getVille(),
                $adherent->getAdresse(),
                $adherent->getId()
            ]);

            $this->conn->commit();

            return true;

        } catch (\PDOException $e) {
            $this->conn->rollBack();

            $errorInfo = $e->errorInfo;

            if (isset($errorInfo[2])) {
                throw new \Exception($errorInfo[2]);
            }

            throw new \Exception("Erreur serveur");
            return false;
        }
    }

    public function updateImageProfile($userId, $chemin)
    {
        try {
            $this->conn->beginTransaction();

            $sqlUser = "
                update users
                set imageProfile = ?
                where id = ?
            ";

            $stm = $this->conn->prepare($sqlUser);
            $stm->execute([
                $chemin,
                $userId,
            ]);

            $this->conn->commit();

            return true;

        } catch (\PDOException $e) {
            $this->conn->rollBack();

            $errorInfo = $e->errorInfo;

            if (isset($errorInfo[2])) {
                throw new \Exception($errorInfo[2]);
            }

            throw new \Exception("Erreur serveur");
            return false;
        }
    }
}