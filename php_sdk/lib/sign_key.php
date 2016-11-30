<?php 
require '../vendor/autoload.php';
require_once('config.php');
require_once('a2f_curl.php'); 

$a2f_client = new auth2factor($API_HOST, $API_KEY, $API_SECRET);

header("Content-type:application/json");

$req_token = $_SESSION['A2F_req_tokens']['x-app-sign-request'];

$ok = false;
if (isset($req_token) && isset($_POST['client_data']) && isset($_POST['signature_data'])) { 
  
    $resp = $a2f_client->validate_u2f($req_token, $_POST['client_data'], $_POST['signature_data']);

    if (isset($resp["status"])) {
      echo json_encode($resp);
      return;      
    } else {
      $_SESSION['2FA_SessionId'] = $resp;
      $ok = true;      
    }
} 

echo json_encode(array("ok" => $ok));
?>