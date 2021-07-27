<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=new-guichet', 'root', 'root');
    } catch (Exception $e) {
        //throw $th;
        die ('Erreur:'. $e->getMessage);
    }