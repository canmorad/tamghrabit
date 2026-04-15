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

    // App/Controllers/DonationController.php

    public function checkout()
    {
        // 1. التحقق من تسجيل الدخول
        $user = Session::get('user');
        if (!$user) {
            Session::flush('error', 'Veuillez vous connecter pour faire un don.');
            header('Location: ' . url('auth/login'));
            exit();
        }

        // 2. التحقق من المعطيات
        $montant = (float) ($_POST['amount'] ?? 0); // توحيد الاسم مع HTML
        $idCampagne = $_POST['idCampagne'] ?? null;

        if ($montant <= 0) {
            Session::flush('error', 'Le montant doit être supérieur à zéro.');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // 3. بناء الـ Entities
        $campagne = new Campagne('', '', '', '', '', '', '', '', '', '');
        $campagne->setId($idCampagne);

        $donation = new Donation($montant);
        $donation->setCampagne($campagne);
        $donation->setAdherent($user);

        try {
            $this->donationService->processDonation($donation);

            // نجاح العملية
            Session::flush('success', 'Merci pour votre générosité ! Votre don de ' . $montant . ' DH a été enregistré.');
            header('Location: ' . url('campagne/show?id=' . $idCampagne));
            exit();

        } catch (\Exception $e) {
            Session::flush('error', 'Une erreur est survenue lors du don : ' . $e->getMessage());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}