<?php
require '../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

function getGoogleAuthToken()
{
    $body_request = array(
        'grant_type'    => 'authorization_code',
        'code'          => $_SESSION['code'],
        'client_secret' => $_ENV['CLIENT_SECRET'],
        'client_id'     => $_ENV['CLIENT_ID'],
        'redirect_uri'  => $_ENV['REDIRECT_URL']
    );

    $headers = array('Content-Type: application/json');
    $url = 'https://accounts.google.com/o/oauth2/token';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true); // Set request method to POST (default)
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body_request)); // Set request body
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set headers
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Request to return the response
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
    } else {
        $decoded_response = json_decode($response, true);
        if ($decoded_response) {
            // prevent undefined warning for $decoded_response['access_token']
            if (isset($decoded_response['access_token'])) {
                // Save token to session
                $_SESSION['accessToken'] = $decoded_response['access_token'];
            }
        } else {
            echo 'Failed to decode response as JSON.';
        }
    }
    curl_close($curl);
}

function fetchGoogleUserData()
{
    $url = 'https://www.googleapis.com/oauth2/v1/userinfo';
    $headers = array(
        'Authorization: Bearer ' . $_SESSION['accessToken'],
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Request to return the response
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set headers
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
    } else {
        $decoded_response = json_decode($response, true);
        if ($decoded_response) {
            // Save user to session
            $_SESSION['user'] = $decoded_response;
        } else {
            echo 'Failed to decode response as JSON.';
        }
    }

    curl_close($curl);
}

getGoogleAuthToken();
fetchGoogleUserData();
