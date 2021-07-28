<?php
// IPN acces for payment validation
error_reporting(E_ALL);
ini_set('display_errors', 1);

//check if there is a cinetpay post value
if (isset($_POST['transaction_id'])) {
    // call required lib
    require_once __DIR__ . '/../src/new-guichet.php';

    // sample class for simulate payment validation
    require_once __DIR__ . '/../commande.php';

    $commande = new Commande();
    try {
        // cinetpay class initialisation and transaction identify
        $id_transaction = $_POST['transaction_id'];
        // enter apiKey
        $apikey = "12912847765bc0db748fdd44.40081707";
        //enter siteId
        $site_id = "445160";
        //version 
        $version = "V2";

        $CinetPay = new CinetPay($site_id, $apikey, $version);
        // get actual transaction's status in your db
        $commande->set_transactionId($id_transaction);
        $commande->getCommandeByTransId();
        // check if transaction is already validated
        if ($commande->get_statut() == '00') {
            // transaction is already validated, don't do anything
            die();
        }
        // get correct values for this transactions
        $CinetPay->setTransId($id_transaction)->getPayStatus();

        $payment_date = $CinetPay->chk_payment_date;
        $phone_number = $CinetPay->chk_phone_number;
        $phone_prefix = $CinetPay->chk_phone_prefix;
        $message = $CinetPay->chk_message;
        $code = $CinetPay->chk_code;
        $api_response_id = $CinetPay->chk_api_response_id;

        // set news values in the class
        $commande->set_prefix($phone_prefix);
        $commande->set_number($phone_number);
        $commande->set_datePaiement($payment_date);
        $commande->set_api_response_id($api_response_id);
        $commande->set_statut($code);
        $commande->set_transStatus($message);
        // check if amount of transaction correspond of the amount in our db
        if ($code == '00') {
            // correct, we continue
             $commande->set_methode($CinetPay->chk_payment_method);
             $commande->set_operator_id($CinetPay->chk_operator_id);
            
        } else {
            // transaction is not valid
        } 
        // update transaction in our db
        $commande->update();
        echo "1"; // a retirer
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    // direct acces on IPN
}