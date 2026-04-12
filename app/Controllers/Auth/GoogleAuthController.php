<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Helpers\Session;
use App\Services\AuthentificationService;
use App\Core\Connection;
use App\Entities\User;
use Google\Client;
use Google\Service\Oauth2;
use App\Entities\Adherent;

class GoogleAuthController extends Controller
{
    private $authService;
    private $googleClient;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthentificationService(Connection::getInstance());

        $this->googleClient = new Client();
        $this->googleClient->setClientId('636624887237-6etnq9800j8i7rdtcrqd12c2r4obmh29.apps.googleusercontent.com');
        $this->googleClient->setClientSecret('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');
        $this->googleClient->setRedirectUri('http://localhost/Tamghrabit/auth/google/callback');
        $this->googleClient->addScope("email");
        $this->googleClient->addScope("profile");
    }

    public function redirectToGoogle()
    {
        header('Location: ' . $this->googleClient->createAuthUrl());
        exit;
    }

    public function handleGoogleCallback()
    {
        Session::start();

        if (!isset($_GET['code'])) {
            header('Location: /Tamghrabit/login');
            exit;
        }

        try {
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->googleClient->setAccessToken($token);

            $googleService = new Oauth2($this->googleClient);
            $googleUser = $googleService->userinfo->get();

            $user = new User($googleUser->familyName ?? $googleUser->name, $googleUser->givenName ?? '', $googleUser->email, '');
            $user->setIdGoogle($googleUser->id);
            $user->setImageProfile($googleUser->picture);


            $foundUser = $this->authService->loginWithGoogle($user);

            if ($foundUser) {
                if ($foundUser->getRole()->getNom() === "adherent") {
                    $adherentData = $this->authService->getAdherentByUserId($foundUser->getId());
                    $adherent = new Adherent($adherentData['nom'], $adherentData['prenom'], $adherentData['email'], $adherentData['sexe'], "");
                    $adherent->setId($foundUser->getId());
                    $adherent->setRole($foundUser->getRole());
                    $adherent->setImageProfile($foundUser->getImageProfile());
                    Session::set("user", $adherent);
                    header("Location: /Tamghrabit/profile/edit");
                } else {
                    Session::set("user", $foundUser);
                    header("Location: /Tamghrabit/admin/users");
                }
                exit;
            }

        } catch (\Exception $e) {
            Session::flush("errors", "Erreur Google Auth: " . $e->getMessage());
            header("Location: /Tamghrabit/login");
            exit;
        }
    }
}