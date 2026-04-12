<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Entities\User;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Services\OrganisationService;
use App\Services\ProfileService;
use App\Core\Connection;

Session::start();
class ProfileController extends Controller
{
    private $profileService;
    private $orgService;
    public function __construct()
    {
        parent::__construct();
        $this->orgService = new OrganisationService(Connection::getInstance());
        $this->profileService = new ProfileService(Connection::getInstance());
    }

    public function demanderMiseAjourEmail()
    {
        header('Content-Type: application/json');
        $user = Session::get('user');

        $nouveauEmail = $_POST['nouveauEmail'] ?? '';
        $motDePasse = $_POST['motDePasse'] ?? '';

        $validate = new Validator([
            'nouveauEmail' => $nouveauEmail,
            'password' => $motDePasse
        ]);

        $validate->field('nouveauEmail', 'nouvelle email')->required()->email();
        $validate->field('password', 'mot de passe')->required();

        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages]);
            exit;
        }

        try {
            $this->profileService->preparerMiseAjourEmail($user, $nouveauEmail, $motDePasse);

            echo json_encode(['type' => 'success', 'step' => 'otp', 'message' => "Un code a été envoyé à $nouveauEmail"]);
        } catch (\Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function confirmerMiseAjourEmail()
    {
        header('Content-Type: application/json');
        $codeSaisi = $_POST['codeOtp'] ?? '';

        try {
            $resultat = $this->profileService->finaliserMiseAjourEmail($codeSaisi);

            if ($resultat) {
                $user = Session::get('user');
                $tempData = $_SESSION['modification_email_temp'];
                $user->setEmail($tempData['nouveauEmail']);
                Session::set('user', $user);

                unset($_SESSION['modification_email_temp']);

                echo json_encode(['type' => 'success', 'message' => "Email mis à jour avec succès !"]);
            }
        } catch (\Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updatePassword()
    {
        header('Content-Type: application/json');

        $data = [
            'ancienPassword' => $_POST['ancienPassword'] ?? '',
            'nouvellePassword' => $_POST['nouvellePassword'] ?? '',
            'confirmPassword' => $_POST['confirmPassword'] ?? '',
        ];

        $validate = new Validator($data);
        $validate->field('ancienPassword', 'Ancien mot de passe')->required();
        $validate->field('nouvellePassword', 'Mot de passe')->required()->min_len(8);
        $validate->field('confirmPassword', 'confirmer mot de passe')->required()->equals($data['nouvellePassword']);

        if (!$validate->isValid()) {
            echo json_encode([
                'type' => 'error',
                'message' => $validate->errorMessages
            ]);
            exit;
        }

        try {
            $user = Session::get('user');

            $this->profileService->updatePassword($user, $data['ancienPassword'], $data['nouvellePassword']);

            echo json_encode([
                'type' => 'success',
                'message' => 'Mot de passe mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
    public function edit()
    {
        $user = Session::get("user");
        $Organisations = $this->orgService->getOrgByUserId($user->getId());

        $this->view('profile/edit', [
            "user" => $user,
            "organisations" => $Organisations,
            'current_uri' => 'edit_profile'
        ]);
    }

    public function settings()
    {
        return $this->view('profile/settings', [
            'current_uri' => 'settings'
        ]);
    }

    public function adminProfile()
    {
        $this->view('admin/adminProfile', [
            'current_uri' => 'edit_profile'
        ]);
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");

        $data = [
            'sexe' => $_POST['sexe'] ?? '',
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'],
            'dateNaissance' => $_POST['dateNaissance'] ?? '',
            'pays' => $_POST['pays'] ?? '',
            'telephoneCode' => $_POST['telephone'] ? $_POST['telephoneCode'] : '',
            'telephone' => $_POST['telephone'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            'adresse' => $_POST['adresse'] ?? '',
        ];

        $validate = new Validator($data);

        $validate->field('nom')->required()->alpha([' ']);
        $validate->field('prenom')->required()->alpha([' ']);

        if (!$validate->isValid()) {
            echo json_encode([
                'type' => 'error',
                'message' => $validate->errorMessages
            ]);
            exit;
        }

        try {
            $userSession->setNom($data['nom']);
            $userSession->setPrenom($data['prenom']);
            $userSession->setDateNaissance($data['dateNaissance'] ?? '');
            $userSession->setPays($data['pays'] ?? '');
            $userSession->setTelephoneCode($data['telephoneCode'] ?? '');
            $userSession->setTelephone($data['telephone'] ?? '');
            $userSession->setVille($data['ville'] ?? '');
            $userSession->setAdresse($data['adresse'] ?? '');

            $this->profileService->updateProfile($userSession);
            Session::set("user", $userSession);

            echo json_encode([
                'type' => 'success',
                'message' => 'Profil mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateImageProfile()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");
        $file = $_FILES['imageProfile'] ?? null;

        $validate = new Validator(['imageProfile' => $file]);
        $validate->field('imageProfile', "La photo")
            ->file_required()
            ->is_image()
            ->file_size(2 * 1024 * 1024);

        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages['imageProfile']]);
            exit;
        }

        try {
            $nomFichierUnique = $this->profileService->updateImageProfile($userSession, $file);
            $userSession->setImageProfile($nomFichierUnique);
            Session::set("user", $userSession);

            echo json_encode(['type' => 'success', 'message' => "Photo mise à jour !"]);

        } catch (\Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

}