<?php
namespace App\Repositories;

use App\Entities\Organisation;
use PDO;

class OrganisationRepository
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

    public function getOrgByUserId($id)
    {
        $sql = "select id, nom, estVerifie from organisations where idAdherent = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$id]);

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrgById($id)
    {
        $sql = "select * from organisations where id = ?";
        $stm = $this->conn->prepare($sql);
        $stm->execute([$id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function create(Organisation $org)
    {
        $sql = "insert into organisations (
                    idAdherent, nom, identifiantFiscal, adresse, ribAssociation, 
                    recepisse, pvElection, statuts, attestationRib, 
                    cniPresidentRecto, cniPresidentVerso
                ) value (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stm = $this->conn->prepare($sql);

        $stm->execute([
            $org->getAdherent()->getId(),
            $org->getNom(),
            $org->getIdentifiantFiscal(),
            $org->getAdresse(),
            $org->getRibAssociation(),
            $org->getRecepisse(),
            $org->getPvElection(),
            $org->getStatuts(),
            $org->getAttestationRib(),
            $org->getCniPresidentRecto(),
            $org->getCniPresidentVerso(),
        ]);
    }
}