<?php
namespace App\Repositories;

use App\Entities\Donation;
use App\Entities\Campagne;
class DonationRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    public function saveDonation($donation)
    {
        $sql = "INSERT INTO donations (idCampagne, idAdherent, montant, status) VALUES (?, ?, ?, 'complete')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $donation->getCampagne()->getId(),
            $donation->getAdherent()->getId(),
            $donation->getMontant()
        ]);
    }

    public function myDonations($userId)
    {
        $sql = "select d.*, c.titre as campagneTitre
                from donations d 
                join campagnes c ON d.idCampagne = c.id 
                where d.idAdherent = ? 
                order by d.dateDon DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $donations = [];
        foreach ($rows as $row) {
            
            $donation = new Donation($row['montant']);
            $donation->setId($row['id'])
                ->setStatus($row['status'])
                ->setDateDon($row['dateDon']);

            $campagne = new Campagne($row['campagneTitre'], '', '', '', '', '', '', '', '', '');
            $campagne->setId($row['idCampagne']);

            $donation->setCampagne($campagne);
            $donations[] = $donation;
        }

        return $donations;
    }

    public function updateCampagneAmount($donation)
    {
        $sql = "update campagnesFinancieres 
                set montantCollecte = montantCollecte + ? 
                where id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $donation->getMontant(),
            $donation->getCampagne()->getId()
        ]);
    }
}