<?php
namespace App\Entities;

class Message
{
    protected $id;
    protected $conversation;
    protected $expediteur;
    protected $contenu;
    protected $dateCreation;

    public function __construct($contenu)
    {
        $this->contenu = $contenu;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getConversation()
    {
        return $this->conversation;
    }
    public function getExpediteur()
    {
        return $this->expediteur;
    }
    public function getContenu()
    {
        return $this->contenu;
    }
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
    }
    public function setExpediteur($expediteur)
    {
        $this->expediteur = $expediteur;
    }
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }
}