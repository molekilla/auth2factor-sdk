<?php 

require_once('lib/a2f_curl.php');  
	 

$action = $_REQUEST["sdk_call"];


$a2f_client = new auth2factor($HOST, $API_KEY, $API_SECRET);	 

if ($action == "delegate") {
header("Content-type:application/json");
    
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
header("Content-type:application/json");
    
    // 2FA - OTC
    $sid = $a2f_client->validate_otc($_POST['otc'], $_POST['bearer_token']);
    echo json_encode(array("sid" => $sid));
    return;    
} else if ($action == "request_key_challenge") {
    header("Content-type:text/html");

    // U2F enroll
    $challenge = $a2f_client->request_challenge($_GET['bearer_token']);
    echo '<script src="u2f-api.js"></script>';
    
    echo "<script>" .
    "var response = " . $challenge  . ";" .
    "U2F.register([response], [], function (data) {" .
    "if (data.errorCode) { console.log(data); return; }" .
    "console.log(data);" .
    "});</script>";
    return;
} else if ($action == "register_key") {
    header("Content-type:application/json");
    $ok = $a2f_client->register_key($_POST['bearer_token'], $_POST['client_data'], $_POST['registration_data']);
        echo json_encode(array("ok" => $ok));
    return;    
}
     


?>
