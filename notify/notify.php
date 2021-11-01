
<?php 
if (isset($_POST['cpm_trans_id'])) {
    try {
    
    require_once __DIR__ . '/../src/new-guichet.php';
    require_once __DIR__ . '/../commande.php';

        //Something to write to txt log
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
        $apikey = "12912847765bc0db748fdd44.40081707";
        // siteId
        $site_id = "445160";
        //version 
        $version = "V2";

        $CinetPay = new CinetPay($site_id, $apikey, $version);
        //On recupère le statut de la transaction dans la base de donnée
        $commande->set_transactionId($id_transaction);
        $commande->getCommandeByTransId();//Il faut s'assurer que la transaction existe dans notre base de donnée
        // On verifie que la commande n'a pas encore été traité
        if ($commande->get_statut() == '00') {
            // La commande a été déjà traité
            // Arret du script
            die();
        }
        // Dans le cas contrait, on remplit notre ligne des nouvelles données acquise en cas de tentative de paiement sur CinetPay
       $CinetPay->setTransId($id_transaction)->getPayStatus();
       
        $payment_date = $CinetPay->chk_payment_date;
        $amount = $CinetPay->chk_amount;
        $currency = $CinetPay->chk_curreency;
        $message = $CinetPay->chk_message;
        $code = $CinetPay->chk_code;
        $metadata = $CinetPay->chk_metadata; 
        $api_response_id = $CinetPay->chk_api_response_id;
    
        $commande->set_prefix($phone_prefix);
        $commande->set_number($phone_number);
        $commande->set_datePaiement($payment_date);
        $commande->set_api_response_id($api_response_id);
        $commande->set_statut($code);
        $commande->set_transStatus($message);
        // On verifie que le montant payé chez CinetPay correspond à notre montant en base de données pour cette transaction
        if ($code == '00') { 
            // correct, we continue
             $commande->set_methode($CinetPay->chk_payment_method);
             $commande->set_operator_id($CinetPay->chk_operator_id);
            
        } else {
            // transaction n'est pas valide
        } 
        // mise à jour des transactions dans la base de donnée
        $commande->update();
        
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    // direct acces on IPN
    echo "Erreur constatée";
}