<?php
namespace App\Entities;

class ConversationUser
{
    protected $id;
    protected $conversation; 
    protected $user;   
    protected $estSupprime;
    protected $dateSupprimer;

    public function __construct($conversation, $user)
    {
        $this->conversation = $conversation;
        $this->user = $user;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getConversation()
    {
        return $this->conversation;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function getEstSupprime()
    {
        return $this->estSupprime;
    }
    public function getDateSupprimer()
    {
        return $this->dateSupprimer;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }
    public function setUser($user)
    {
        $this->user = $user;
    }
    public function setEstSupprime($estSupprime)
    {
        $this->estSupprime = $estSupprime;
    }
    public function setDateSupprimer($dateSupprimer)
    {
        $this->dateSupprimer = $dateSupprimer;
    }
}