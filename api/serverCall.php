<?php
/**
 *                       ######
 *                       ######
 * ############    ####( ######  #####. ######  ############   ############
 * #############  #####( ######  #####. ######  #############  #############
 *        ######  #####( ######  #####. ######  #####  ######  #####  ######
 * ###### ######  #####( ######  #####. ######  #####  #####   #####  ######
 * ###### ######  #####( ######  #####. ######  #####          #####  ######
 * #############  #############  #############  #############  #####  ######
 *  ############   ############  #############   ############  #####  ######
 *                                      ######
 *                               #############
 *                               ############
 *
 * Adyen Checkout Example (https://www.adyen.com/)
 *
 * Copyright (c) 2017 Adyen BV (https://www.adyen.com/)
 *
 */

header('Content-Type: application/json');

include('../config/timezone.php');

global $jsSetupResponse;

if (!empty (getenv('MERCHANT_ACCOUNT')) && !empty(getenv('CHECKOUT_API_KEY'))) {
    $authentication['merchantAccount'] = getenv('MERCHANT_ACCOUNT');
    $authentication['checkoutAPIkey'] = getenv('CHECKOUT_API_KEY');
} else {
    $authentication = parse_ini_file('../config/authentication.ini', true);
}

$order = include('../payment/order.php');
$server = include('../config/server.php');

/** Set up the cURL call to  adyen */
function requestPaymentData($order, $server, $authentication)
{
 $request = array(
        'amount' => array(
             'value' => '10',
             'currency' => 'EUR'
         ),
         // 'apiVersion' => '1234',
        'sdkVersion' => $sdkVersion,
        'countryCode' => 'NL',
        'shopperLocale' => 'NL',
        'merchantAccount' => $merchantAccount,
        'returnUrl' => 'http://localhost:3000/paymentSuccess.php',
        'reference' => 'Checkout php ' . $_ENV['LOGNAME'] .' '.date('YmdHi'),
        'sessionValidity' => date('Y-m-d\TH:i:s\Z', strtotime('+2 days')),
        'channel' => 'Web',
        'shopperReference' => 'misterman',
        'shopperIP' => $_SERVER['REMOTE_ADDR'],
        'shopperEmail' => 'youremail@email.com',
        // 'allowedPaymentMethods' => array ("scheme"),
        // 'blockedPaymentMethods' => array ("scheme"),
        'shopperName' => array(
            'firstName' => 'Testperson-se',
            'lastName' => 'Approved',
            // 'gender' => 'MALE'
        ),
        'dateOfBirth' => '1985-04-03',
        'billingAddress' => array(
            'city' => 'Ankeborg',
            'country' => 'SE',
            'houseNumberOrName' => '1',
            'postalCode' => '12345',
            'street' => 'Stårgatan'
        ),
        'socialSecurityNumber' => '410321-9202',
        'lineItems' => array(
            array(
                'id' => '1',
                'description' => 'Test Item 1',
                'amountExcludingTax' => 10000,
                'amountIncludingTax' => 11800,
                'taxAmount' => 1800,
                'taxPercentage' => 1800,
                'quantity' => 1,
                'taxCategory' => 'High'
            ),
            array(
                'id' => '2',
                'description' => 'Test Item 2',
                'amountExcludingTax' => 100000,
                'amountIncludingTax' => 103000,
                'taxAmount' => 3000,
                'taxPercentage' => 300,
                'quantity' => 5,
                'taxCategory' => 'Low'
            ),
            array(
                'id' => '3',
                'description' => 'Test Item 3',
                'amountExcludingTax' => 1000,
                'amountIncludingTax' => 1000,
                'taxAmount' => 0,
                'taxPercentage' => 0,
                'quantity' => 1,
                'taxCategory' => 'Zero'
            )
        ),
        'telephoneNumber' => '0765260000',
        // 'html' => 'true',
        // 'token' => 'eyJhcGlWZXJzaW9uIjoiMSJ9',// apiVersion 1 // Comment out to get default: apiVersion 2 (separate fields in inputDetails) or have value: 'eyJhcGlWZXJzaW9uIjoiMiJ9'
    );

    $setupString = json_encode($request);

    //  Initiate curl
    $curlAPICall = curl_init();

    // Set to POST
    curl_setopt($curlAPICall, CURLOPT_CUSTOMREQUEST, "POST");

    // Add JSON message
    curl_setopt($curlAPICall, CURLOPT_POSTFIELDS, $setupString);

    // Will return the response, if false it print the response
    curl_setopt($curlAPICall, CURLOPT_RETURNTRANSFER, true);

    // Set the url
    curl_setopt($curlAPICall, CURLOPT_URL, $server['setupURL']);

    // Api key
    curl_setopt($curlAPICall, CURLOPT_HTTPHEADER,
        array(
            "X-Api-Key: " . $authentication['checkoutAPIkey'],
            "Content-Type: application/json",
            "Content-Length: " . strlen($setupString)
        )
    );

    // Execute
    $result = curl_exec($curlAPICall);

    // Closing
    curl_close($curlAPICall);

    // When this file gets called by javascript or another language, it will respond with a json object
    echo $result;
}

requestPaymentData($order, $server, $authentication);
