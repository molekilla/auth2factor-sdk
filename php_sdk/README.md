# php_sdk
## v1.2.0
## auth2factor PHP Integration SDK

### Requisitos

* sudo apt-get install php5-curl
* sudo apt-get install composer

> Utiliza Firebase JWT para firmado HMAC. Si no usas Composer, copiar las librerias de Firebase JWT a tu solución.

### API

Configurar hostname, API key y secret

```php
$HOST = "https://localhost";
$API_KEY = "...";
$API_SECRET = "...";
$a2f_client = new auth2factor($HOST, $API_KEY, $API_SECRET);	 

``` 

### Autenticación

#### delegate

Retorna un token temporal. Utilizado para solicitar verificación OTC/U2F.

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
$sid = $a2f_client->validate_otc("...temporary token", "001122");	 

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

## Implementación U2F


### FIDO U2F - Enrolamiento

Una vez autenticado, el usuario ingresa a Configuración de Cuenta de la solución y le ofrece al usuario
enrolar llave.

#### [cookbook/register.php](cookbook/register.php)

* Obtiene un U2F **challenge**: API request_challenge
* Llama a libreria cliente `u2f.register` con el challenge y solicita firmar
* Se procede a ingresar la llave
* Se almacena la confirmación exitosa en `register_key.php`: API register_key


### FIDO U2F - Autenticación

Si el usuario tiene llaves registradas en el dominio donde se autentico en el 1er paso.

#### [cookbook/sign.php](cookbook/sign.php)

* Obtiene un conjunto de **sign requests** 
* Llama a libreria cliente `u2f.sign` con los sign requests y solicita firmar
* Se procede a ingresar la llave
* Se valida en `sign_key.php` y obtiene un bearer token: API validate_u2f



## Libreria Javascript para U2F

Incluir libreria minificada

```html
<head>
<script src="js/a2f.js"></script>
</head>
```

Contiene

* Axios para AJAX / REST `axios.min.js`
* Axios config `axios-config.js`
* U2F `u2f-api.js`
* U2F utils `u2f-utils.js`