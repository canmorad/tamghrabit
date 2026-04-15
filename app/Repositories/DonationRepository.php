<?php
namespace App\Repositories;


class DonationRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // App/Repositories/DonationRepository.php
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