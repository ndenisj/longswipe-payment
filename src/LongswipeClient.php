<?php

namespace Longswipe\Payment;

class LongswipeClient {
    private string $apiKey;
    private bool $isSandbox;
    private string $baseUrl;

    public function __construct(string $apiKey, bool $isSandbox = false) {
        $this->apiKey = $apiKey;
        $this->isSandbox = $isSandbox;
        $this->baseUrl = $isSandbox 
            ? 'https://sandbox.longswipe.com'
            : 'https://api.longswipe.com';
    }

    private function getHeaders(): array {
        return [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    public function fetchVoucherDetails(
        string $voucherCode,
        int $amount,
        string $receivingCurrencyId,
        string $lockPin = "",
        string $walletAddress = ""
    ): array {
        try {
            $payload = json_encode([
                'amount' => $amount,
                'lockPin' => $lockPin,
                'receivingCurrencyId' => $receivingCurrencyId,
                'voucherCode' => $voucherCode,
                'walletAddress' => $walletAddress
            ]);

            $ch = curl_init($this->baseUrl . '/merchant-integrations/fetch-voucher-redemption-charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode !== 200) {
                $errorData = json_decode($response, true);
                throw new LongswipeException(
                    $errorData['message'] ?? 'Unknown error',
                    $statusCode,
                    $errorData
                );
            }

            return json_decode($response, true);
        } catch (LongswipeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new LongswipeException($e->getMessage());
        }
    }

    public function processVoucherPayment(
        string $voucherCode,
        int $amount,
        string $receivingCurrencyId,
        string $lockPin = "",
        string $walletAddress = ""
    ): array {
        try {
            $payload = json_encode([
                'amount' => $amount,
                'lockPin' => $lockPin,
                'receivingCurrencyId' => $receivingCurrencyId,
                'voucherCode' => $voucherCode,
                'walletAddress' => $walletAddress
            ]);

            $ch = curl_init($this->baseUrl . '/merchant-integrations/redeem-voucher');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode !== 200) {
                $errorData = json_decode($response, true);
                throw new LongswipeException(
                    $errorData['message'] ?? 'Unknown error',
                    $statusCode,
                    $errorData
                );
            }

            return json_decode($response, true);
        } catch (LongswipeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new LongswipeException($e->getMessage());
        }
    }
}


class LongswipeClient {
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, bool $isSandbox = false) {
        $this->apiKey = $apiKey;
        $this->baseUrl = $isSandbox 
            ? 'https://sandbox.longswipe.com'
            : 'https://api.longswipe.com';
    }

    /**
     * Required parameters for fetchVoucherDetails:
     * @param array $params [
     *      'voucherCode' => string (required) - The code of the voucher to fetch
     *      'amount' => int (required) - Amount to redeem
     *      'receivingCurrencyId' => string (required) - ID of the currency to receive
     *      'lockPin' => string (optional) - PIN if voucher is locked
     *      'walletAddress' => string (optional) - Wallet address for redemption
     * ]
     * 
     * Response structure:
     * - code: int
     * - data: array
     *   - charges: array
     *     - exchangeRate: int
     *     - fromCurrency: array (currency details)
     *     - isPercentageCharge: bool
     *     - percentageCharge: int
     *     - processingFee: int
     *     - swapAmount: int
     *     - toAmount: int
     *     - toCurrency: array (currency details)
     *   - voucher: array (voucher details)
     * - message: string
     * - status: string
     * 
     * @throws LongswipeException
     */
    public function fetchVoucherDetails(array $params): array {
        return $this->makeRequest(
            'merchant-integrations/fetch-voucher-redemption-charges',
            $params
        );
    }


     /**
     * Required parameters for processVoucherPayment:
     * @param array $params [
     *      'voucherCode' => string (required) - The code of the voucher to redeem
     *      'amount' => int (required) - Amount to redeem
     *      'receivingCurrencyId' => string (required) - ID of the currency to receive
     *      'lockPin' => string (optional) - PIN if voucher is locked
     *      'walletAddress' => string (optional) - Wallet address for redemption
     * ]
     * 
     * Response structure:
     * - code: int
     * - message: string
     * - status: string
     * 
     * @throws LongswipeException
     */
    public function processVoucherPayment(array $params): array {
        return $this->makeRequest(
            'merchant-integrations/redeem-voucher',
            $params
        );
    }

    private function makeRequest(string $endpoint, array $params): array {
        $ch = curl_init("{$this->baseUrl}/$endpoint");
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode !== 200) {
            $errorData = json_decode($response, true);
            throw new LongswipeException(
                $errorData['message'] ?? 'Unknown error',
                $statusCode,
                $errorData
            );
        }

        return json_decode($response, true);
    }
}