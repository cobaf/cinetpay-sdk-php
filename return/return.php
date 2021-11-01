<?php

if (isset($_POST['transaction_id'])) {
    try {
    require_once __DIR__ . '/../commande.php';
    $commande = new Commande();
          $id_transaction = $_POST['transaction_id'];
          // get buyer name in your db
          $commande->set_transactionId($id_transaction);
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
    header('Location:'.$commande->getCurrentUrl().'cinetpay-sdk-php/');
    die();
}