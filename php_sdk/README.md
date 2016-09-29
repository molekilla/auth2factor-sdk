# php_sdk
## auth2factor PHP Integration SDK

### Requisitos

* sudo apt-get install php5-curl
* sudo apt-get install composer

> Utiliza Firebase JWT para firmado HMAC

### API

Configurar hostname, API key y secret

```php
$HOST = "https://localhost";
$API_KEY = "...";
$API_SECRET = "...";
$a2f_client = new auth2factor($HOST, $API_KEY, $API_SECRET);	 

``` 

### Autenticacion

#### delegate

Retorna un token temporal. Utilizado para solicitar verificacion OTC/U2F.

Returns a temporary login token. Used to request an OTC/U2F verification.

```php
$tokens = $a2f_client->delegate("user@me.com");
$req_token = $tokens["x-app-sign-request"];	 
$u2f_req = $tokens["x-u2f-sign-request"];

```

#### validate_otc

Valida OTC. Retorna un bearer token, de otro modo false.

Verifies OTC. Returns a bearer token, otherwise false.

```php
$sid = $a2f_client->validate_otc("001122", "...temporary token");	 

```

#### validate_u2f

Valida U2F. Retorna un bearer token, de otro modo false. Debe ser llamado una vez el cliente firme exitosamente con u2f.sign.

Verifies U2F. Returns a bearer token, otherwise false. Must be called once succesfully signed with u2f.sign.

```php
$client_data = "eyJ0eXAiO...";
$signature_data = "AQAAADUw...";
$sid = $a2f_client->validate_u2f("...temporary token", $client_data, $signature_data);	 

```

### Registro de llaves

#### request_challenge

Solicita un U2F challenge para iniciar el registro de una llave.

Requests an U2F challenge to initiate key registration.

```php
$challenge = $a2f_client->request_challenge("a valid bearer token"); 

```

#### register_key

Registra una llave U2F. Debe ser llamado una vez u2f.register retorne exitosamente.

Registers an U2F security key. Must be called once u2f.register returns succesfully.

```php
$client_data = "eyJ0eXAiO...";
$registration_data = "AQAAADUw...";
$a2f_client->register_key("a valid bearer token", $client_data, $registration_data);	 

```
