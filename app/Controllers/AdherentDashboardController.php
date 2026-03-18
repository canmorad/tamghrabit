<?php
namespace App\Controllers;
use App\Core\Controller;

class AdherentDashboardController extends Controller
{
    public function dashboard()
    {
        return $this->view('adherent/dashboard');
    }
}