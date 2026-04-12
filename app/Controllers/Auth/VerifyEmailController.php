<?php
namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Connection;
use App\Helpers\Session;

class VerifyEmailController extends Controller
{
    public function verify()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            header("Location: /Tamghrabit/login");
            exit;
        }

        $db = Connection::getInstance();

        $stmt = $db->prepare("select id from users where tokenVerification = ? limit 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $update = $db->prepare("update users set estVerifieGmail = true, tokenVerification = null where id = ?");
            $update->execute([$user['id']]);

            Session::start();
             Session::flush("success", "Votre adresse email a été vérifiée avec succès. Vous pouvez maintenant vous connecter.");
            header("Location: /Tamghrabit/login");
        } else {
            header("Location: /Tamghrabit/login");
        }
        exit;
    }
}