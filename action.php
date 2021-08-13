<?php
/*Commenter ses deux lines si vous êtes en production
error_reporting(E_ALL);
ini_set('display_errors', 1);*/

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
    $notify_url = $commande->getCurrentUrl().'cinetpay-sdk-php/notify/notify.php';//'http://cinetpay-sdk-php/notify';
    //return url
    $return_url = $commande->getCurrentUrl().'cinetpay-sdk-php/return/return.php';//'http://cinetpay-sdk-php/return';
    //Channel list
    $channels = "ALL";
       
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
        "channels" => $channels,
        //pour afficher le paiement par carte de credit
        "alternative_currency" => "EUR",
        "customer_email" => "test@cinetpay.com",
        "customer_phone_number" => "0505050505",
        "customer_address" => "BP 258",
        "customer_city" => "ABIDJAN",
        "customer_country" => "US",
        "customer_state" => "AZ",
        "customer_zip_code" => "+225"
    );
    // save transaction in db
     $commande->create();

    $CinetPay = new CinetPay($site_id, $apikey, $version);
    $result = $CinetPay->generatePaymentLink($formData);

    if ($result["code"] == '201')
    {
        $url = $result["data"]["payment_url"];

       // update transaction in db
        $commande->update();
        //redirection vers l'url
        header('Location:'.$url);
        
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

