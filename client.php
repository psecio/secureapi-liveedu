<?php

// A test client to make testing API requests easier
require_once 'vendor/autoload.php';


echo "\n### MAKING REQUEST ###################\n";

$baseUrl = 'http://localhost:8111';

// Going to refactor to not user username anymore
// It will use an API key instead
// $username = 'user4';
$apiKey = 'e839e9ed062ee41a75aa90ccc629109389fde357bf9c743675b54fe9bc8574348b9370887f1e1aa9d940a29871f52fbd9e735e5b071e65e56f0160098d3812c3';
$password = 'test1234';
//
$client = new GuzzleHttp\Client();
$res = $client->request('POST', $baseUrl.'/user/login', [
    'form_params' => [
        'key' => $apiKey,
        'password' => $password
    ]
]);

$body = (string)$res->getBody();
print_r($body);

$json = json_decode($body);
print_r($json);

die();

// Now we make another request, using this session value to do two things:
// 1. Send it as a token in a header of the request (X-Token)
// 2. Use it to make a HMAC of the message (X-Token-Hash)

$body = '';
$sessionId = $json->data->session;

// Don't log in so we can test the session expiration
// $sessionId = '9852bb5de151d732c85ef8e730ad8f8f5b6ae5516a7244a0d317a0835a6e812314f6be7cb7238efe9f9d399a56913384a15faf2dbc7ceaef9e0b898dd21b41b9';

// Time is added to the hash key to produce more randomized keys and prevent replay attacks
$hash = hash_hmac('sha512', $body, $sessionId.time());

$res = $client->request('GET', $baseUrl.'/test', [
    'headers' => [
        'X-Token' => $apiKey,
        'X-Token-Hash' => $hash
    ],
    'body' => $body
]);
print_r((string)$res->getBody());



echo "\n\n";
