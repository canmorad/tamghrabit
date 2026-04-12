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

    public function updateStatus($orgId, $status, $reason = null)
    {
        $sql = "UPDATE organisations SET status = ?, rejection_reason = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $reason, $orgId]);
    }

    public function getAllPendingWithUsers()
    {
        $sql = "SELECT o.*, u.nom as last_name, u.prenom, u.email 
            FROM organisations o 
            JOIN users u ON o.idAdherent = u.id 
            WHERE o.status = 'en_attente' 
            ORDER BY o.dateCreation DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrgByUserId($id)
    {
        $sql = "select id, nom, status from organisations where idAdherent = ?";
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

    public function store(Organisation $org)
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

    public function update(Organisation $org, $id)
    {
        $sql = "update organisations set 
            nom = ?, identifiantFiscal = ?, adresse = ?, ribAssociation = ?, 
            recepisse = ?, pvElection = ?, statuts = ?, attestationRib = ?, 
            cniPresidentRecto = ?, cniPresidentVerso = ?
            where id = ? and idAdherent = ?";

        $stm = $this->conn->prepare($sql);

        return $stm->execute([
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
            $id,
            $org->getAdherent()->getId()
        ]);
    }
}