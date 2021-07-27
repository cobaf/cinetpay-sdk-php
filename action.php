<?php
//Commenter ses deux lines si vous êtes en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// required libs
require_once __DIR__ . '/src/new-guichet.php';
include('commande.php');

// This class manage "Commande" table in DB
$commande = new Commande();
try {
    if(isset($_POST['valider']))
    {
        $customer_name = $_POST['customer_name'];
        $customer_surname = $_POST['customer_surname'];
        $description = $_POST['description'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
    }
    else{
        echo "Veuillez passer par le formulaire";
    }
    //transaction id
    $id_transaction = date("YmdHis"); // or $id_transaction = Cinetpay::generateTransId()
    //
   
    //Veuillez entrer votre apiKey
    $apikey = "12912847765bc0db748fdd44.40081707";
    //Veuillez entrer votre siteId
    $site_id = "445160";
    //version
    $version = "V2";
    //notify url
    $notify_url = 'https://support.cinetpay.com/';
    //return url
    $return_url = 'https://support.cinetpay.com/';
    //Channel list
    $channels = "ALL";
    // fill command class
    $commande->get_transactionId($id_transaction);
    $commande->setMontant($amount);
    
       
    // create for newGuichet
    $formData = array(
        "transaction_id"=> $id_transaction,
        "amount"=> $amount,
        "currency"=> $currency,
        "customer_surname"=> $customer_name,
        "customer_name"=> $customer_surname,
        "description"=> $description,
        "notify_url" => $notify_url,
        "return_url" => $return_url,
        "channels" => $channels
    );
    // save transaction in db
     $commande->create($formData);

    $CinetPay = new CinetPay($site_id, $apikey, $version);
    $result = $CinetPay->generatePaymentLink($formData);

    if ($result["code"] == '201')
    {
        $url = $result["data"]["payment_url"];
        //mise à jour de la base de donné
        $updateBd = array(
            "code" => $result["code"],
            "message" => $result["message"],
            "payment_token" => $result["data"]["payment_token"],
            "payment_url" => $url,
            "api_response_id" => $result["api_response_id"],
            "transaction_id"=> $id_transaction

        );
        include ('connection.php');
        // Mise à jour d'une ligne spécifique
        try {
            //code...
        $req = $bdd->prepare('UPDATE commande SET code = :code, message = :message,payment_token = :payment_token, payment_url = :payment_url,api_response_id = :api_response_id WHERE transaction_id = :transaction_id ');
        
        $req->execute($updateBd);
        
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        //redirection vers l'url
        header('Location:'.$url);
        
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

