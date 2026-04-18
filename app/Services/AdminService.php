<?php
namespace App\Services;

use App\Repositories\AdminRepository;

class AdminService
{
    private $adminRepo;

    public function __construct($conn)
    {
        $this->adminRepo = new AdminRepository($conn);
    }

    public function getDashboardStats()
    {
        return [
            'users' => $this->adminRepo->countUsers(),
            'campagnes' => $this->adminRepo->countActiveCampagnes(),
            'revenus' => $this->adminRepo->getTotalRevenues(),
            'rib_rate' => $this->adminRepo->getRibValidationRate()
        ];
    }

    public function getChartData()
    {
        $monthly = $this->adminRepo->getMonthlyRevenues();
        $types = $this->adminRepo->getCampagnesByType();

        return [
            'months' => array_column($monthly, 'month'),
            'revenues' => array_column($monthly, 'amount'),
            'type_labels' => array_column($types, 'type'),
            'type_counts' => array_column($types, 'total')
        ];
    }
}