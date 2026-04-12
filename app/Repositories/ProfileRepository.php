<?php
namespace App\Repositories;

class ProfileRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function updatePassword($user)
    {
        $sql = 'update users set password = ? where id = ?';
        $stm = $this->conn->prepare($sql);
        $stm->execute([
            $user->getPassword(),
            $user->getId()
        ]);
    }

    public function existEmail($email)
    {
        $stm = $this->conn->prepare('select email from users where email = ?');
        $stm->execute([$email]);
        return $stm->fetch() ? true : false;
    }

    public function confirmEmailUpdate($userId, $nouveauEmail)
    {
        $sql = "update users set email = ? where id = ?";
        $stm = $this->conn->prepare($sql);
        return $stm->execute([$nouveauEmail, $userId]);
    }

    public function updateProfile($adherent)
    {
        $sqlUser = "update users set nom = ?, prenom = ?, dateModifier = now() where id = ?";
        $stm = $this->conn->prepare($sqlUser);
        $stm->execute([
            $adherent->getNom(),
            $adherent->getPrenom(),
            $adherent->getId()
        ]);

        $sqlAdherent = "update adherents set dateNaissance = ?, pays = ?, telephoneCode = ?, telephone = ?, ville = ?, adresse = ? where id = ?";
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

        return true;
    }

    public function updateImageProfile($userId, $chemin)
    {
        $sqlUser = "update users set imageProfile = ? where id = ?";
        $stm = $this->conn->prepare($sqlUser);
        return $stm->execute([$chemin, $userId]);
    }
}