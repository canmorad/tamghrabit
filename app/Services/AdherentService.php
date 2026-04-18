<?php
namespace App\Services;

use App\Repositories\AdherentRepository;

class AdherentService {
    private $adherentRepo;

    public function __construct($conn) {
        $this->adherentRepo = new AdherentRepository($conn);
    }

    public function getDashboardData($adherentId) {
        return [
            'total_donated' => $this->adherentRepo->getTotalMyDonations($adherentId),
            'my_campagnes_count' => $this->adherentRepo->countMyCampagnes($adherentId),
            'recent_donations' => $this->adherentRepo->getRecentDonationsReceived($adherentId),
            'progress_data' => $this->adherentRepo->getMyCampagnesProgress($adherentId)
        ];
    }
}