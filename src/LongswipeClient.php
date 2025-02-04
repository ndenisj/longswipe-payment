<?php

namespace Longswipe\Payment;

class LongswipeClient
{
    private $apiKey;
    private $baseUrl;

    public function __construct(string $apiKey, bool $isSandbox = false)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $isSandbox 
            ? 'https://sandbox.longswipe.com' 
            : 'https://api.longswipe.com';
    }

    public function fetchVoucherDetails(string $voucherCode, string $voucherPin, float $amount)
    {
        $endpoint = '/merchant-integrations/fetch-voucher-redemption-charges';
        
        $payload = [
            'voucher_code' => $voucherCode,
            'voucher_pin' => $voucherPin,
            'amount' => $amount
        ];

        return $this->makeRequest('POST', $endpoint, $payload);
    }

    public function processVoucherPayment(string $voucherCode, string $voucherPin, float $amount)
    {
        $endpoint = '/merchant-integrations/redeem-voucher';
        
        $payload = [
            'voucher_code' => $voucherCode,
            'voucher_pin' => $voucherPin,
            'amount' => $amount
        ];

        return $this->makeRequest('POST', $endpoint, $payload);
    }

    private function makeRequest(string $method, string $endpoint, array $payload)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new LongswipeException(curl_error($ch));
        }
        
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        if ($statusCode >= 400) {
            throw new LongswipeException(
                $decodedResponse['message'] ?? 'Unknown error occurred',
                $statusCode
            );
        }

        return $decodedResponse;
    }
}