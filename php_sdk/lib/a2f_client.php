<?php 
require 'vendor/autoload.php';
require 'config.php';
require_once('a2f_curl.php');  

$a2f_client = new auth2factor($API_HOST, $API_KEY, $API_SECRET);

?>