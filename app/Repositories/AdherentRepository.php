<?php
namespace App\Repositories;

class AdherentRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getTotalMyDonations($adherentId) {
        $sql = "select sum(montant) as total from donations where idAdherent = ? and status = 'complete'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adherentId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function countMyCampagnes($adherentId) {
        $sql = "select count(*) as total from campagnes where idAdherent = ? and dateSupprimer is null";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adherentId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getRecentDonationsReceived($adherentId) {
        $sql = "select d.*, u.nom, u.prenom, c.titre 
                from donations d
                join campagnes c on d.idCampagne = c.id
                join users u on d.idAdherent = u.id
                where c.idAdherent = ? 
                order by d.dateDon desc limit 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adherentId]);
        return $stmt->fetchAll();
    }

    public function getMyCampagnesProgress($adherentId) {
        $sql = "select titre, montantCollecte, objectifMontant 
                from campagnes c
                join campagnesFinancieres cf on c.id = cf.id
                where c.idAdherent = ? and c.status = 'approuvee'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adherentId]);
        return $stmt->fetchAll();
    }
}