<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Core\Connection;
use App\Services\AdherentService;

class AdherentController extends Controller {
    private $service;

    public function __construct() {
        parent::__construct();
        $this->service = new AdherentService(Connection::getInstance());
    }

    public function dashboard() {
        $user = Session::get('user');
        $data = $this->service->getDashboardData($user->getId());

        return $this->view('adherent/dashboard', [
            'stats' => $data,
            'current_uri' => 'adherent_dash'
        ]);
    }
}