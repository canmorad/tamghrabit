<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Connection;
use App\Services\MessageService;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Entities\Conversation;
use App\Entities\Message;
use Exception;

Session::start();
class MessageController extends Controller
{
    private $messageService;

    public function __construct()
    {
        parent::__construct();
        $this->messageService = new MessageService(Connection::getInstance());
    }

    public function index()
    {
        $user = Session::get('user');
        $conversations = $this->messageService->getConversationsByUserId($user->getId());

        return $this->view('chat',[
            "user" => $user,
            "conversations" => $conversations
        ]);
    }

    public function delete()
    {
        header('Content-Type: application/json');
        Session::start();

        $user = Session::get("user");

        $idConv = $_GET['id'] ?? null;

        if (!$user || !$idConv) {
            echo json_encode(['type' => 'error', 'message' => 'Données manquantes']);
            exit;
        }

        $this->messageService->delete($idConv, $user->getId());

        echo json_encode([
            "type" => "success"
        ]);
    }

    public function getOrCreate()
    {
        header('Content-Type: application/json');
        Session::start();

        $userSession = Session::get("user");
        $idDestinataire = $_GET['idDestinataire'] ?? null;

        if (!$userSession || !$idDestinataire) {
            echo json_encode(['type' => 'error', 'message' => 'Données manquantes']);
            exit;
        }

        try {
            $idConv = $this->messageService->getOrCreateConversation($userSession->getId(), $idDestinataire);
            echo json_encode(['idConversation' => $idConv]);
        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getHistory()
    {
        header('Content-Type: application/json');
        Session::start();

        $utilisateurSession = Session::get("user");
        $idConversation = $_GET['idConversation'] ?? null;

        if (!$utilisateurSession || !$idConversation) {
            echo json_encode(['messages' => []]);
            exit;
        }

        try {
            $messages = $this->messageService->getMessagesByConversation($idConversation);
            $messagesFormates = [];

            foreach ($messages as $message) {
                $dateCreation = $message->getDateCreation() ?? date('Y-m-d H:i:s');

                $messagesFormates[] = [
                    'contenu' => $message->getContenu(),
                    'nomExpediteur' => $message->getExpediteur()->getNom(),
                    'date' => date('H:i', strtotime($dateCreation)),
                    'estLeMien' => ($message->getExpediteur()->getId() == $utilisateurSession->getId())
                ];
            }

            echo json_encode([
                "messages" => $messagesFormates
            ]);

        } catch (Exception $e) {
            echo json_encode(['erreur' => $e->getMessage()]);
        }
    }

    public function send()
    {
        header('Content-Type: application/json');
        Session::start();
        $userSession = Session::get("user");

        if (!$userSession) {
            echo json_encode(['type' => 'error', 'message' => 'Session expirée']);
            exit;
        }

        $data = [
            "idConversation" => $_POST['idConversation'] ?? '',
            "contenu" => $_POST['contenu'] ?? ''
        ];

        $validate = new Validator($data);
        $validate->field("idConversation", "Conversation")->required()->numeric();
        $validate->field("contenu", "Message")->required();

        if (!$validate->isValid()) {
            echo json_encode(['type' => 'error', 'message' => $validate->errorMessages]);
            exit;
        }

        try {
            $conv = new Conversation();
            $conv->setId($data['idConversation']);

            $msg = new Message($data['contenu']);
            $msg->setConversation($conv);
            $msg->setExpediteur($userSession);

            $message = $this->messageService->sendMessage($msg);

            echo json_encode([
                'type' => 'success',
                'data' => [
                    'id' => $message->getId(),
                    'contenu' => $message->getContenu(),
                    'date' => date('H:i', strtotime($message->getDateCreation() ?? date('Y-m-d H:i:s')))
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}