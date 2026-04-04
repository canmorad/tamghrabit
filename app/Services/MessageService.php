<?php
namespace App\Services;

use App\Repositories\MessageRepository;
use App\Entities\Message;
use App\Entities\Conversation;
use App\Entities\User;
use App\Traits\DatabaseTransaction;
use Exception;

class MessageService
{
    use DatabaseTransaction;
    private $messageRepo;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->messageRepo = new MessageRepository($conn);
    }

    public function sendMessage($message)
    {
        return $this->messageRepo->save($message);
    }

    public function getConversationsByUserId($id)
    {
        return $this->messageRepo->getConversationsByUserId($id);
    }

    public function getOrCreateConversation($user1, $user2)
    {
        $conversation = $this->messageRepo->findConversationBetweenUsers($user1, $user2);

        if ($conversation) {
            return $conversation['idConversation'];
        }

        try {
            $this->beginTransaction();
            $idConv = $this->messageRepo->createNewConversation($user1, $user2);
            $this->commit();
            return $idConv;
        } catch (Exception $e) {
            $this->rollBack();
            throw new Exception("Erreur serveur lors de la création de la conversation");
        }
    }

    public function getMessagesByConversation($idConv)
    {
        $lignes = $this->messageRepo->findByConversationId($idConv);
        $messages = [];

        foreach ($lignes as $ligne) {
            $conversation = new Conversation();
            $conversation->setId($ligne['idConversation']);

            $utilisateur = new User($ligne['nomExpediteur'], "", "", "");
            $utilisateur->setId($ligne['idExpediteur']);

            $msg = new Message($ligne['contenu']);
            $msg->setId($ligne['idMessage']);
            $msg->setExpediteur($utilisateur);
            $msg->setConversation($conversation);
            $msg->setDateCreation($ligne['dateCreation']);

            $messages[] = $msg;
        }
        return $messages;
    }

    public function delete($idConv, $idUser)
    {
        $this->messageRepo->delete($idConv, $idUser);
    }
}