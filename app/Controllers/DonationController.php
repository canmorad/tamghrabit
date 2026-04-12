<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Connection;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class DonationController extends Controller
{
    private $conn;

    // DonationController.php
    public function __construct()
    {
        parent::__construct();
        $this->conn = Connection::getInstance();

        // الساروت ديالك
        \Stripe\Stripe::setApiKey('sk_test_51TKmla3D3IP9ZOhr5MtnHeOW3dxRi6wfQ6tuHfp0n3GrmeV4A19ghQFCvXEcIzTnvQjr0j3qXLk07d7X6udB8Val00QQ3Uz8Lv');

        $curl = new \Stripe\HttpClient\CurlClient([
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_SSL_VERIFYPEER => false, // تجاهل التحقق مؤقتاً للتأكد من الاتصال
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80
        ]);
        \Stripe\ApiRequestor::setHttpClient($curl);
    }

    public function checkout()
    {
        $campagneId = $_POST['campagne_id'];
        $amount = $_POST['amount'];

        try {
            // صاوب الجلسة في Stripe
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'mad', // الدرهم المغربي
                            'product_data' => [
                                'name' => 'Soutien à la campagne #' . $campagneId,
                            ],
                            'unit_amount' => $amount * 100, // Stripe كيحسب بالسنتيم
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                // فين يرجع المستخدم إلا نجح الدفع أو لغاه
                'success_url' => "http://localhost/Tamghrabit/donation/success?session_id={CHECKOUT_SESSION_ID}&campagne_id=$campagneId",
                'cancel_url' => "http://localhost/Tamghrabit/campagne/show?id=$campagneId",
            ]);

            // صيفط المستخدم لـ Stripe
            header("HTTP/1.1 303 See Other");
            header("Location: " . $checkout_session->url);
            exit();
        } catch (\Exception $e) {
            echo "Erreur Stripe: " . $e->getMessage();
        }
    }

    public function success()
    {
        $sessionId = $_GET['session_id'];
        $campagneId = $_GET['campagne_id'];

        // هنا خاصك تأكد الدفع وتزيد المبلغ في الباز دو دوني
        $session = Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $amountPaid = $session->amount_total / 100;

            try {
                $this->conn->beginTransaction();

                // 1. سجل التبرع في جدول donations
                $stmtDon = $this->conn->prepare("INSERT INTO donations (idCampagne, idAdherent, montant, idTransactionStripe, status) VALUES (?, ?, ?, ?, 'complete')");
                $stmtDon->execute([
                    $campagneId,
                    $_SESSION['user_id'], // خاص يكون User connecté
                    $amountPaid,
                    $sessionId
                ]);

                // 2. حدث مبلغ الـ campagne
                $stmtUpdate = $this->conn->prepare("UPDATE campagnesFinancieres SET montantCollecte = montantCollecte + ? WHERE id = ?");
                $stmtUpdate->execute([$amountPaid, $campagneId]);

                $this->conn->commit();

                // صيفطو لصفحة الشكر
                return $this->view('donation/success', ['amount' => $amountPaid]);

            } catch (\Exception $e) {
                $this->conn->rollBack();
                die("Erreur base de données: " . $e->getMessage());
            }
        }
    }
}