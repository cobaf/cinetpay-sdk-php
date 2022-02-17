
<?php 


if (isset($_POST['cpm_trans_id'])) {
  
    try {
    
        require_once __DIR__ . '/../src/new-guichet.php';
        require_once __DIR__ . '/../commande.php';
        require_once __DIR__ . '/../marchand.php';

        //Création d'un fichier log pour s'assurer que les éléments sont bien exécuté
        $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
        "TransId:".$_POST['cpm_trans_id'].PHP_EOL.
        "SiteId: ".$_POST['cpm_site_id'].PHP_EOL.
        "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

        //La classe commande correspond à votre colonne qui gère les transactions dans votre base de données
        $commande = new Commande();
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        // apiKey
        $apikey = $marchand["apikey"];


        // siteId
        $site_id = $_POST['cpm_site_id'];


        $CinetPay = new CinetPay($site_id, $apikey);
        //On recupère le statut de la transaction dans la base de donnée
        /* $commande->set_transactionId($id_transaction);
             //Il faut s'assurer que la transaction existe dans notre base de donnée
         * $commande->getCommandeByTransId();
         */

        // On verifie que la commande n'a pas encore été traité
        $VerifyStatusCmd = "1"; // valeur du statut à recupérer dans votre base de donnée
        if ($VerifyStatusCmd == '00') {
            // La commande a été déjà traité
            // Arret du script
            die();
        }

        // Dans le cas contrait, on verifie l'état de la transaction en cas de tentative de paiement sur CinetPay

        $CinetPay->getPayStatus($id_transaction, $site_id);


        $amount = $CinetPay->chk_amount;
        $currency = $CinetPay->chk_currency;
        $message = $CinetPay->chk_message;
        $code = $CinetPay->chk_code;
        $metadata = $CinetPay->chk_metadata;

        //Something to write to txt log
        $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
            "Code:".$code.PHP_EOL.
            "Message: ".$message.PHP_EOL.
            "Amount: ".$amount.PHP_EOL.
            "currency: ".$currency.PHP_EOL.
            "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

        // On verifie que le montant payé chez CinetPay correspond à notre montant en base de données pour cette transaction
        if ($code == '00') {
            // correct, on delivre le service
            echo 'Felicitation, votre paiement a été effectué avec succès';
            die();

        } else {
            // transaction n'est pas valide
            echo 'Echec, votre paiement a échoué pour cause : ' .$message;
            die();
        }
        // mise à jour des transactions dans la base de donnée
        /*  $commande->update(); */

    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    // direct acces on IPN
    echo "cpm_trans_id non fourni";
}