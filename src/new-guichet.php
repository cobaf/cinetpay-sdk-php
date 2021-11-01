<?php
/**
 * CinetPay
 *
 * LICENSE
 *
 * This source file is subject to the MIT License that is bundled
 * with this package in the file LICENSE.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@cinetpay.com so we can send you a copy immediately.
 * @category cinetpay
 * @package  cinetpay
 * @license  MIT 
 * @version  
 */

 /**
  * Cinetpay
  * @category cinetpay
  * @package  cinetpay
  * @copyright Copyright (c) 2015-2021 CinetPay Inc. (https://www.cinetpay.com)
  */

  class CinetPay 
  {
      protected $BASE_URL = null; //generer lien de paiement Pour la production

      //Variable obligatoire identifiant
    /**
     * An identifier
     * @var string
     */
    
      public $amount = null ;
      public $apikey = null ;
      public $site_id = null;
      public $currency = 'XOF';
      public $transaction_id = null;
      public $customer_name = null;
      public $customer_surname = null;
      public $description = null;

      //Variable facultative identifiant
      public $channels = 'ALL';
      public $notify_url= null;
      public $return_url= null;

      //toute les variables 
      public $metadata = null;
      public $alternative_currency = null;
      public $customer_email = null;
      public $customer_phone_number = null;
      public $customer_address = null;
      public $customer_city = null;
      public $customer_country = null;
      public $customer_state = null;
      public $customer_zip_code = null; 

      //variables des payments check
      public $token = null;
      public $chk_payment_date = null;
      public $chk_operator_id = null;
      public $chk_payment_method = null;
      public $chk_code = null;
      public $chk_message = null;
      public $chk_api_response_id = null;
      public $chk_description = null;
      public $chk_amount = null;
      public $chk_currency = null;
      public $chk_metadata = null;
      /**
     * CinetPay constructor.
     * @param $site_id
     * @param $apikey
     * @param string $version
     * @param array $params
     */
      public function __construct($site_id, $apikey, $version = 'v2', $params = null)
      {
        $this->BASE_URL = sprintf('https://api-checkout.cinetpay.com/%s/payment',strtolower($version)); 
        $this->apikey = $apikey;
        $this->site_id = $site_id;
      }

      //generer lien de payment
      public function generatePaymentLink($param)
      {
        $this->CheckDataExist($param, "payment");
        //champs obligatoire
        $this->transaction_id = $param['transaction_id'];
        $this->amount = $param['amount'];
        $this->currency = $param['currency'];
        $this->description = $param['description'];
        //champs quasi obligatoire
        $this->customer_name = $param['customer_name'];
        $this->customer_surname = $param['customer_surname'];
        //champs facultatif
        if (!empty($param['notify_url'])) $this->notify_url = $param['notify_url'];
        if (!empty($param['return_url'])) $this->return_url = $param['return_url'];
        if (!empty($param['channels'])) $this->channels = $param['channels'];
        //exception pour le CREDIT_CARD
        if ($this->channels == "CREDIT_CARD"  )
        $this->checkDataExist($param, "paymentCard");

      if (!empty($param['alternative_currency'])) $this->alternative_currency = $param['alternative_currency'];
      if (!empty($param['customer_email']))  $this->customer_email = $param['customer_email'];
      if (!empty($param['customer_phone_number']))  $this->customer_phone_number = $param['customer_phone_number'];
      if (!empty($param['customer_address']))  $this->customer_address = $param['customer_address'];
      if (!empty($param['customer_city']))  $this->customer_city = $param['customer_city'];
      if (!empty($param['customer_country']))  $this->customer_country = $param['customer_country'];
      if (!empty($param['customer_state']))  $this->customer_state = $param['customer_state'];
      if (!empty($param['customer_zip_code']))  $this->customer_zip_code = $param['customer_zip_code'];
      if (!empty($param['metadata']))  $this->metadata = $param['metadata'];
        //soumission des donnees
        $data = $this->getData();
        
        $flux_json = $this->callCinetpayWsMethod($data, $this->BASE_URL);
        if ( $flux_json == false)
        throw new Exception("Un probleme est survenu lors de l'appel du WS !");

        $paymentUrl = json_decode($flux_json,true);

        if(is_array($paymentUrl))
        {
          if(empty($paymentUrl['data']))
          {
            $message = 'Une erreur est survenue, Code: ' . $paymentUrl['code'] . ', Message: ' . $paymentUrl['message'] . ', Description: ' . $paymentUrl['description'];

            throw new Exception($message);
          }
          
          
        }
        
        return $paymentUrl;
      }

      //check data
      public function CheckDataExist($param, $action)// a customiser pour la function check status
      {
        if (empty($this->apikey))
        throw new Exception("Erreur: Apikey non defini");
        if (empty($this->site_id))
        throw new Exception("Erreur: Site_id non defini");
        if (empty($param['transaction_id']))
        $this->transaction_id = $this->generateTransId();

        if($action == "payment")
        {
          if (empty($param['amount']))
          throw new Exception("Erreur: Amount non defini");
          if (empty($param['currency']))
          throw new Exception("Erreur: Currency non defini");
          if (empty($param['customer_name']))
          throw new Exception("Erreur: Customer_name non defini");
          if (empty($param['description']))
          throw new Exception("Erreur: description non defini");
          if (empty($param['customer_surname']))
          throw new Exception("Erreur: Customer_surname non defini");
          if (empty($param['notify_url']))
          throw new Exception("Erreur: notify_url non defini");
          if (empty($param['return_url']))
          throw new Exception("Erreur: return_url non defini");
        }
        elseif ($action == "paymentCard") 
        {
          if (empty($param['customer_email']))
          throw new Exception("Erreur: customer_email non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_phone_number']))
          throw new Exception("Erreur: custom_phone_number non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_address']))
          throw new Exception("Erreur: Customer_address non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_city']))
          throw new Exception("Erreur: customer_city non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_country']))
          throw new Exception("Erreur: customer_country non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_state']))
          throw new Exception("Erreur: Customer_address non defini (champs requis pour le paiement par carte)");
          if (empty($param['customer_zip_code']))
          throw new Exception("Erreur: customer_zip_code non defini (champs requis pour le paiement par carte)");
        }
        
      }
      
      //send datas
      private function callCinetpayWsMethod($params, $url, $method = 'POST')
      {
        
          if (function_exists('curl_version')) {
              try {
                  $curl = curl_init();
                 
                  curl_setopt_array($curl, array(
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 45,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => $method,
                      CURLOPT_POSTFIELDS => json_encode($params),
                      CURLOPT_SSL_VERIFYPEER => 0,
                      CURLOPT_HTTPHEADER => array(
                          "content-type:application/json"
                      ),
                  ));
                  $response = curl_exec($curl);
                  $err = curl_error($curl);
                  curl_close($curl);
                  if ($err) {
                      throw new Exception("Error :" . $err);
                  } else {
                      return $response;
                  }
              } catch (Exception $e) {
                  throw new Exception($e);
              }
          }  else {
              throw new Exception("Vous devez activer curl ou allow_url_fopen pour utiliser CinetPay");
          }
      }
      //getData
      public function getData()
      {
        $dataArray = array(
          "amount"=> $this->amount,
          "apikey"=> $this->apikey,
          "site_id"=> $this->site_id,
          "currency"=> $this->currency,
          "transaction_id"=> $this->transaction_id,
          "customer_surname"=> $this->customer_surname,
          "customer_name"=> $this->customer_name,
          "description"=> $this->description,
          "notify_url"=> $this->notify_url,
          "return_url"=> $this->return_url,
          "channels"=> $this->channels,
          "alternative_currency"=> $this->alternative_currency,
          "customer_email"=> $this->customer_email,
          "customer_phone_number"=> $this->customer_phone_number,
          "customer_address"=> $this->customer_address,
          "customer_city"=> $this->customer_city,
          "customer_country"=> $this->customer_country,
          "customer_state"=> $this->customer_state,
          "customer_zip_code"=> $this->customer_zip_code,
          "metadata" => $this->metadata,
        );
        return $dataArray;
      }
      //get payStatus
      public function getPayStatus()
      {
        $data = (array)$this->getPayStatusArray();
        
        $flux_json = $this->callCinetpayWsMethod($data, $this->BASE_URL."/check");

       
        if ( $flux_json == false)
        throw new Exception("Un probleme est survenu lors de l'appel du WS !"); 
        
        $StatusPayment = json_decode($flux_json, true);

        if(is_array($StatusPayment))
        {
          if(empty($StatusPayment['data']))
          {
            $message = 'Une erreur est survenue, Code: ' . $StatusPayment['code'] . ', Message: ' . $StatusPayment['message'] . ', Description: ' . $StatusPayment['description'];

            throw new Exception($message);
          }
          
        }
        $this->chk_payment_date = $StatusPayment['data']['payment_date'];
        $this->chk_operator_id = $StatusPayment['data']['operator_id'];
        $this->chk_payment_method = $StatusPayment['data']['payment_method'];
        $this->chk_amount = $StatusPayment['data']['amount'];
        $this->chk_currency = $StatusPayment['data']['currency'];
        $this->chk_code = $StatusPayment['code'];
        $this->chk_message = $StatusPayment['message'];
        $this->chk_api_response_id = $StatusPayment['api_response_id'];
        $this->chk_description = $StatusPayment['data']['description'];
        $this->chk_metadata = $StatusPayment['data']['metadata'];
      }
      private function getPayStatusArray()
       {
          return $dataArray = array(
            'apikey' => $this->apikey,
            'site_id' => $this->site_id,
            'transaction_id' => $this->transaction_id);

       }
      //generate transId
      /**
       * @return int
       */
      public function generateTransId()
      {
        $timestamp = time();
        $parts = explode(' ', microtime());
        $id = ($timestamp + $parts[0] - strtotime('today 00:00')) * 10;
        $id = sprintf('%06d', $id) . mt_rand(100, 9999);

        return $id;
      }
      /**
       * @param $id
       * @return $this
       */
      public function setTransId($id)
      {
          $this->transaction_id = $id;
          return $this;
      }

      public function setToken($token)
      {
          $this->token= $token;
          return $this;
      }

  }

  
  