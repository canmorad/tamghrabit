<?php
namespace App\Middleware;

use App\Helpers\Session;

class GuestMiddleware
{
    public function handle()
    {
        Session::start();
        $user = Session::get('user');
        if ($user) {
            if ($user->getRole()->getNom() === "admin") {
                header("Location: /Tamghrabit/admin/users");
                exit;
            }
            if ($user->getRole()->getNom() === "adherent") {
                header("Location: /Tamghrabit/profile/edit");
                exit;
            }
        }
    }
}