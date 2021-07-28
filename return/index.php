<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['payment_token'])) {
    require_once __DIR__ . '/../commande.php';
    $commande = new Commande();
    try {
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
            echo 'Echec, votre paiement a échoué';
            die();
        }
 
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    header('Location: /');
    die();
}