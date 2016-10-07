<?php

  // 2FA - delegate
  $resp = $a2f_client->delegate($correo);

  if (isset($resp["status"])) {
    echo $resp["message"];
  } else {
    $_SESSION['A2F_req_tokens'] = $resp;
  }
  
  $has_token = true;

?>