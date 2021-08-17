<?php

if (isset($_POST['payment_token'])) {
    try {
    require_once __DIR__ . '/../commande.php';
    $commande = new Commande();
        $payment_token = $_POST['payment_token'];
          // get buyer name in your db
          $commande->set_payment_token($payment_token);
          $commande->getUserByPayment();
          // check if payment token is already validated
          if ($commande->get_statut() == '00') {
            echo 'Felicitation, votre paiement a été effectué avec succès';
            die();
          }
          else {
            header('Location: '.$commande->getCurrentUrl().'cinetpay-sdk-php/');
            echo 'Echec, votre paiement a échoué';
        }
 
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    header('Location: '.$commande->getCurrentUrl().'cinetpay-sdk-php/');
    die();
}