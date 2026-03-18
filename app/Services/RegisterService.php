<?php
namespace App\Services;
use App\Repositories\RegisterRepository;
class RegisterService
{
    private $registerRepository;
    public function __construct($conn)
    {
        $this->registerRepository = new RegisterRepository($conn);
    }

    public function store($adherent)
    {
        $adherent->setPassword(password_hash($adherent->getPassword(), PASSWORD_DEFAULT));
        $this->registerRepository->store($adherent);
    }
}