<?php
  // 2FA - otc
  $resp = $a2f_client->validate_otc($_POST['otc'], $_SESSION['A2F_req_tokens']['x-app-sign-request']);

  if (isset($resp["status"])) {
    if ($resp["status"] == 401) {
        $_SESSION['2FA_SessionId'] = false;  
    } else {
        echo $resp["message"];
        return;
    }
  } else {
    $_SESSION['2FA_SessionId'] = $resp;
  }
?>