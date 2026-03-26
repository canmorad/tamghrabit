<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Entities\Rib;
use App\Core\Connection;
use App\Services\RibService;
use App\Helpers\Validator;
use Exception;

Session::start();

class RibController extends Controller
{
    private $ribService;

    public function __construct()
    {
        parent::__construct();
        $this->ribService = new RibService(Connection::getInstance());
    }

    public function index()
    {
        $user = Session::get("user");
      
        $rib = $this->ribService->getRibByUserId($user->getId());
        echo json_encode([
            "rib" => $rib->getRib() ? $rib->getRib() : '',
            "attestationRib" => $rib->getAttestationRib() ? url("public/storage/ribs/".$rib->getAttestationRib()) : '' 
        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');
        $userSession = Session::get("user");

        $data = [
            "rib" => $_POST['rib'] ?? '',
            "attestationRib" => $_FILES['attestationRib'] ?? null
        ];

        $validate = new Validator($data);
        $validate->field("rib", "RIB")->required();

        $existingRib = $this->ribService->getRibByUserId($userSession->getId());

        if (!$existingRib || !$existingRib->getAttestationRib()) {
            $validate->field("attestationRib", "Attestation")->file_required();
        }

        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages]);
            exit;
        }

        try {
            $rib = new Rib($data['rib'], $data['attestationRib']);
            $rib->setAdherent($userSession);

            $this->ribService->update($rib);
            echo json_encode(['type' => 'success', 'message' => "Informations bancaires enregistrées !"]);
        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}