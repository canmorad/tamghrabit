<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\DonationService;
use App\Helpers\Session;
use App\Entities\Donation;
use App\Entities\Campagne;
use App\Core\Connection;

Session::start();

class DonationController extends Controller
{
    private $donationService;

    public function __construct()
    {
        parent::__construct();
        $this->donationService = new DonationService(Connection::getInstance());
    }

    public function myDonations()
    {
        $user = Session::get('user');

        $donations = $this->donationService->myDonations($user->getId());

        return $this->view('mesDons', [
            'donations' => $donations,
            'current_uri' => 'mes_dons'
        ]);
    }

    public function checkout()
    {
        $user = Session::get('user');

        $montant = (float) ($_POST['montant'] ?? 0);
        $idCampagne = $_POST['idCampagne'] ?? null;

        if ($montant <= 0) {
            Session::flush('error', 'Le montant doit être supérieur à zéro.');
            return $this->view('auth/login');
            exit();
        }

        $campagne = new Campagne('', '', '', '', '', '', '', '', '', '');
        $campagne->setId($idCampagne);

        $donation = new Donation($montant);
        $donation->setCampagne($campagne);
        $donation->setAdherent($user);

        try {
            $this->donationService->processDonation($donation);

            Session::flush('success', 'Merci pour votre générosité ! Votre don de ' . $montant . ' DH a été enregistré.');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();

        } catch (\Exception $e) {
            Session::flush('error', 'Une erreur est survenue lors du don : ' . $e->getMessage());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}