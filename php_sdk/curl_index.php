<?php 

require_once('lib/a2f_curl.php');  
	 
$u = $_POST["username"];
$p = $_POST["password"];
$action = $_POST["auth_type"];

header("Content-type:application/json");

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

$HOST = "https://localhost";
$a2f_client = new auth2factor($HOST);	 

if ($action == "login") {
    // 2FA - 1st leg
    $token = $a2f_client->delegate($u);

    echo json_encode(array("token" => $token));
    return;
} else if ($action == "otc") {
    // 2FA - OTC
    $sid = $a2f_client->validateOtc($_POST['otc'], $_POST['bearer_token']);
    echo json_encode(array("sid" => $sid));
    return;    
} else if ($action == "u2f") {
    // 2FA - U2F
    $sid = $a2f_client->validateSecurityKey($_POST['client_data'], $_POST['signature_data'], $_SESSION['bearer_token']);
    echo json_encode(array("sid" => $sid));
    return;
}
     


?>
