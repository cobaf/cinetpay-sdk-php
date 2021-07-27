<?php

class Commande
{
    protected $_montant;
    protected $_transactionId;
    protected $_methode;
    protected $_transStatus;
    protected $_statut;
    protected $_dateCreation;
    protected $_dateModification;
    protected $_datePaiement;
    protected $_operator_id;
    protected $_number;
    protected $_prefix;
    protected $_api_response_id;

    public function create($data)
    {
        include ('connection.php');
        // Enregister la ligne pour la première fois
        $req = $bdd->prepare('INSERT INTO commande(transaction_id,amount,currency,customer_surname,customer_name,description,notify_url,return_url,channels) 
                     VALUES( :transaction_id, :amount, :currency, :customer_surname, :customer_name, :description, :notify_url, :return_url, :channels)');
        $req->execute($data);

    }

    public function update($updateBd)
    {
        include ('connection.php');
        // Mise à jour d'une ligne spécifique
        $req = $bdd->prepare('UPDATE commande SET code = :code, message = :message, payment_token = :payment_token, payment_url = :payment_url, api_response_id = :api_response_id WHERE transaction_id = :transaction_id ');
        $req->execute($updateBd);
    }

    public function getCommandeByTransId()
    {
        // Recuperation d'une commande par son $_transId
    }

    /**
     * @return mixed
     */
    public function getMontant()
    {
        return $this->_montant;
    }

    /**
     * @param mixed $montant
     */
    public function setMontant($montant)
    {
        $this->_montant = $montant;
    }




    /**
     * Get the value of _transactionId
     */ 
    public function get_transactionId()
    {
        return $this->_transactionId;
    }

    /**
     * Set the value of _transactionId
     *
     * @return  self
     */ 
    public function set_transactionId($_transactionId)
    {
        $this->_transactionId = $_transactionId;

        return $this;
    }

    /**
     * Get the value of _dateCreation
     */ 
    public function get_dateCreation()
    {
        return $this->_dateCreation;
    }

    /**
     * Set the value of _dateCreation
     *
     * @return  self
     */ 
    public function set_dateCreation($_dateCreation)
    {
        $this->_dateCreation = $_dateCreation;

        return $this;
    }

    /**
     * Get the value of _dateModification
     */ 
    public function get_dateModification()
    {
        return $this->_dateModification;
    }

    /**
     * Set the value of _dateModification
     *
     * @return  self
     */ 
    public function set_dateModification($_dateModification)
    {
        $this->_dateModification = $_dateModification;

        return $this;
    }

    /**
     * Get the value of _number
     */ 
    public function get_number()
    {
        return $this->_number;
    }

    /**
     * Set the value of _number
     *
     * @return  self
     */ 
    public function set_number($_number)
    {
        $this->_number = $_number;

        return $this;
    }


    /**
     * Get the value of _statut
     */ 
    public function get_statut()
    {
        return $this->_statut;
    }

    /**
     * Set the value of _statut
     *
     * @return  self
     */ 
    public function set_statut($_statut)
    {
        $this->_statut = $_statut;

        return $this;
    }

    /**
     * Get the value of _datePaiement
     */ 
    public function get_datePaiement()
    {
        return $this->_datePaiement;
    }

    /**
     * Set the value of _datePaiement
     *
     * @return  self
     */ 
    public function set_datePaiement($_datePaiement)
    {
        $this->_datePaiement = $_datePaiement;

        return $this;
    }

    /**
     * Get the value of _methode
     */ 
    public function get_methode()
    {
        return $this->_methode;
    }

    /**
     * Set the value of _methode
     *
     * @return  self
     */ 
    public function set_methode($_methode)
    {
        $this->_methode = $_methode;

        return $this;
    }

    /**
     * Get the value of _operator_id
     */ 
    public function get_operator_id()
    {
        return $this->_operator_id;
    }

    /**
     * Set the value of _operator_id
     *
     * @return  self
     */ 
    public function set_operator_id($_operator_id)
    {
        $this->_operator_id = $_operator_id;

        return $this;
    }

    /**
     * Get the value of _prefix
     */ 
    public function get_prefix()
    {
        return $this->_prefix;
    }

    /**
     * Set the value of _prefix
     *
     * @return  self
     */ 
    public function set_prefix($_prefix)
    {
        $this->_prefix = $_prefix;

        return $this;
    }


    /**
     * Get the value of _transStatus
     */ 
    public function get_transStatus()
    {
        return $this->_transStatus;
    }

    /**
     * Set the value of _transStatus
     *
     * @return  self
     */ 
    public function set_transStatus($_transStatus)
    {
        $this->_transStatus = $_transStatus;

        return $this;
    }

    /**
     * Get the value of _api_response_id
     */ 
    public function get_api_response_id()
    {
        return $this->_api_response_id;
    }

    /**
     * Set the value of _api_response_id
     *
     * @return  self
     */ 
    public function set_api_response_id($_api_response_id)
    {
        $this->_api_response_id = $_api_response_id;

        return $this;
    }
}