<?php

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('..');
$dotenv->load();

use League\OAuth2\Client\Provider\GenericProvider;

$provider = new GenericProvider([
    'clientId'                => $_ENV['CLIENT_ID'],
    'clientSecret'            => $_ENV['CLIENT_SECRET'],
    'redirectUri'             => $_ENV['REDIRECT_URL'],
    'urlAuthorize'            => $_ENV['URL_AUTHORIZE'],
    'urlAccessToken'          => $_ENV['URL_ACCESS_TOKEN'],
    'urlResourceOwnerDetails' => $_ENV['URL_RESOURCE_OWNER_DETAIL']
]);
