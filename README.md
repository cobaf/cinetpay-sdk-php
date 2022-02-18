# CinetPay SDK PHP Integration(Nouveau guichet)

CinetPay SDK PHP Integration permet d'intégrer rapidement CinetPay à un site en ligne fait avec PHP.

## Prérequis
Avant de procéder à l’intégration du module de paiement, veuillez-vous assurer d’être en possession des éléments suivants :

    - Un compte marchand sur  [cinetpay.com](https://cinetpay.com/)
    - Votre api key, site ID et clé secret(disponible dans votre interface administration backend)

L'intégration de ce SDK se fait en deux etapes :

Pour utiliser pleinement le sdk, insérer vos informations dans le ficher `marchand.php`

```php
<?php
$marchand = array(
    "apikey" => "", // Enrer votre apikey
    "site_id" => "", //Entrer votre site_ID
    "secret_key" => "" //Entrer votre clé secret
);
```

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
```
### **Page de Notification(Hmac)**
Vous pouvez utiliser le HMAC sur votre url de notification

```php
<?php 

if (isset($_POST['cpm_trans_id'])) {
  
    try {
    
        require_once __DIR__ . '/../src/new-guichet.php';
        require_once __DIR__ . '/../commande.php';
        require_once __DIR__ . '/../marchand.php';

        /* Implementer le HMAC pour une vérification supplémentaire .*/
        //Etape 1 : Concatenation des informations posté
         $data_post = implode('',$_POST);

        //Etape 2 : Créer le token suivant la technique HMAC en appliquant l'algorithme SHA256 avec la clé secrète
        $generated_token = hash_hmac('SHA256', $data_post, $marchand["secret_key"]);

        if ($_SERVER["HTTP_X_TOKEN"])
        {
            $xtoken = $_SERVER["HTTP_X_TOKEN"];
        }
        else{
            $xtoken = "indisponible";
        }

        //Etape 3: Verifier que le token reçu dans l’en-tête correspond à celui que vous aurez généré.
        if(hash_equals($xtoken, $generated_token))
        {
            // Valid Token
            $validtoken = True;

            //Création d'un fichier log pour s'assurer que les éléments sont bien exécuté
            $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
                "TransId:".$_POST['cpm_trans_id'].PHP_EOL.
                "SiteId: ".$_POST['cpm_site_id'].PHP_EOL.
                "HMAC RECU: ".$xtoken.PHP_EOL.
                "HMAC GENERATE: ".$generated_token.PHP_EOL.
                "VALID-TOKEN: ".$validtoken.PHP_EOL.
                "-------------------------".PHP_EOL;

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

            $payment_date = $CinetPay->chk_payment_date;
            $amount = $CinetPay->chk_amount;
            $currency = $CinetPay->chk_currency;
            $message = $CinetPay->chk_message;
            $code = $CinetPay->chk_code;
            $metadata = $CinetPay->chk_metadata;

            //Enregistrement du statut dans le fichier log
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

        }
        else{
            echo "HMAC non-conforme";
            die();
        }


    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    // direct acces on IPN
    echo "cpm_trans_id non fourni";
}
```

### **Page de retour(Return_url)**
La page de retour est la page où est redirigée le client après une transaction sur CinetPay (quelque soit le statut de la transaction). Aucune mise à jour de la base de données ne doit être traité sur cette page

Exemple de page de retour :
```php
<?php
    require_once __DIR__ . '/../src/new-guichet.php';
    require_once __DIR__ . '/../commande.php';
    include('../marchand.php');

if (isset($_POST['transaction_id']) || isset($_POST['token'])) {

    $commande = new Commande();
    $id_transaction = $_POST['transaction_id'];

    try {
        // Verification d'etat de transaction chez CinetPay
        $CinetPay = new CinetPay($marchand["site_id"], $marchand["apikey"]);

        $CinetPay->getPayStatus($id_transaction, $marchand["site_id"]);
        $message = $CinetPay->chk_message;
        $code = $CinetPay->chk_code;

        //recuperer les info du clients pour personnaliser les reponses.
        /* $commande->getUserByPayment(); */

        // redirection vers une page en fonction de l'état de la transaction
        if ($code == '00') {
            echo 'Felicitation, votre paiement a été effectué avec succès';
            die();
        }
        else {
           // header('Location: '.$commande->getCurrentUrl().'/');
            echo 'Echec, votre paiement a échoué';
            die();
        }

    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    echo 'transaction_id non transmis';
    die();

}
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
    * invoice_data: Toute information supplementaire que vous voulez afficher sur la facture de CinetPay(Supporte trois variables que vous nommez à votre convenance)
    * metadata: Tout autre information complémentaires, généralement vous mettrez des valeurs dont vous aurez besoin pour identifier ou traiter facilement le paiement, exemple : la référence de la commande
    * lang : La langue par défaut du guichet de paiement (**fr, en**)
    * alternative_currency : Valeur de la transaction dans la devise choisie par le Marchand

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


Les données envoyées via mail sont traité sur le fichier **action.php**
 
```php
<?php
/*Commenter ses deux lines si vous êtes en production
error_reporting(E_ALL);
ini_set('display_errors', 1);*/

// required libs
require_once __DIR__ . '/src/new-guichet.php';
include('marchand.php');
include('commande.php');

// La class gère la table "Commande"( A titre d'exemple)
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

    //Veuillez entrer votre apiKey
    $apikey = $marchand["apikey"];
    //Veuillez entrer votre siteId
    $site_id = $marchand["site_id"];

    //notify url
    $notify_url = $commande->getCurrentUrl().'cinetpay-sdk-php/notify/notify.php';
    //return url
    $return_url = $commande->getCurrentUrl().'cinetpay-sdk-php/return/return.php';
    $channels = "ALL";

    //
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
        "metadata" => "", // utiliser cette variable pour recevoir des informations personnalisés.
        "alternative_currency" => "",//Valeur de la transaction dans une devise alternative
        //pour afficher le paiement par carte de credit
        "customer_email" => "", //l'email du client
        "customer_phone_number" => "", //Le numéro de téléphone du client
        "customer_address" => "", //l'adresse du client
        "customer_city" => "", // ville du client
        "customer_country" => "",//Le pays du client, la valeur à envoyer est le code ISO du pays (code à deux chiffre) ex : CI, BF, US, CA, FR
        "customer_state" => "", //L’état dans de la quel se trouve le client. Cette valeur est obligatoire si le client se trouve au États Unis d’Amérique (US) ou au Canada (CA)
        "customer_zip_code" => "" //Le code postal du client
    );
    // enregistrer la transaction dans votre base de donnée
    /*  $commande->create(); */

    $CinetPay = new CinetPay($site_id, $apikey);
    $result = $CinetPay->generatePaymentLink($formData);

    if ($result["code"] == '201')
    {
        $url = $result["data"]["payment_url"];

        // ajouter le token à la transaction enregistré
        /* $commande->update(); */
        //redirection vers l'url de paiement
        header('Location:'.$url);

    }
} catch (Exception $e) {
    echo $e->getMessage();
}
```
# Exemple Intégration

Vous trouverez un exemple d'intégration complet dans le dossier