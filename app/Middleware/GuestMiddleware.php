<?php
namespace App\Middleware;

use App\Helpers\Session;

class GuestMiddleware
{
    public function handle()
    {
        Session::start();

        if (Session::get('user')) {
            header('Location: /accueil');
            exit();
        }
    }
}