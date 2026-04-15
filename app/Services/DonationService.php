<?php
namespace App\Services;

use App\Repositories\DonationRepository;
use App\Traits\DatabaseTransaction;

class DonationService
{
    use DatabaseTransaction;
    private $repository;
    private $conn;

    public function __construct($conn)
    {
        $this->repository = new DonationRepository($conn);
        $this->conn = $conn;
    }

    public function processDonation($donation)
    {
        try {
            $this->beginTransaction();

            $this->repository->saveDonation($donation);

            $this->repository->updateCampagneAmount($donation);

            $this->commit();

        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}