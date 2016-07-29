<?php 

require_once('lib/a2f_curl.php');  
	 

$action = $_POST["auth_type"];

header("Content-type:application/json");

$HOST = "https://localhost";
$API_KEY = "...";
$API_SECRET = "...";
$a2f_client = new auth2factor($HOST, $API_KEY, $API_SECRET);	 

if ($action == "login") {
    $u = $_POST["username"];
    $p = $_POST["password"];

    if ($u == NULL)  {
    header('X-PHP-Response-Code: 400', true, 400);
    echo json_encode(array("error" => "Missing username"));
    return;
    }

    if ($p == NULL)  {
    header('X-PHP-Response-Code: 400', true, 400);
    echo json_encode(array("error" => "Missing password"));
    return;
    }

    // 2FA - 1st leg
    $token = $a2f_client->delegate($u);

    echo json_encode(array("token" => $token));
    return;
} else if ($action == "otc") {
    // 2FA - OTC
    $sid = $a2f_client->validate_otc($_POST['otc'], $_POST['bearer_token']);
    echo json_encode(array("sid" => $sid));
    return;    
} else if ($action == "u2f") {
    // 2FA - U2F
    $sid = $a2f_client->validate_security_key($_POST['client_data'], $_POST['signature_data'], $_SESSION['bearer_token']);
    echo json_encode(array("sid" => $sid));
    return;
}
     


?>
