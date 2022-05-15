<?php

require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

// For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = false;
// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
    'client_id' => 'Abj1URvG9kpqEro9KL1Zyj5PIzfJFLhNumftEgqbiMlHFI0QB3GtlLmfXh7tPQkYtGTUtcRbvnfok3V4',
    'client_secret' => 'EGz73G-h33GCg_AFVQrkoUw6NJXAU-3j7KisMDbFW-baf6lJoOU0XVevAIWTT5kNoSV8piKhAMX28FAz',
    'return_url' => 'https://www.larose.bg/bg/shopping-cart/paypal-response',
    'cancel_url' => 'https://www.larose.bg/bg/shopping-cart/shopping-cart-checkout-paypal-failure'
];

// Database settings. Change these for your database configuration.
$dbConfig = [
    'host' => 'localhost',
    'username' => 'larovrcf_larose',
    'password' => 'XLgy$c$@EWmB',
    'name' => 'larovrcf_larose'
];

$apiContext = getApiContext($paypalConfig['client_id'], $paypalConfig['client_secret'], $enableSandbox);

/**
 * Set up a connection to the API
 *
 * @param string $clientId
 * @param string $clientSecret
 * @param bool   $enableSandbox Sandbox mode toggle, true for test payments
 * @return \PayPal\Rest\ApiContext
 */
function getApiContext($clientId, $clientSecret, $enableSandbox = false)
{
    $apiContext = new ApiContext(
        new OAuthTokenCredential($clientId, $clientSecret)
    );
    $apiContext->setConfig([
        'mode' => $enableSandbox ? 'sandbox' : 'live'
//        'log.FileName' => '../some-paypal-log-file.log',
//        'log.LogLevel' => $enableSandbox ? 'DEBUG' : 'INFO'
    ]);
    return $apiContext;
}
