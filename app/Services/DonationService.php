<?php
namespace App\Services;

use App\Repositories\DonationRepository;
use App\Traits\DatabaseTransaction;

class DonationService
{
    use DatabaseTransaction;
    private $donationRepo;
    private $conn;

    public function __construct($conn)
    {
        $this->donationRepo = new DonationRepository($conn);
        $this->conn = $conn;
    }

    public function myDonations($userId)
    {
        return $this->donationRepo->myDonations($userId);
    }

    public function processDonation($donation)
    {
        try {
            $this->beginTransaction();

            $this->donationRepo->saveDonation($donation);

            $this->donationRepo->updateCampagneAmount($donation);

            $this->commit();

        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}