<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Entities\CampagneParrainage;
use App\Entities\CampagneAssociation;
use App\Entities\Organisation;
use App\Entities\CampagneArgent;
use App\Entities\CampagneNature;
use App\Services\CampagneService;
use App\Entities\Categorie;
use App\Core\Connection;
use App\Services\OrganisationService;
use App\Services\CategorieService;
use App\Helpers\Validator;

Session::start();
class CampagneController extends Controller
{
    private $campagneService;
    private $orgService;
    private $catService;


    public function __construct()
    {
        parent::__construct();
        $this->campagneService = new CampagneService(Connection::getInstance());
        $this->orgService = new OrganisationService(Connection::getInstance());
        $this->catService = new CategorieService(Connection::getInstance());
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        $user = Session::get('user');

        if (!$id || !$user) {
            header('Location: ' . url('mes-campagnes'));
            exit;
        }

        $campagne = $this->campagneService->getCampagneById($id);

        if (!$campagne) {
            $this->abort("Campagne non trouvée", 404);
        }

        if ($campagne['idAdherent'] !== $user->getId()) {
            $this->abort("Accès refusé", 403);
        }

        $organisations = $this->orgService->getOrgByUserId($user->getId());
        $categories = $this->catService->getAllCategories();

        return $this->view("campagne/edit", [
            'campagne' => $campagne,
            'organisations' => $organisations,
            'categories' => $categories,
            'suffix' => $campagne['type']
        ]);
    }

    public function updateStatus()
    {
        $id = $_POST['id'];
        $status = $_POST['status'];

        $this->campagneService->toggleStatus($id, $status);
        return $this->redirect(url('admin/campagnes/actives'));
    }

     public function campagnesActives()
    {
        $campagnes =$this->campagneService->getListActive();
        return $this->view('admin/campagnesActives', [
            'campagnes' => $campagnes,
            'current_uri' => 'admin_active_camp'
        ]);
    }

    public function explorer()
    {
        $campagnes = $this->campagneService->getAllCampagnes();
        return $this->view("explorer/campagnes", [
            'campagnes' => $campagnes,
            'current_uri' => 'explorer'
        ]);
    }

    public function gererCampagne()
    {
        $id = $_GET['id'] ?? null;
        $user = Session::get('user');

        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

        if (!$id) {
            http_response_code(404);
            echo "Campagne non spécifiée";
            exit;
        }

        try {
            $data = $this->campagneService->getManageData($id, $user->getId());

            if (!$data || !$data['campagne']) {
                http_response_code(403);
                echo "Accès refusé";
                exit;
            }

            return $this->view('campagne/gererCampagne', [
                'campagne' => $data['campagne'],
                'donors' => $data['donors'],
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo "Erreur serveur: " . $e->getMessage();
            exit;
        }
    }
    public function show()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(404);
            echo "Route non trouvée";
            exit;
        }

        $campagne = $this->campagneService->getCampagneById($id);

        if (!$campagne) {
            http_response_code(404);
            echo "Route non trouvée";
            exit;
        }

        return $this->view("explorer/campagneDetail", [
            'c' => $campagne
        ]);
    }

    public function mesCampagnes()
    {
        $user = Session::get('user');
        $campagnes = $this->campagneService->getCampagnesByUser($user->getId());

        return $this->view("campagne/mesCampagnes", [
            'campagnes' => $campagnes,
            'count' => count($campagnes),
            'current_uri' => 'mes_campagnes'
        ]);
    }

    public function create()
    {
        $user = Session::get('user');
        $organisations = $this->orgService->getOrgByUserId($user->getId());
        $categories = $this->catService->getAllCategories();
        return $this->view("campagne/create", [
            'organisations' => $organisations,
            'categories' => $categories,
            'current_uri' => 'create_campagne'
        ]);
    }

    public function delete()
    {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(["type" => "error", "message" => "ID introuvable"]);
            exit;
        }
        try {
            $this->campagneService->delete($id);
            echo json_encode(["type" => "success", "message" => "Campagne supprimée avec succès"]);
        } catch (\Exception $e) {
            echo json_encode(["type" => "error", "message" => $e->getMessage()]);
        }
    }

    public function pending()
    {
        $campagnes = $this->campagneService->getPendingCampagnes();

        return $this->view('admin/validerCampagnes', [
            'campagnes' => $campagnes,
            'current_uri' => 'admin_pending_camp'
        ]);
    }

    public function verify()
    {
        header('Content-Type: application/json');

        $id = $_POST['orgId'] ?? null;
        $action = $_POST['action'] ?? null;
        $reason = $_POST['reason'] ?? '';

        if (!$id || !$action) {
            echo json_encode(["type" => "error", "message" => "Données incomplètes"]);
            exit;
        }

        try {
            $res = $this->campagneService->verifyCampagne($id, $action, $reason);
            if ($res) {
                echo json_encode(["type" => "success", "message" => "Action effectuée avec succès"]);
            } else {
                echo json_encode(["type" => "error", "message" => "Échec de l'opération"]);
            }
        } catch (\Exception $e) {
            echo json_encode(["type" => "error", "message" => $e->getMessage()]);
        }
    }

    public function terminer()
    {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(["type" => "error", "message" => "ID introuvable"]);
            exit;
        }

        try {
            $this->campagneService->terminerCampagne($id);
            echo json_encode([
                "type" => "success",
                "message" => "La campagne a été marquée comme terminée."
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                "type" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');
        $adherent = Session::get('user');
        $id = $_POST['id'];

        if (!$id) {
            echo json_encode(["type" => "error", "message" => "ID manquant"]);
            exit;
        }

        $type = $_POST['type'];
        $data = [
            "id" => $id,
            "type" => $type,
            "titre" => $_POST['titre'] ?? '',
            "description" => $_POST['description'] ?? '',
            "telephone" => $_POST['telephone'] ?? '',
            "dateDebut" => $_POST['dateDebut'] ?? '',
            "dateFin" => $_POST['dateFin'] ?? '',
            "idCategorie" => $_POST['idCategorie'] ?? '',
            "image" => ($_FILES['image']['size'] > 0) ? $_FILES['image'] : null,
            "justificatif" => ($_FILES['justificatif']['size'] > 0) ? $_FILES['justificatif'] : null
        ];

        $validateur = new Validator($data);
        $validateur->field("titre", "Titre")->required();
        $validateur->field("description", "Description")->required();
        $validateur->field("telephone", "Téléphone")->required();
        $validateur->field("dateDebut", "Date de début")->required();
        $validateur->field("dateFin", "Date de fin")->required();
        $validateur->field("idCategorie", "Catégorie")->required();

        if (!$validateur->isValid()) {
            echo json_encode(["type" => "error", "message" => $validateur->errorMessages]);

            exit;
        }

        $categorie = new Categorie('');
        $categorie->setId($data['idCategorie']);

        $campagne = null;

        if ($type === 'parrainage') {
            $campagne = new CampagneParrainage($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $_POST['objectifMontant'], $_POST['frequence']);
        } elseif ($type === 'association') {
            $organisation = new Organisation('', '', '', '', '', '', '', '', '', '');
            $organisation->setId($_POST['idOrganisation']);
            $campagne = new CampagneAssociation($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $_POST['objectifMontant'], $organisation);
        } elseif ($type === 'argent') {
            $campagne = new CampagneArgent($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $_POST['objectifMontant']);
        } elseif ($type === 'nature') {
            $campagne = new CampagneNature($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $_POST['typeDon'], $_POST['nomArticle']);
        }

        $campagne->setId($id);

        try {
            $this->campagneService->update($campagne);

            echo json_encode(["type" => "success", "message" => "Campagne mise à jour !"]);
        } catch (\Exception $e) {
            echo json_encode(["type" => "error", "message" => $e->getMessage()]);
        }
    }

    public function store()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($uri, '/'));
        $type = end($parts);

        header('Content-Type: application/json');

        $adherent = Session::get('user');

        $data = [
            "titre" => $_POST['titre'] ?? '',
            "description" => $_POST['description'] ?? '',
            "telephone" => $_POST['telephone'] ?? '',
            "dateDebut" => $_POST['dateDebut'] ?? '',
            "dateFin" => $_POST['dateFin'] ?? '',
            "idCategorie" => $_POST['idCategorie'] ?? '',
            "image" => $_FILES['image'] ?? null,
            "justificatif" => $_FILES['justificatif'] ?? null,
        ];

        $categorie = new Categorie('');
        $categorie->setId($data['idCategorie']);

        $validateur = new Validator($data);
        $validateur->field("titre", "Titre")->required();
        $validateur->field("description", "Description")->required();
        $validateur->field("telephone", "Téléphone")->required();
        $validateur->field("dateDebut", "Date de début")->required();
        $validateur->field("dateFin", "Date de fin")->required();
        $validateur->field("idCategorie", "Catégorie")->required();
        $validateur->field("image", "Image")->file_required()->is_image();
        $validateur->field("justificatif", "Justificatif")->file_required();

        if (in_array($type, ['argent', 'parrainage', 'association'])) {
            $data['objectifMontant'] = $_POST['objectifMontant'] ?? '';
            $validateur->set('objectifMontant', $data['objectifMontant']);
            $validateur->field("objectifMontant", "Montant")->required();
        }

        if ($type === 'parrainage') {
            $data['frequence'] = $_POST['frequence'] ?? '';
            $validateur->set('frequence', $data['frequence']);
            $validateur->field("frequence", "Fréquence")->required();
        }

        if ($type === 'association') {
            $data['idOrganisation'] = $_POST['idOrganisation'] ?? '';
            $validateur->set('idOrganisation', $data['idOrganisation']);
            $validateur->field("idOrganisation", "Organisation")->required();
        }

        if ($type === 'nature') {
            $data['nomArticle'] = $_POST['nomArticle'] ?? '';
            $data['typeDon'] = $_POST['typeDon'] ?? '';
            $validateur->set('nomArticle', $data['nomArticle']);
            $validateur->set('typeDon', $data['typeDon']);
            $validateur->field("nomArticle", "Article")->required();
            $validateur->field("typeDon", "Type de don")->required();
        }

        if (!$validateur->isValid()) {
            header('Content-Type: application/json');
            echo json_encode(["type" => "error", "message" => $validateur->errorMessages]);
            exit;
        }

        $campagne = null;

        if ($type === 'parrainage') {
            $campagne = new CampagneParrainage($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $data['objectifMontant'], $data['frequence']);
        } elseif ($type === 'association') {
            $organisation = new Organisation('', '', '', '', '', '', '', '', '', '');
            $organisation->setId($data['idOrganisation']);
            $campagne = new CampagneAssociation($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $data['objectifMontant'], $organisation);
        } elseif ($type === 'argent') {
            $campagne = new CampagneArgent($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $data['objectifMontant']);
        } elseif ($type === 'nature') {
            $campagne = new CampagneNature($data['titre'], $data['description'], $data['image'], $data['telephone'], $data['dateDebut'], $data['dateFin'], $categorie, $adherent, $data['justificatif'], $type, $data['typeDon'], $data['nomArticle']);
        }

        try {
            $this->campagneService->store($campagne);
            echo json_encode(["type" => "success", "message" => "Campagne créée avec succès !"]);
        } catch (\Exception $e) {
            echo json_encode(["type" => "error", "message" => $e->getMessage()]);
        }
    }
}