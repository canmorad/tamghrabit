<?php
namespace App\Repositories;

use PDO;
use App\Entities\Message;

class MessageRepository
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getConversationsByUserId($userId)
    {
        $sql = "select 
                c.id as idConversation,
                u.id as idDestinataire,
                u.nom as nomDestinataire,
                u.prenom as prenomDestinataire,
                u.imageProfile as imageDestinataire,
                m.contenu as dernierMessage,
                m.dateCreation as dateDernierMessage,
                m.idExpediteur as idDernierExpediteur
            from conversations c
            join conversationUsers cu1 on c.id = cu1.idConversation and cu1.idUser = :userId
            join conversationUsers cu2 on c.id = cu2.idConversation and cu2.idUser != :userId
            join users u on cu2.idUser = u.id
            left join messages m on m.id = (
                select id from messages 
                where idConversation = c.id 
                order by dateCreation desc limit 1
            )
            where cu1.estSupprime = false
            order by m.dateCreation desc";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($idConv, $idUser)
    {
        $sql = "
                update conversationUsers 
                set estSupprime = true, dateSupprimer = NOW() 
                where idConversation = :idConv 
                and idUser = :idUser
            ";

        $this->conn->prepare($sql)->execute([
            "idConv" => $idConv,
            "idUser" => $idUser
        ]);
    }
    public function save($message)
    {
        $sql = "insert into messages (idConversation, idExpediteur, contenu) 
                values (:idConv, :idExp, :content)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'idConv' => $message->getConversation()->getId(),
            'idExp' => $message->getExpediteur()->getId(),
            'content' => $message->getContenu(),
        ]);

        $message->setId($this->conn->lastInsertId());
        return $message;
    }

    public function findByConversationId($idConv)
    {
        $sql = "select m.id as idMessage, m.idConversation, m.idExpediteur, 
                       m.contenu, m.dateCreation, u.nom as nomExpediteur 
                from messages m 
                join users u on m.idExpediteur = u.id 
                where m.idConversation = :idConv 
                order by m.dateCreation asc";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['idConv' => $idConv]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findConversationBetweenUsers($user1, $user2)
    {
        $sql = "select cu1.idConversation 
                from conversationUsers cu1
                join conversationUsers cu2 on cu1.idConversation = cu2.idConversation
                where cu1.idUser = :u1 and cu2.idUser = :u2 
                limit 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['u1' => $user1, 'u2' => $user2]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createNewConversation($user1, $user2)
    {
        $this->conn->prepare("insert into conversations (datecreation) values (now())")->execute();
        $idConv = $this->conn->lastInsertId();

        $sql = "insert into conversationUsers (idConversation, idUser) values (:idConv, :idUser)";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute(['idConv' => $idConv, 'idUser' => $user1]);
        $stmt->execute(['idConv' => $idConv, 'idUser' => $user2]);

        return $idConv;
    }
}