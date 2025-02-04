<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Longswipe\Payment\LongswipeClient;
use Longswipe\Payment\Exceptions\LongswipeException;

// Initialize the client with sandbox mode
$client = new LongswipeClient('your-api-key-here', true);

// Example voucher details
$voucherCode = 'VOUCHER123';
$voucherPin = '1234';
$amount = 100.00;

// Example 1: Fetch Voucher Details
try {
    echo "Fetching voucher details...\n";
    $voucherDetails = $client->fetchVoucherDetails($voucherCode, $voucherPin, $amount);
    echo "Voucher Details Response:\n";
    print_r($voucherDetails);
} catch (LongswipeException $e) {
    echo "Error fetching voucher details: " . $e->getMessage() . "\n";
    if ($e->getErrorData()) {
        echo "Error Data: " . print_r($e->getErrorData(), true) . "\n";
    }
}

// Example 2: Process Payment
try {
    echo "\nProcessing payment...\n";
    $paymentResult = $client->processVoucherPayment($voucherCode, $voucherPin, $amount);
    echo "Payment Response:\n";
    print_r($paymentResult);
} catch (LongswipeException $e) {
    echo "Error processing payment: " . $e->getMessage() . "\n";
    if ($e->getErrorData()) {
        echo "Error Data: " . print_r($e->getErrorData(), true) . "\n";
    }
}