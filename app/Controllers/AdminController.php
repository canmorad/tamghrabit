<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Connection;
use App\Repositories\AdminRepository;
use App\Services\AdminService;

class AdminController extends Controller
{
    private $adminService;

    public function __construct()
    {
        parent::__construct();
        $this->adminService = new AdminService(Connection::getInstance());
    }

    public function dashboard()
    {
        $stats = $this->adminService->getDashboardStats();
        $chartData = $this->adminService->getChartData();

        return $this->view('admin/dashboard', [
            'current_uri' => 'admin_dash',
            'stats' => $stats,
            'chartData' => $chartData
        ]);
    }
}