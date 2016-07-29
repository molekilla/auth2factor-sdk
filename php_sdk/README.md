# php_sdk
## auth2factor PHP Integration SDK

### Requirements

* sudo apt-get install php5-curl
* sudo apt-get install composer

### API

Set hostname, API key and secret

```php
$HOST = "https://localhost";
$API_KEY = "...";
$API_SECRET = "...";
$a2f_client = new auth2factor($HOST, $API_KEY, $API_SECRET);	 

``` 

#### delegate

Returns a temporary login token. Used to request an OTC/U2F verification.

```php
$token = $a2f_client->delegate("user@me.com");	 

```

#### verify_otc

Verifies OTC. Returns a bearer token, otherwise false.

```php
$sid = $a2f_client->validate_otc("001122", "...temporary token");	 

```

