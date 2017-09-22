<?php

require __DIR__ . '/../vendor/autoload.php';

use PrCy\OAuth2\Client\Provider\YandexMoney;

// Replace these with your token settings
// Create a service at https://money.yandex.ru/myservices/new.xml
$clientId     = 'your-client-id';
$clientSecret = 'your-client-secret';

// Change this if you are not using the built-in PHP server
$redirectUri  = 'http://localhost:8080/';

// Start the session
session_start();

// Initialize the provider
$provider = new YandexMoney(compact('clientId', 'clientSecret', 'redirectUri'));

// No HTML for demo, prevents any attempt at XSS
header('Content-Type', 'text/plain');

return $provider;
