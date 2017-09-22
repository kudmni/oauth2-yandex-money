<?php

$provider = require __DIR__ . '/provider.php';

if (!empty($_SESSION['token'])) {
    $token = unserialize($_SESSION['token']);
}

if (empty($token)) {
    header('Location: /');
    exit;
}

try {

    // We got an access token, let's now get the owner details
    $ownerDetails = $provider->getResourceOwner($token);

    // Use these details
    printf('Your account is %s', $ownerDetails->getAccount());

} catch (Exception $e) {

    // Failed to get account details
    exit('Something went wrong: ' . $e->getMessage());

}

// Use this to interact with an API on the account behalf
echo $token->accessToken;

// Use this to get a new access token if the old one expires
echo $token->refreshToken;

// Number of seconds until the access token will expire, and need refreshing
echo $token->expires;
