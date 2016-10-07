<?php 
require_once('auth2factor/a2f_client.php');  
?>
<html>
<head>
<script src="js/u2f-api.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/axios-config.js"></script>
<script src="js/u2f-utils.js"></script>
</head>


<div class="init-enroll">
<h3></h3>
</div>  
<?php 
  if (isset($_SESSION['2FA_SessionId']) && $_SESSION['2FA_SessionId'] != false) {

  $a2f_token = $_SESSION['2FA_SessionId'];
  echo '<a class="enroll-link" href="/?enroll_key=true">Haga clic para enrolar llave</a>';


  if (isset($a2f_token) && isset($_GET['enroll_key'])) {
    ?>
    <script>
    <?php 
    // U2F enroll
    $challenge = $a2f_client->request_challenge($a2f_token);
    echo "var response =" . json_encode($challenge);
 ?>    
    var el = document.getElementsByClassName("init-enroll");


    u2f.register([response], [], function (data) {
      if (data.errorCode) { 
        console.log(u2fUtils.getErrorDescription(data.errorCode)); 
        return; 
      }
    
      console.log(data);
      console.log('Register key with POST /v2/security_keys');
      axios.post('/auth2factor/register_key.php',  
          'client_data=' + data.clientData + '&' +
          'registration_data=' + data.registrationData
      )
        .then(function (response) {
          console.log(response);
          if (response.data.ok) {
            el[0].innerHTML = 'Llave registrada exitosamente.'
          }
        })
        .catch(function (error) {
          console.log(error);
        });      
    });
    console.log('Please enter key...');

    el[0].innerHTML = 'Ingrese llave de seguridad..';
    document.getElementsByClassName("enroll-link")[0].innerHTML = "";
    </script>

    
<?php
 }
  }
   ?>

</html>