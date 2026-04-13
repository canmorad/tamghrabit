<?php
use App\Core\Connection;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = Connection::getInstance();

try {

    echo "--- début du cron : " . date('Y-m-d H:i:s') . " ---\n";

    $stmt1 = $conn->prepare("update campagnes set status = 'terminee' where status = 'approuvee' and datefin < curdate()");
    $stmt1->execute();
    echo "- campagnes expirées (date) : " . $stmt1->rowCount() . " mises à jour.\n";

 
    $sqlAmount = "update campagnes c
                  join campagnesFinancieres cf on c.id = cf.id
                  set c.status = 'terminee'
                  where c.status = 'approuvee'
                  and cf.montantCollecte >= cf.objectifMontant";
                  
    $stmt2 = $conn->prepare($sqlAmount);
    $stmt2->execute();
    echo "- campagnes terminées (objectif atteint) : " . $stmt2->rowCount() . " mises à jour.\n";

    echo "--- fin du cron ---\n";

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/cron_errors.log', date('Y-m-d H:i:s') . " : " . $e->getMessage() . "\n", FILE_APPEND);
    echo "erreur : " . $e->getMessage();
}