<?php 
require '../vendor/autoload.php';
require_once('config.php');  
require_once('a2f_curl.php'); 

$a2f_client = new auth2factor($API_HOST, $API_KEY, $API_SECRET);

header("Content-type:application/json");

if (!isset($_SESSION)) {
  session_start();
}

$a2f_token = $_SESSION['2FA_SessionId'];
$ok = false;

if (isset($a2f_token) && isset($_POST['client_data']) && isset($_POST['registration_data'])) { 
  
  $ok = $a2f_client->register_key($a2f_token, $_POST['client_data'], $_POST['registration_data']);    

} 

echo json_encode(array("ok" => $ok));
?>