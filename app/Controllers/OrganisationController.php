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

class OrganisationController extends Controller
{
    private $ribService;

    public function __construct()
    {
        parent::__construct();
        $this->ribService = new RibService(Connection::getInstance());
    }

    public function create()
    {
        $userSession = Session::get('user');

        $data = [
            "idAdherent" => $userSession['id'],
            "nom" => $_POST['nom'] ?? '',
            "identifiantFiscal" => $_POST['identifiantFiscal'] ?? '',
            "adresse" => $_POST['adresse'] ?? '',
            "ribAssociation" => $_POST['ribAssociation'] ?? '',
            "recepisse" => $_FILES['recepisse'] ?? null,
            "pvElection" => $_FILES['pvElection'] ?? null,
            "statuts" => $_FILES['statuts'] ?? null,
            "attestationRib" => $_FILES['attestationRib'] ?? null,
            "cniPresidentRecto" => $_FILES['cniPresidentRecto'] ?? null,
            "cniPresidentVerso" => $_FILES['cniPresidentVerso'] ?? null
        ];

        $validate = new Validator($data);
        $validate->field("nom", "Nom de l'ONG")->required();
        $validate->field("identifiantFiscal", "Identifiant Fiscal")->required();
        $validate->field("adresse", "Adresse")->required();
        $validate->field("ribAssociation", "RIB")->required();
        $validate->field("recepisse", "Récépissé")->file_required();
        $validate->field("pvElection", "PV d'élection")->file_required();
        $validate->field("statuts", "Statuts")->file_required();
        $validate->field("attestationRib", "Attestation RIB")->file_required();
        $validate->field("cniPresidentRecto", "CIN Recto")->file_required();
        $validate->field("cniPresidentVerso", "CIN Verso")->file_required();

    }

    public function update()
    {

    }
}