<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Credentials
//$apikey ='146581772860f5d2009f16f6.14710950';
$apikey ='12912847765bc0db748fdd44.40081707';//demo cinetpay
//$cpm_site_id = 531397;
$cpm_site_id = '445160';//demo cinetpay

//Post Parameters
$cpm_version = 'v1';
$cpm_language = 'fr';
$cpm_currency = $_POST['currency']; // value can also be : XOF, XAF, CDF, GNF
$cpm_page_action = 'PAYMENT';
$cpm_payment_config = 'SINGLE';
$cpSecure = "https://secure.cinetpay.com";
$signatureUrl = "https://api.cinetpay.com/v1/?method=getSignatureByPost";
/////////////////////////////

$cpm_amount = $_POST['amount']; //Le montant de la transaction
$cpm_custom = ''; // Toutes information qui pourrais vous servir
$cpm_designation = 'PAIEMENTTEST'; //Le produit acheter


$cpm_trans_date = date("Y-m-d H:i:s");
$cpm_trans_id = 'TEST-' . (string)date("YmdHis"); // ID d la transaction
//notify url
$notify_url = 'http://15.188.62.100/cinetpay-sdk-php/notify/notify.php';
//return url
$return_url = 'http://15.188.62.100/cinetpay-sdk-php/return/return.php';
$cancel_url = "https://exemple.net/cancel";
$debug = 1;


//Data that will be send in the form
$getSignatureData = array(
    'apikey' => $apikey,
    'cpm_amount' => $cpm_amount,
    'cpm_custom' => $cpm_custom,
    'cpm_site_id' => $cpm_site_id,
    'cpm_version' => $cpm_version,
    'cpm_currency' => $cpm_currency,
    'cpm_trans_id' => $cpm_trans_id,
    'cpm_language' => $cpm_language,
    'cpm_trans_date' => $cpm_trans_date,
    'cpm_page_action' => $cpm_page_action,
    'cpm_designation' => "",
    'cpm_payment_config' => $cpm_payment_config,
    'update_time' => 10,
    'notify_url' => 'http://15.188.62.100/cinetpay-sdk-php/notify/notify.php'
    
    /* 'cel_phone_num' => '0198757845',
    'cpm_phone_prefixe' => '225',  */
    /* 'debug' => 1*/
);
// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'method' => "POST",
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($getSignatureData)
        ),
       "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
);

$context = stream_context_create($options);
$result = file_get_contents($signatureUrl, false, $context);
if ($result === false) {
    /* Handle error */
    \header($return_url);
    exit();
}
$signature = json_decode($result);
var_dump($signature);

?>

<!DOCTYPE html>
<html>

<body>
    <p>Veuillez patientez...</p>
    <form action="<?php echo $cpSecure; ?>" method="post" id="cinetPayForm">
        <input type="hidden" value="<?php echo $apikey; ?>" name="apikey">
        <input type="hidden" value="<?php echo $cpm_custom; ?>" name="cpm_custom">
        <p><input type="hidden" value="<?php echo $cpm_amount; ?>" name="cpm_amount"></p>
        <input type="hidden" value="<?php echo $cpm_site_id; ?>" name="cpm_site_id">
        <p><input type="hidden" value="<?php echo $cpm_currency; ?>" name="cpm_currency"></p>
        <input type="hidden" value="<?php echo $cpm_version; ?>" name="cpm_version">
        <input type="hidden" value="<?php echo $cpm_trans_id; ?>" name="cpm_trans_id">
        <input type="hidden" value="<?php echo $cpm_language; ?>" name="cpm_language">
        <input type="hidden" value="<?php echo $getSignatureData['cpm_trans_date']; ?>" name="cpm_trans_date">
        <input type="hidden" value="<?php echo $cpm_page_action; ?>" name="cpm_page_action">
        <p><input type="hidden" name="cpm_designation"> </p>
        <input type="hidden" value="<?php echo $cpm_payment_config; ?>" name="cpm_payment_config">
<input type="hidden" value="<?php echo $cpm_payment_config; ?>" name="cpm_payment_config">
<!--   <input type="text" value="0198757845" name="cel_phone_num">
<input type="number" value="225" name="cpm_phone_prefixe"> -->
        <input type="hidden" value="<?php echo $signature; ?>" name="signature">
        <input type="hidden" value="<?php echo $return_url; ?>" name="return_url">
        <input type="hidden" value="<?php echo $cancel_url; ?>" name="cancel_url">
        <input type="hidden" value="<?php echo $notify_url; ?>" name="notify_url">
       <input type="hidden" value="<?php echo $debug; ?>" name="debug">
     <!--    <input type="submit" value="Valider"> -->
    </form>
    <script>
        document.getElementById("cinetPayForm").submit();
    </script>
</body>
</html>
