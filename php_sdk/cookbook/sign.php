<?php 
require_once('auth2factor/a2f_client.php');  
?>
<html>
<head>
<script src="js/a2f.js"></script>
</head>
<?php
  if (isset($_GET['verify_key'])) {
    $has_token = true;
    $has_u2f_token = true;
  }
     

  // 2FA - delegate
  $resp = $a2f_client->delegate($correo);

  if (isset($resp["status"])) {
    echo $resp["message"];
  } else {
    $_SESSION['A2F_req_tokens'] = $resp;
  }
  
  $has_token = true;
  $has_u2f_token = isset($_SESSION['A2F_req_tokens']['x-u2f-sign-request']);

  if ($has_u2f_token) {

    echo '<a class="verify-link" href="login.php?verify_key=true">Haga clic para verificar llave</a>';
?>
    <script>
    <?php
    // U2F sign
    echo "var response = JSON.parse(" . json_encode($_SESSION['A2F_req_tokens']['x-u2f-sign-request']) . ");";
    ?>    

    u2f.sign(response, function (data) {
      if (data.errorCode) { 
        console.log(u2fUtils.getErrorDescription(data.errorCode)); 
        return; 
      }
    
      console.log(data);
      axios.post('/auth2factor/sign_key.php',  
          'client_data=' + data.clientData + '&' +
          'signature_data=' + data.signatureData
     )
        .then(function (response) {
          console.log(response);
          if (response.data.ok) {
            window.location.href = 'index.php';
          }
        })
        .catch(function (error) {
          console.log(error);
        });      
    });
    console.log('Please enter key...');

    document.getElementsByClassName("verify-link")[0].innerHTML = "";
    </script>

    
<?php
  }
   ?>
</html>