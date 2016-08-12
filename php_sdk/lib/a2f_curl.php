<?php
require 'vendor/autoload.php';

/**
 * auth2factor
 */


use \Firebase\JWT\JWT;

class auth2factor {
    private $host = "";
    private $apiKey = "";
    private $apiSecret = "";
    
    public function __construct($host, $apiKey, $apiSecret) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->host = $host;
    }

    /**
     * Obtener el token_2fa del usuario
     * @return <string>
     */
    public function getToken(){
        return $_SESSION["user"]["token_2fa"];
    }

    private function get_bearer_token($account) {
        $hmac = $this->get_hmac($account);
        return 'Bearer ' . $this->apiKey . ':' . $hmac;
    }

    private function get_hmac($account) {
        $key = $this->apiSecret;
        $payload = array(
            "accountRequester" => $account,
            "email" => $account,
            "apiUniqueId" => $this->apiKey,
            "created" => date("DATE_W3C")
        );

        $jwt = JWT::encode($payload, $key);
         
        return $jwt;
    }

    public function register_key($bearer, $client_data, $registration_data){
        $acct = $email;

        $data = array(
            "clientData" => $client_data,
            "registrationData" => $registration_data
        );
        $data_string = json_encode($data);

        $API_HOST = $this->host;
        $ch = curl_init($API_HOST . "/api/v2/security_keys");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $bearer,
            'Content-Length: ' . strlen($data_string))
        );
        $output = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        list($header, $body) = explode("\r\n\r\n", $output, 2);
        $header=explode("\r\n", $header);
        array_shift($header);    //get rid of "HTTP/1.1 200 OK"
        $resp_headers=array();
        foreach ($header as $k=>$v)
        {
            $v=explode(': ', $v, 2);
            $resp_headers[$v[0]]=$v[1];
        }

        curl_close($ch);

        return true;
    }

    /**
     * Requests an U2F challenge
     * @return <string>
     */
    public function request_challenge($bearer_token) {


        $API_HOST = $this->host;
        $ch = curl_init($API_HOST . "/api/v2/security_keys/challenge");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $bearer_token,
            'Content-Length: ' . strlen(""))
        );
        $output = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        list($header, $body) = explode("\r\n\r\n", $output, 2);
        $header=explode("\r\n", $header);
        array_shift($header);    //get rid of "HTTP/1.1 200 OK"
        $resp_headers=array();
        foreach ($header as $k=>$v)
        {
            $v=explode(': ', $v, 2);
            $resp_headers[$v[0]]=$v[1];
        }
        curl_close($ch);
        
        return array(
            "version" => $resp_headers["x-app-u2f-version"],
            "appId" => $resp_headers["x-app-u2f-appid"],
            "challenge" => $resp_headers["x-app-u2f-challenge"] 
        );

    }

    /**
     * Autenticacion de OTC
     * @return <bool>
     */
    public function validate_otc($code, $request_token) {

        $data = array("code" => $code);
        $data_string = json_encode($data);


        $API_HOST = $this->host;
        $ch = curl_init($API_HOST . "/api/v2/users/otc");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $request_token,
            'Content-Length: ' . strlen($data_string))
        );
        $output = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        list($header, $body) = explode("\r\n\r\n", $output, 2);
        $header=explode("\r\n", $header);
        array_shift($header);    //get rid of "HTTP/1.1 200 OK"
        $resp_headers=array();
        foreach ($header as $k=>$v)
        {
            $v=explode(': ', $v, 2);
            $resp_headers[$v[0]]=$v[1];
        }

        $auth_token = null;
        if (isset($resp_headers["x-app-bearer"])) {
            $auth_token = $resp_headers["x-app-bearer"];
        }

        if ($auth_token == null) {
            return false;
        } else {
            return $auth_token;
        }

        curl_close($ch);

    }

    /**
     * Autenticacion de U2F
     * @return <bool>
     */
    public function validate_u2f($bearer, $client_data, $signature_data) {

        $data = array(
            "clientData" => $client_data,
            "signatureData" => $signature_data
        );
        $data_string = json_encode($data);


        $API_HOST = $this->host;
        $ch = curl_init($API_HOST . "/api/v2/users/u2f");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $bearer,
            'Content-Length: ' . strlen($data_string))
        );
        
        $resp = $this->get_response($ch);
        
        $auth_token = null;
        if (isset($resp["headers"]["x-app-bearer"])) {
            $auth_token = $resp["headers"]["x-app-bearer"];
        }

        if ($auth_token == null) {
            return false;
        } else {
            return $auth_token;
        }

        curl_close($ch);

    }

    private function get_response($ch) {
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $err  = curl_error($ch);
        list($header, $body) = explode("\r\n\r\n", $output, 2);
        $header=explode("\r\n", $header);
        array_shift($header);    //get rid of "HTTP/1.1 200 OK"
        $resp_headers=array();
        foreach ($header as $k=>$v)
        {
            $v=explode(': ', $v, 2);
            $resp_headers[$v[0]]=$v[1];
        }

        return array(
            "status" => $info["http_code"],
            "headers" => $resp_headers,
        );
    }    
    /**
     * Delega autenticacion
     * @return <string>
     */
    public function delegate($email){
        $acct = $email;
        $bearer = $this->get_bearer_token($acct);
        $data = array("account" => $acct);
        $data_string = json_encode($data);

        $API_HOST = $this->host;
        $ch = curl_init($API_HOST . "/api/v2/users/delegate");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $bearer,
            'Content-Length: ' . strlen($data_string))
        );
        $resp = $this->get_response($ch);
        curl_close($ch);

        return array(
            "x-u2f-sign-request" => $resp["headers"]["x-u2f-sign-request"],
            "x-app-sign-request" => $resp["headers"]["x-app-sign-request"]
        );
    }


}

?>
