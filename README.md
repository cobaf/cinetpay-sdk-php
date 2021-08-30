# CinetPay SDK PHP Integration(Nouveau guichet)

CinetPay SDK PHP Integration permet d'intégrer rapidement CinetPay à un site en ligne fait avec PHP.

## Prérequis
Avant de procéder à l’intégration du module de paiement, veuillez-vous assurer d’être en possession des éléments suivants :

    - Un compte marchand sur  [cinetpay.com](https://cinetpay.com/)
    - Votre api key et site ID (disponible dans votre interface administration backend)

L'intégration de ce SDK se fait en deux etapes :

## Etape 1 : Préparation des pages de notification et de retour

### **Page de Notification(Notify_url)**

Pour ceux qui possèdent des services qui ne necessitent pas un traitement des notifications de paiement de CinetPay, vous pouvez passer directement à la phase suivante, par exemple les services de don.

A chaque paiement, CinetPay vous notifie via un lien de notification.

NB :

    -C'est un lien silencieux
    -C'est le seul lien qui est abilité à mettre à jour les informations de la base de donnée relatif à la transaction

Exemple :
```php 
<?php 
if (isset($_POST['cpm_trans_id'])) {
    try {
    
    require_once __DIR__ . '/../src/new-guichet.php';
    require_once __DIR__ . '/../commande.php';

        //La classe commande correspond à votre colonne qui gère les transactions dans votre base de données
        $commande = new Commande();
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        
        $apikey = "YOUR APIKEY";
        
        $site_id = "YOUR SITE_ID";
        
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
        $phone_number = $CinetPay->chk_phone_number;
        $phone_prefix = $CinetPay->chk_phone_prefix;
        $message = $CinetPay->chk_message;
        $code = $CinetPay->chk_code;
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
?>
```
### **Page de retour(Return_url)**
La page de retour est la page où est redirigée le client après une transaction sur CinetPay (quelque soit le statut de la transaction). Aucune mise à jour de la base de données ne doit être traité sur cette page

Exemple de page de retour :
```php
<?php

if (isset($_POST['payment_token'])) {
    try {
    require_once __DIR__ . '/../commande.php';
    $commande = new Commande();
        $payment_token = $_POST['payment_token'];
          // Recupere le nom du payeur
          $commande->set_payment_token($payment_token);
          $commande->getUserByPayment();
          // on verifie le statut de la commande
          if ($commande->get_statut() == '00') {
            echo 'Felicitation, votre paiement a été effectué avec succès';
            die();
          }
          else {
            echo 'Echec, votre paiement a échoué';
        }
 
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    echo "Token non trouvé";
    die();
}
?>
```

## Etape 2 : Préparation et affichage du guichet de paiement

Pour initialiser un paiement vous devez generer un lien de paiement en envoyer les informations suivantes en JSON.

    * apikey : votre apikey
    * site_id : votre site_id
    * transaction_id : Identification de la transaction (unique) amount : Le montant de la transaction
    * currency : La devise monétaire (XOF, XAF, CDF, GNF) description : Description du paiement en cours customer_name: Le nom du client
    * customer_surname: Le prénom du client
    * notify_url: Le lien de notification du paiement
    * return_url: Le lien où le client sera redirigé après le paiement
    * channels:  les moyens de paiements qu'on souhaite afficher 

Pour afficher l'option paiement par carte bancaire, vous devez ajouter aux informations précédentes ces informations optionnelles:

    + alternative_currency: devise de conversion
    + customer_email: l'email du client,
    + customer_phone_number: numéro du client,
    + customer_address" : l'adresse du client,
    + customer_city" : nom de la ville,
    + customer_country" : code du pays,
    + customer_state" : nom de l’état ,
    + customer_zip_code" : prefixe du numéro 

Après avoir obtenu l’url de paiement à la requête précédente, il vous suffit juste de lancer cette url dans un navigateur web pour vous retrouver sur le guichet de paiement.    

Pour plus de sécurité, Il faut enregistrer les informations sur le paiement dans la base de données avant d'afficher le guichet.

Exemple:
```php
<?php
/*Commenter ses deux lines si vous êtes en production*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclusion des classes necessaires
require_once __DIR__ . '/src/new-guichet.php';
include('commande.php');
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
    $id_transaction = date("YmdHis");
    
    //Veuillez entrer votre apiKey
    $apikey = "12912847765bc0db748fdd44.40081707";
    //Veuillez entrer votre siteId
    $site_id = "445160";
    //version
    $version = "V2";
    //notify url
    $notify_url = 'NOTIFY_URL';
    //return url
    $return_url = 'RETURN_URL';
    //liste des moyens de paiement
    $channels = ["MOBILE_MONEY", "CREDIT_CARD", "WALLETS"];
       
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
        "customer_country" => "CI",
        "customer_state" => "AZ",
        "customer_zip_code" => "00225"
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
?>
```
# Exemple Intégration

Vous trouverez un exemple d'intégration complet dans le dossier