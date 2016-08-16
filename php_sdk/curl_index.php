<?php 

require_once('lib/a2f_curl.php');  
	 

$action = $_REQUEST["sdk_call"];


$HOST = "";
$API_KEY = "";
$API_SECRET = "";

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
    $requests = $a2f_client->delegate($u);

    echo json_encode($requests);
    return;
} else if ($action == "delegate_u2f") {
    header("Content-type:text/html");

    $u = $_GET["username"];

    if ($u == NULL)  {
    header('X-PHP-Response-Code: 400', true, 400);
    echo json_encode(array("error" => "Missing username"));
    return;
    }

    $requests = $a2f_client->delegate($u);
    echo '<html><body><script src="u2f-api.js"></script>';
    
    echo "<script>" .
    "var response = JSON.parse(" . json_encode($requests["x-u2f-sign-request"])  . ");" .
    "u2f.sign(response, function (data) {" .
    "if (data.errorCode) { console.log(data); return; }" .
    "console.log('clientData: ' + data.clientData);" .
    "console.log('signatureData:' + data.signatureData);" .
    "console.log('Sign key with POST /v2/users/u2f');" .
    " console.log(' Use this temp request token');" .
    " console.log('" . $requests["x-app-sign-request"] . "');" .
    "});" .
    "console.log('Please enter key...');" .    
    "</script></body></html>";
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
    echo '<html><body><script src="u2f-api.js"></script>';
    
    echo "<script>" .
    "var response = " . json_encode($challenge)  . ";" .
    "u2f.register([response], [], function (data) {" .
    "if (data.errorCode) { console.log(data); return; }" .
    "console.log(data);console.log('Register key with POST /v2/security_keys')" .
    "});" .
    "console.log('Please enter key...');" .    
    "</script></body></html>";
    return;
} else if ($action == "register_key") {
    header("Content-type:application/json");
    $ok = $a2f_client->register_key($_POST['bearer_token'], 
    $_POST['client_data'], $_POST['registration_data']);
        echo json_encode(array("ok" => $ok));
    return;    
} else if ($action == "u2f") {
    header("Content-type:application/json");
    $sid = $a2f_client->validate_u2f($_POST['bearer_token'], 
    $_POST['client_data'], $_POST['signature_data']);
        echo json_encode(array("ok" => $sid));
    return;    
}     


?>
