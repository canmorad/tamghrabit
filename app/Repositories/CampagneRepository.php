<?php
namespace App\Repositories;

class CampagneRepository
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }
    public function commit()
    {
        return $this->conn->commit();
    }
    public function rollBack()
    {
        return $this->conn->rollBack();
    }

    public function storeBase($campagne)
    {
        $sql = "insert into campagnes (idadherent, idcategorie, titre, description, image, telephone, datedebut, datefin, justificatif) 
                values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stm = $this->conn->prepare($sql);
        $stm->execute([
            $campagne->getAdherent()->getId(),
            $campagne->getCategorie()->getId(),
            $campagne->getTitre(),
            $campagne->getDescription(),
            $campagne->getImage(),
            $campagne->getTelephone(),
            $campagne->getDateDebut(),
            $campagne->getDateFin(),
            $campagne->getJustificatif()
        ]);
        return $this->conn->lastInsertId();
    }

    public function storeFinanciere($campagne)
    {
        $sql = "insert into campagnesFinancieres (id, objectifmontant) values (?, ?)";
        $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getObjectifMontant()
        ]);
    }

    public function storeArgent($campagne)
    {
        $sql = "insert into campagnesArgent (id) values (?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
        ]);
    }

    public function storeParrainage($campagne)
    {
        $sql = "insert into campagnesParrainage (id, frequence) values (?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getFrequence()
        ]);
    }

    public function storeAssociation($campagne)
    {
        $sql = "insert into campagnesAssociation (id, idorganisation) values (?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getOrganisation()->getId()
        ]);
    }

    public function storeNature($campagne)
    {
        $sql = "insert into campagnesNature (id, typedon, nomarticle) values (?, ?, ?)";
        return $this->conn->prepare($sql)->execute([
            $campagne->getId(),
            $campagne->getTypeDon(),
            $campagne->getNomArticle()
        ]);
    }
}