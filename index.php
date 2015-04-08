<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Stream\BufferStream;
use GuzzleHttp\Exception\RequestException;

$key = "YOUR KEY";                                  // Please use your own api key
$secret = "YOUR SECRET";                            // and secret.
$route = "/merchantV1/GetCurrencyExchangeRates";    // The api-method path
$method = "POST";                                   // The request method for your api-method.
$content = json_encode ( [                          // The content of your request. (a.k.a. the body-part for your requested api-method)
        "currency" => "USD",
        "amount" => 2
] );

$url = "https://api-demo.coinsnap.eu";              // Please use the base url for your enviroment (LIVE/DEMO)

// Generate a new nonce for every request to minimize the possibility of brute force attacks.
$nonce = rand ( 1, 999999999 );

// Calculate the hash for this request
$sign = hash_hmac ( 'sha512', $route . hash ( 'sha256', $nonce . $content, false ), $secret, false );

// Use Guzzle for easy request abstraction
$client = new Client ();

// Create and configure the request
$request = $client->createRequest ( $method, $url . $route, [
        'verify' => false
] );

// Set the header informations
$request->setHeader ( "X-Key", $key );
$request->setHeader ( "nonce", $nonce );
$request->setHeader ( "X-Sign", $sign );

// Write the body of the request
$stream = new BufferStream ();
$stream->write ( $content );
$request->setBody ( $stream );

// Get possible error responses
$response = null;
try {
    $response = $client->send ( $request );
} catch ( RequestException $e ) {
    var_dump ( $e->getMessage () );
    $response = $e->getResponse ();
}
echo "<pre>";
echo $response->getBody ()->getContents ();
echo "</pre>";